<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

/*
|--------------------------------------------------------------------------
| for company_popup
|--------------------------------------------------------------------------
|
*/
if($action == "company_popup")
{
	echo load_html_head_contents("Company Info","../../../../", 1, 1, '','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array();
		var selected_name = new Array();

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'table_body' ).rows.length;
			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ )
			{
				js_set_value( i );
			}
		}

		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style )
			{
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function set_all()
		{
			var old=document.getElementById('selected_ids').value;
			if(old!="")
			{
				old=old.split(",");
				for(var k=0; k<old.length; k++)
				{
					js_set_value( old[k] )
				}
			}
		}

		function js_set_value( str )
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 )
			{
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );

			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == $('#txt_individual_id' + str).val() )
						break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}

			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#selected_id').val(id);
			$('#selected_name').val(name);
		}
	</script>
</head>
<!--<fieldset style="width:320px">
	<legend>Item Details</legend>-->
	<input type="hidden" name="selected_name" id="selected_name" value="">
	<input type="hidden" name="selected_id" id="selected_id" value="">
	<table width="320" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
		<thead>
			<tr>
				<th width="40">SL</th>
                <th width="">Company Name</th>
			</tr>
		</thead>
	</table>
	<div style="width:320px; overflow-y:scroll; max-height:300px" id="scroll_body">
		<table width="300" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
			<?
			$i = 1;
			$cbo_company_name=str_replace("'","",$cbo_company_name);
			
			$sql="SELECT ID, COMPANY_NAME FROM LIB_COMPANY WHERE STATUS_ACTIVE=1 AND IS_DELETED=0 ORDER BY COMPANY_NAME";
			//echo $sql;
			$result=sql_select($sql);
			$selected_id_arr=explode(",",$cbo_company);
			foreach ($result as $row)
			{
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

				if(in_array($row[csf("id")],$selected_id_arr))
				{
					if($selected_ids=="") $selected_ids=$i; else $selected_ids.=",".$i;
				}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
					<td width="40" align="center">
						<? echo $i; ?>
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i ?>" value="<? echo $row['ID']; ?>"/>
						<input type="hidden" name="txt_individual" id="txt_individual<? echo $i ?>" value="<? echo $row['COMPANY_NAME']; ?>"/>



					</td>
                    <td width=""><p><? echo $row['COMPANY_NAME']; ?></p></td>
				</tr>
				<?
				$i++;
			}
			?>
			<input type="hidden" name="selected_ids" id="selected_ids" value="<? echo $selected_ids; ?>"/>
		</table>
	</div>
	<table width="320" cellspacing="0" cellpadding="0" style="border:none" align="center">
		<tr>
			<td align="center" height="30" valign="bottom">
				<div style="width:100%">
					<div style="width:50%; float:left" align="left">
						<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
					</div>
					<div style="width:50%; float:left" align="left">
						<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
					</div>
				</div>
			</td>
		</tr>
	</table>
<!--</fieldset>-->
<script type="text/javascript">
	setFilterGrid('table_body',-1);
	set_all();
</script>
<?
exit();
}

/*
|--------------------------------------------------------------------------
| for buyer dropdown
|--------------------------------------------------------------------------
|
*/
if($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);
	if($data[1]==1) $party="1,3,21,90"; else $party="80";
	echo create_drop_down( "cbo_buyer_id", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in (".$data[0].") $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (".$party.")) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" );
	exit();
}

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
//$buyer_array=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		
		var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
		
		function toggle( x, origColor ) 
		{
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
			}
		}
		
		function js_set_value(id)
		{
			var str=id.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
			var strdt=str[2];
			str=str[1];
			
			if( jQuery.inArray(  str , selected_id ) == -1 ) {
				selected_id.push( str );
				selected_name.push( strdt );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str  ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i,1 );
			}
			var id = '';
			var ddd='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				ddd += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			ddd = ddd.substr( 0, ddd.length - 1 );
			$('#hide_job_id').val( id );
			$('#hide_job_no').val( ddd );
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
                                <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                                <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                            </th> 					
                        </thead>
                        <tbody>
                            <tr>
                                <td align="center">
                                    <?
                                    echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in (".$companyID.") $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
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
                                    <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'fb_wise_yarn_ih_rpt_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                                </td>
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

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$month_id=$data[5];
	//echo $month_id;

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
	
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	
	if($db_type==0)
	{
		if($year_id!=0) $year_search_cond=" and year(insert_date)=$year_id"; else $year_search_cond="";
		$year_cond= "year(insert_date)as year";
	}
	else if($db_type==2)
	{
		if($year_id!=0) $year_search_cond=" and TO_CHAR(insert_date,'YYYY')=$year_id"; else $year_search_cond="";
		$year_cond= "TO_CHAR(insert_date,'YYYY') as year";
	}
	
	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_cond from wo_po_details_master where status_active=1 and is_deleted=0 and company_name in (".$company_id.") and $search_field like '$search_string' $buyer_id_cond $year_search_cond $month_cond order by job_no DESC";
	
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,80,80,60","620","270",0, $sql , "js_set_value", "id,job_no", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','',1) ;
	exit(); 
}

if($action=="booking_no_popup")
{
	echo load_html_head_contents("Booking Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	
	<script>
	
		var selected_id = new Array; var selected_name = new Array;
		function check_all_datas()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_div' ).rows.length;
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
		
		function js_set_value2( str ) {
			
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
			
			
			$('#hide_job_id').val( id );
			$('#hide_job_no').val( name );
			//$('#hide_recv_id').val( rec_id );
			//$("#hide_booing_type").val(str[3]);
		}
	</script>
    </head>
    <body>
        <div align="center">
            <form name="styleRef_form" id="styleRef_form">
                <fieldset style="width:680px;">
                    <table width="670" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                        <thead>
                            <th>Buyer</th>
                            <th>Search By</th>
                            <th id="search_by_td_up" width="170">Please Enter Booking No</th>
                            <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
                            <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                            <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                        </thead>
                        <tbody>
                            <tr>
                                <td align="center">
                                     <? 
                                        echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in (".$companyID.") $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
                                    ?>
                                </td>   
                                              
                                <td align="center">	
                                <?
                                    $search_by_arr=array(1=>"Booking No",2=>"Job No",3=>"Style Ref");
                                    $dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
                                    echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "1",$dd,0 );
                                ?>
                                </td>     
                                <td align="center" id="search_by_td">				
                                    <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                                </td> 	
                                <td align="center">
                                    <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>', 'create_booking_no_search_list_view', 'search_div', 'fb_wise_yarn_ih_rpt_controller', 'setFilterGrid(\'tbl_list_div\',-1)');" style="width:100px;" />
                            </td>
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

if($action=="create_booking_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{ 
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
				$buyer_id_cond2=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else 
			{ 
				$buyer_id_cond="";$buyer_id_cond2="";
			}
		}
		else
		{
			$buyer_id_cond="";$buyer_id_cond2="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$data[1]";
		$buyer_id_cond2=" and a.buyer_id=$data[1]";
	}
	
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	if($search_by==3) 
	{
		 $search_field="a.style_ref_no";
	}
	else if($search_by==2)
	{
		 $search_field="a.job_no_prefix_num";
	}
	else $search_field="b.booking_no";
	//$year="year(insert_date)";
	if($db_type==0) $year_field_by="and YEAR(a.insert_date)"; 
	else if($db_type==2) $year_field_by=" and to_char(a.insert_date,'YYYY')";
	else $year_field_by="";
	if($db_type==0) $month_field_by="and month(a.insert_date)"; 
	else if($db_type==2) $month_field_by=" and to_char(a.insert_date,'MM')";
	else $month_field_by="";
	if($db_type==0) $year_field=" YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="  to_char(a.insert_date,'YYYY') as year";
	else $year_field="";

	if($year_id!=0) $year_cond=" $year_field_by=$year_id"; else $year_cond="";
	
	$sql= "select a.job_no,a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,b.booking_no,c.booking_no_prefix_num,c.id as booking_id  
	from wo_po_details_master a,wo_booking_dtls b ,wo_booking_mst c where a.job_no=b.job_no and b.booking_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and b.booking_type in(1,4) and c.ITEM_CATEGORY = 2 and a.company_name in (".$company_id.") and $search_field like '$search_string' $buyer_id_cond $year_cond group by a.job_no,b.booking_no,a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,c.id,c.booking_no_prefix_num  order by a.job_no desc";
	
	//echo $sql;//die;	
	$sqlResult=sql_select($sql);
	?>
    <div align="center">
    <fieldset style="width:650px;margin-left:10px">
        <form name="searchprocessfrm_1" id="searchprocessfrm_1" autocomplete="off">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="670" class="rpt_table">
                <thead>
                    <th width="30">SL</th>
                    <th width="130">Company</th> 
                    <th width="110">Buyer</th>
                    <th width="110">Job No</th>
                    <th width="120">Style Ref.</th>
                    <th width="">Booking No</th>
                </thead>
            </table>
            <div style="width:670px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table" id="tbl_list_div">
            <?
            $i=1;
            foreach($sqlResult as $row )
            {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
				
				$data=$i.'_'.$row[csf('booking_id')].'_'.$row[csf('booking_no')];
				//echo $data;
				?>
				<tr id="tr_<?php echo $i; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"  onClick="js_set_value2('<? echo $data;?>')">
				<td width="30" align="center"><?php echo $i; ?>
				<td width="130"><p><? echo $company_arr[$row[csf('company_name')]]; ?></p></td>
				<td width="110"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
				<td width="110"><p><? echo $row[csf('job_no')]; ?></p></td>
				<td width="120"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
				<td width=""><p><? echo $row[csf('booking_no')]; ?></p></td>
				</tr>
				<?
				$i++;
            }
            ?>
            </table>
            </div>
            <table width="650" cellspacing="0" cellpadding="0" style="border:none" align="center">
                <tr>
                    <td align="center" height="30" valign="bottom">
                    <div style="width:100%">
                        <div style="width:50%; float:left" align="left">
                        <input type="checkbox" name="check_all" id="check_all" onClick="check_all_datas()"/>
                        Check / Uncheck All
                        </div>
                        <div style="width:50%; float:left" align="left">
                        <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px"/>
                        </div>
                    </div>
                    </td>
                </tr>
            </table>
        </form>
    </fieldset>
    </div>
	<?
   exit(); 
}  
$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
/*
$store_arr=return_library_array( "select id, store_name from lib_store_location", "id", "store_name");
$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name");
$other_party_arr=return_library_array( "select id,other_party_name from lib_other_party", "id", "other_party_name");*/

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$con = connect();
	//*********
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
	$cbo_year=str_replace("'","",$cbo_year);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_job_id=str_replace("'","",$txt_job_id);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$txt_booking_id=str_replace("'","",$txt_booking_id);
	$cbo_allocation_balance_status=str_replace("'","",$cbo_allocation_balance_status);
	$cbo_search_by=str_replace("'","",$cbo_search_by);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	
	$sql_cond="";
	if($cbo_buyer_id>0)
		$sql_cond .=" and a.buyer_name=$cbo_buyer_id";
	if($db_type==0)
	{
		if($cbo_year>0)
			$sql_cond .=" and year(a.insert_date)=$cbo_year";
	}
	else
	{
		if($cbo_year>0)
			$sql_cond .=" and to_char(a.insert_date,'YYYY')='$cbo_year'";
	}
	
	if($txt_job_no!="")
		$sql_cond .=" and a.id in($txt_job_id)";
		
	if($txt_booking_no!="")
	{

		$sql_cond .=" and d.id in($txt_booking_id)";
	}
	
	if($txt_date_from!="" && $txt_date_to!="")
	{
		if($cbo_search_by==1)
		{
			$sql_cond .=" and b.pub_shipment_date between '$txt_date_from' and '$txt_date_to'";
		}
		else if($cbo_search_by==2)
		{
			$sql_cond .=" and d.booking_date between '$txt_date_from' and '$txt_date_to'";
		}
		else if($cbo_search_by==3)
		{
			$sql_cond .=" and d.insert_date between '$txt_date_from%' and '$txt_date_to%'";
		}
	}

	//echo $sql_cond ;die;
	
	$sql="select a.id as job_id, a.job_no, a.buyer_name, d.id as booking_id, d.company_id, d.booking_no, d.booking_date, e.count_id, e.copm_one_id, e.type_id, max(c.update_date) as last_update, min(b.pub_shipment_date) as ship_date, listagg(cast(c.po_break_down_id as varchar(4000)),',') within group(order by c.po_break_down_id) as po_id_all, sum(c.grey_fab_qnty) as book_qnty from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c, wo_booking_mst d, wo_pre_cost_fab_yarn_cost_dtls e where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.booking_no=d.booking_no and c.job_no=a.job_no and c.pre_cost_fabric_cost_dtls_id=e.fabric_cost_dtls_id and c.job_no=e.job_no and d.item_category = 2 and a.company_name in (".$cbo_company_id.") and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 $sql_cond group by a.id, a.job_no, a.buyer_name, d.id, d.company_id, d.booking_no, d.booking_date, e.count_id, e.copm_one_id, e.type_id order by d.company_id";
	//echo $sql; die;
	$result=sql_select($sql);
	$report_data=array();$all_booking_no="";
	$temp_table_id=return_field_value("max(id) as id","gbl_temp_report_id","1=1","id");
	$temp_table_id = ($temp_table_id=="")?$temp_table_id=1 : $temp_table_id+1;
	$r_id3=execute_query("delete from tmp_poid where userid=$user_id",0);

	if($r_id3)
	{
		oci_commit($con);
	}

	foreach($result as $row)
	{
		//for booking_no
		if($booking_check[$row[csf("booking_no")]]=="")
		{
			$booking_check[$row[csf("booking_no")]]=$row[csf("booking_no")];
			$r_id=execute_query("insert into gbl_temp_report_id (id, ref_val, ref_from, user_id, ref_string) values ($temp_table_id,".$row[csf("booking_id")].",86,$user_id,'".$row[csf("booking_no")]."')",0);
			if($r_id) {$r_id=1;} 
			else 
			{
				execute_query("delete from gbl_temp_report_id where user_id=$user_id",0);
				echo "insert into gbl_temp_report_id (id, ref_val, ref_from, user_id, ref_string) values ($temp_table_id,".$row[csf("booking_id")].",86,$user_id,'".$row[csf("booking_no")]."')";
				oci_rollback($con);die;
			}
			$temp_table_id++;
		}
		
		//for po_id
		$po_id_arr=array_unique(explode(",",$row[csf("po_id_all")]));
		foreach($po_id_arr as $po_id)
		{
			if($po_id_check[$po_id]=="")
			{
				$po_id_check[$po_id]=$po_id;
				$r_id2=execute_query("insert into tmp_poid (userid, poid) values ($user_id,$po_id)",0);
				if($r_id2) 
				{
					$r_id2=1;
				} 
				else 
				{
					execute_query("delete from tmp_poid where userid=$user_id",0);
					echo "insert into tmp_poid (userid, poid) values ($user_id,$po_id)";
					oci_rollback($con);die;
				}
			}
		}
	}
	
	if($r_id && $r_id2)
	{
		oci_commit($con);
	}
	else
	{
		oci_rollback($con);
	}

	//for booking qty
	// $sql_1="select a.id as job_id, a.job_no, a.buyer_name, d.id as booking_id, d.booking_no, d.booking_date, e.count_id, e.copm_one_id, e.type_id, (c.grey_fab_qnty*e.cons_ratio/100) as book_qnty from tmp_poid f , wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c, wo_booking_mst d, wo_pre_cost_fab_yarn_cost_dtls e where f.poid=c.po_break_down_id and userid=".$user_id." and a.id=b.job_id and c.job_no=a.job_no and c.booking_no=d.booking_no and c.pre_cost_fabric_cost_dtls_id=e.fabric_cost_dtls_id and c.job_no=e.job_no and a.company_name in (".$cbo_company_id.") and a.status_active=1 and c.status_active=1 and d.status_active=1 ".$sql_cond." ";
	$sql_1="select distinct a.id as job_id, c.id as  booking_dtls_id, a.job_no, a.buyer_name, d.id as booking_id, d.booking_no, d.booking_date, e.count_id, e.copm_one_id, e.type_id, (c.grey_fab_qnty*e.cons_ratio/100) as book_qnty from tmp_poid f , wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c, wo_booking_mst d, wo_pre_cost_fab_yarn_cost_dtls e where f.poid=c.po_break_down_id and userid=".$user_id." and a.id=b.job_id and c.job_no=a.job_no and c.booking_no=d.booking_no and c.pre_cost_fabric_cost_dtls_id=e.fabric_cost_dtls_id and c.job_no=e.job_no and a.company_name in (".$cbo_company_id.") and a.status_active=1 and c.status_active=1 and d.status_active=1 ".$sql_cond." ";
	
	//echo $sql_1; die();
	$sql_1_rslt = sql_select($sql_1);
	$qty_data_arr = array();
	foreach($sql_1_rslt as $row)
	{
		$qty_data_arr[$row[csf("booking_no")]][$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("type_id")]]["book_qnty"] += $row[csf("book_qnty")];
	}
		
	//for print data
	foreach($result as $row)
	{
		
		$repeate_data[$row[csf("booking_no")]]["company_id"]	=$row[csf("company_id")];
		$repeate_data[$row[csf("booking_no")]]["buyer_name"]	=$row[csf("buyer_name")];
		$repeate_data[$row[csf("booking_no")]]["last_update"]	=$row[csf("last_update")];
		$repeate_data[$row[csf("booking_no")]]["ship_date"]		=$row[csf("ship_date")];
		$repeate_data[$row[csf("booking_no")]]["booking_date"]	=$row[csf("booking_date")];

        
		$report_data[$row[csf("booking_no")]][$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("type_id")]]["job_id"]=$row[csf("job_id")];
		$report_data[$row[csf("booking_no")]][$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("type_id")]]["job_no"]=$row[csf("job_no")];
		$report_data[$row[csf("booking_no")]][$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("type_id")]]["booking_id"]=$row[csf("booking_id")];
		$report_data[$row[csf("booking_no")]][$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("type_id")]]["booking_no"]=$row[csf("booking_no")];
		
		$report_data[$row[csf("booking_no")]][$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("type_id")]]["count_id"]=$row[csf("count_id")];
		$report_data[$row[csf("booking_no")]][$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("type_id")]]["copm_one_id"]=$row[csf("copm_one_id")];
		$report_data[$row[csf("booking_no")]][$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("type_id")]]["type_id"]=$row[csf("type_id")];
		
		$report_data[$row[csf("booking_no")]][$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("type_id")]]["book_qnty"]=$qty_data_arr[$row[csf("booking_no")]][$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("type_id")]]["book_qnty"];

		$booking_wise_booking_qty[$row[csf("booking_no")]] += $qty_data_arr[$row[csf("booking_no")]][$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("type_id")]]["book_qnty"];	
	}

	//echo "<pre>";
	//print_r($booking_wise_booking_qty); die();

	unset($result);
	
	$temp_booking_data=return_field_value("max(id) as id","gbl_temp_report_id","user_id=$user_id","id");
	//echo $temp_booking_data.test;die;
	if($temp_booking_data!="")
	{
		$req_sql="select a.requ_no, a.requisition_date, b.booking_no, b.buyer_id, b.count_id, b.composition_id, b.yarn_type_id,b.yarn_inhouse_date, sum(b.quantity) as req_qnty 
		from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b 
		where a.id=b.mst_id and a.entry_form=70 and a.status_active=1 and b.status_active=1 and b.booking_no in(select ref_string from gbl_temp_report_id)
        group by a.requ_no, a.requisition_date, b.booking_no, b.buyer_id, b.count_id, b.composition_id, b.yarn_type_id,b.yarn_inhouse_date";
		$req_result=sql_select($req_sql);
		//echo $req_sql;
		//$req_data=array();
		foreach($req_result as $row)
		{
			$report_data[$row[csf("booking_no")]][$row[csf("count_id")]][$row[csf("composition_id")]][$row[csf("yarn_type_id")]]["requ_no"]=$row[csf("requ_no")];
			$report_data[$row[csf("booking_no")]][$row[csf("count_id")]][$row[csf("composition_id")]][$row[csf("yarn_type_id")]]["requisition_date"]=$row[csf("requisition_date")];
			
			$report_data[$row[csf("booking_no")]][$row[csf("count_id")]][$row[csf("composition_id")]][$row[csf("yarn_type_id")]]["yarn_inhouse_date"]=$row[csf("yarn_inhouse_date")];

			$report_data[$row[csf("booking_no")]][$row[csf("count_id")]][$row[csf("composition_id")]][$row[csf("yarn_type_id")]]["req_qnty"]+=$row[csf("req_qnty")];
			
			//for booking and buyer
			$allocation_data_arr[$row[csf("booking_no")]][$row[csf("count_id")]][$row[csf("composition_id")]][$row[csf("yarn_type_id")]]["booking_no"]=$row[csf("booking_no")];
			$allocation_data_arr[$row[csf("booking_no")]][$row[csf("count_id")]][$row[csf("composition_id")]][$row[csf("yarn_type_id")]]["byer"]=$row[csf("buyer_id")];
		}
		unset($req_result);
		
		$wo_sql="select a.wo_number, a.wo_date, a.supplier_id, a.pay_mode, c.booking_no, b.yarn_count, b.yarn_comp_type1st, b.yarn_type,b.yarn_inhouse_date,b.delivery_end_date, sum(b.supplier_order_quantity) as wo_qnty 
		from wo_non_order_info_mst a, wo_non_order_info_dtls b, inv_purchase_requisition_dtls c  
		where a.id=b.mst_id and b.requisition_dtls_id=c.id and a.entry_form=144 and a.status_active=1 and b.status_active=1 and c.booking_no in(select ref_string from gbl_temp_report_id)
        group by a.wo_number, a.wo_date, a.supplier_id, a.pay_mode, c.booking_no, b.yarn_count, b.yarn_comp_type1st, b.yarn_type,b.yarn_inhouse_date,b.delivery_end_date";
        //echo $wo_sql;
		$wo_result=sql_select($wo_sql);
		//$wo_data=array();
		foreach($wo_result as $row)
		{
			$report_data[$row[csf("booking_no")]][$row[csf("yarn_count")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_type")]]["wo_number"]=$row[csf("wo_number")];
			$report_data[$row[csf("booking_no")]][$row[csf("yarn_count")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_type")]]["wo_date"]=$row[csf("wo_date")];

			$report_data[$row[csf("booking_no")]][$row[csf("yarn_count")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_type")]]["delivery_start_date"]=$row[csf("yarn_inhouse_date")];
			$report_data[$row[csf("booking_no")]][$row[csf("yarn_count")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_type")]]["delivery_end_date"]=$row[csf("delivery_end_date")];

			$report_data[$row[csf("booking_no")]][$row[csf("yarn_count")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_type")]]["supplier_id"]=$row[csf("supplier_id")];
			$report_data[$row[csf("booking_no")]][$row[csf("yarn_count")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_type")]]["wo_qnty"]+=$row[csf("wo_qnty")];
		}
		unset($wo_result);
		
		$rcv_sql="select c.booking_no, c.count_id, c.composition_id, c.yarn_type_id, min(a.transaction_date) as rcv_date, sum(a.order_qnty) as rcv_qnty
		from inv_transaction a, wo_non_order_info_dtls b, inv_purchase_requisition_dtls c 
		where a.pi_wo_req_dtls_id=b.id and b.requisition_dtls_id=c.id and a.receive_basis=2 and a.item_category=1 and a.transaction_type=1 and a.status_active=1 and b.status_active=1 and c.booking_no in(select ref_string from gbl_temp_report_id)
        group by c.booking_no, c.count_id, c.composition_id, c.yarn_type_id
		union all
		select d.booking_no, b.count_name as count_id, b.yarn_composition_item1 as composition_id, b.yarn_type as yarn_type_id, min(a.transaction_date) as rcv_date, sum(a.order_qnty) as rcv_qnty
		from inv_transaction a, com_pi_item_details b, wo_non_order_info_dtls c, inv_purchase_requisition_dtls d 
		where a.pi_wo_req_dtls_id=b.id and b.work_order_dtls_id=c.id and c.requisition_dtls_id=d.id and a.receive_basis=1 and a.item_category=1 and a.transaction_type=1 and a.status_active=1 and b.status_active=1 and b.work_order_no is not null and d.booking_no in(select ref_string from gbl_temp_report_id)
        group by d.booking_no, b.count_name, b.yarn_composition_item1, b.yarn_type";
        //echo $rcv_sql;//die();
		$rcv_result=sql_select($rcv_sql);
		$rcv_data=array();
		foreach($rcv_result as $row)
		{
			$report_data[$row[csf("booking_no")]][$row[csf("count_id")]][$row[csf("composition_id")]][$row[csf("yarn_type_id")]]["rcv_date"]=$row[csf("rcv_date")];
			$report_data[$row[csf("booking_no")]][$row[csf("count_id")]][$row[csf("composition_id")]][$row[csf("yarn_type_id")]]["rcv_qnty"]+=$row[csf("rcv_qnty")];
		}
		unset($rcv_result);
		
		$allocation_sql="select a.booking_no,b.job_no, c.yarn_count_id, c.yarn_comp_type1st, c.supplier_id, c.yarn_type,d.buyer_name, min(b.allocation_date) as allocation_date, sum(b.qnty) as alocate_qnty 
		from inv_material_allocation_mst a, inv_material_allocation_dtls b left join wo_po_details_master d on d.job_no=b.job_no, product_details_master c 
		where a.id=b.mst_id and b.item_id=c.id and a.item_category=1 and b.item_category=1 and c.item_category_id=1 and (b.is_dyied_yarn is null or b.is_dyied_yarn=0 OR b.is_dyied_yarn = 2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no in(select ref_string from gbl_temp_report_id) 
		group by a.booking_no,b.job_no, c.yarn_count_id, c.yarn_comp_type1st, c.supplier_id,c.yarn_type,d.buyer_name"; 
		//echo $allocation_sql;die;
		$allocation_result=sql_select($allocation_sql);
		$booking_wise_allocation = array();
		foreach($allocation_result as $row)
		{
			$report_data[$row[csf("booking_no")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_type")]]["allocation_date"]=$row[csf("allocation_date")];
			$report_data[$row[csf("booking_no")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_type")]]["alocate_qnty"]+=$row[csf("alocate_qnty")];
			$report_data[$row[csf("booking_no")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_type")]]["supplier_id_allocation"]=$row[csf("supplier_id")];

			
			//$allocation_data_arr[$row[csf("booking_no")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_type")]]["byer"]=$row[csf("buyer_name")];

            $allocation_data_arr[$row[csf("booking_no")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_type")]]["booking_no"]=$row[csf("booking_no")];
			$booking_wise_allocation[$row[csf("booking_no")]] +=$row[csf("alocate_qnty")];

		}
		unset($allocation_result);	
	}

	/*
	echo "<pre>";
	print_r($booking_wise_allocation); die();
	*/
	
	$temp_poid_data=return_field_value("max(poid) as id","tmp_poid","userid=$user_id","id");
	$tna_cond="";
	if($temp_poid_data!="")
	{		
		$tna_sql="select a.booking_no,b.task_number, 
			min(b.task_start_date) as task_start_date, 
			max(b.task_finish_date) as task_finish_date, 
			min(b.actual_start_date) as actual_start_date, 
			max(b.actual_finish_date) as actual_finish_date 
		from wo_booking_dtls a, tna_process_mst b 
		where a.po_break_down_id=b.po_number_id and b.task_number in(48,50,60) and b.status_active=1 and b.is_deleted=0 and b.po_number_id in(select poid from tmp_poid where userid=$user_id)
		group by a.booking_no,b.task_number";
		 //echo $tna_sql;die;
		$tna_result=sql_select($tna_sql);
		$tna_data=array();
		foreach($tna_result as $row)
		{
			$tna_data[$row[csf("task_number")]][$row[csf("booking_no")]]["task_start_date"]=$row[csf("task_start_date")];
			$tna_data[$row[csf("task_number")]][$row[csf("booking_no")]]["task_finish_date"]=$row[csf("task_finish_date")];
			$tna_data[$row[csf("task_number")]][$row[csf("booking_no")]]["actual_start_date"]=$row[csf("actual_start_date")];
			$tna_data[$row[csf("task_number")]][$row[csf("booking_no")]]["actual_finish_date"]=$row[csf("actual_finish_date")];
			$tna_data[$row[csf("task_number")]][$row[csf("booking_no")]]["task_number"]=$row[csf("task_number")];
		}
		unset($tna_result);
	}
	
	//print_r($tna_data);
	execute_query("delete from gbl_temp_report_id where user_id=$user_id",0);
	execute_query("delete from tmp_poid where userid=$user_id",0);
	oci_commit($con);
	//die;
	
	$exp_comp = explode(',',$cbo_company_id);
	if(count($exp_comp) > 1)
	{
		asort($exp_comp);
		$cmp_arr = array();
		foreach($exp_comp as $kcom=>$vcom)
		{
			$cmp_arr[$kcom]  = $company_arr[$vcom];
		}
		$compName = implode(', ', $cmp_arr);
	}
	else
	{
		$compName = $company_arr[$cbo_company_id];
	}
	
	$width=2980;
 	ob_start();	

	if($report_type==1)
	{
		?>
	    <fieldset style="width:3000px;">
	        <table cellpadding="0" cellspacing="0" width="<?= $width;?>">
	            <tr  class="form_caption" style="border:none;">
	               <td align="center" width="100%" colspan="29" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
	            </tr>
	            <tr  class="form_caption" style="border:none;">
	               <td align="center" width="100%" colspan="29" style="font-size:16px"><strong>Company Name : <? echo $compName; ?></strong></td>
	            </tr>
	        </table>
	        <table width="<?= $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">            	
	            <thead>
	                <th width="30">SL</th>
	                <th width="120">Company</th>
	                <th width="100">Buyer</th>
	                <th width="110">Fab. Bk. No</th>
	                <th width="70">Bk. Date</th>
	                <th width="70">Bk. Last Update Dt.</th>
	                
	                <th width="70">1st Ship Date</th>
	                <th width="130">Req. No</th>
	                <th width="70">Req Date</th>
	                <th width="120">Y/PO. No</th>
	                
	                <th width="70">Y/PO. Date</th>
	                <th width="150">Supplier</th>
	                <th width="80">Yarn Count</th>
	                <th width="150">Composition</th>
	                
	                <th width="100">Yarn Type</th> 
	                <th width="80">Booking Qty</th>  
	                <th width="80">Requisition Qty</th>
	                <th width="80">Y/PO Qty</th>
	                
	                <th width="70">Yarn Allo TNA Start</th>
	                <th width="70">Yarn Allo TNA End</th>
	                
	                <th width="70">Yarn Issue TNA Start</th>
	                <th width="70">Yarn Issue TNA End</th>
	                
	                <th width="70">Knit TNA Start</th>
	                <th width="70">Knit TNA End</th>
	                
	                
	                <th width="70">Plan IH Start</th>
	                <th width="70">Plan IH End</th>
	                
	                <th width="80">Rcv Qty</th> 
	                <th width="70">1st Rcv Date</th>  
	                <th width="80">Yet to Rcv</th>
	                <th width="80">Allocate Qty</th>
	                
	                <th width="70">Allocation Dt.</th>  
	                <th width="80">Yet to allocate</th>
	                <th width="80">Allocated from Prev. Stock</th>
	                <th width="80">Booking Balance</th>
	                <th>Allocation Balance Status</th>
	            </thead>
	        </table>
	        <div style="width:<?= $width+20;?>px; overflow-y: scroll; max-height:350px;" id="scroll_body">
				<table width="<?= $width;?>" cellpadding="0" cellspacing="0" border="1" align="left" rules="all" class="rpt_table"> 
	                <tbody>
						<?
	                    // Data shorting/filtering by allocation balance status start  
	                    $allocation_balance_status_arr=array(1=>'All',2=>'Full Pending',3=>'Partial Balance',4=>'No Balance',5=>'Full Pending And Partial Balance');    
	                    foreach($report_data as $book_no=>$book_data)
	                    {
	                    	foreach($book_data as $count_id=>$count_data)
	                    	{
	                    		foreach($count_data as $composition_id=>$composition_data)
	                    		{
	                    			foreach($composition_data as $yarn_type_id=>$val)
	                    			{
	                    				$booking_qnty = $booking_wise_booking_qty[$book_no];
	                    				$booking_allocation_qnty = $booking_wise_allocation[$book_no];
	                    				$booking_bal = ($booking_wise_booking_qty[$book_no]-$booking_wise_allocation[$book_no]);
	                    				$allocation_balance_status = 0;
	                    				
	                    				if( $cbo_allocation_balance_status==1 ) // all
	                    				{
	                    					if($booking_qnty==$booking_bal)
	                    					{
	                    						$allocation_balance_status = 2; // full pending
	                    					}
	                    					else if($booking_bal>0 && $booking_allocation_qnty>0)
	                    					{
	                    						$allocation_balance_status=3; // partial
	                    					}
	                    					else if($booking_bal<0.01)
	                    					{
	                    						$allocation_balance_status=4; // No balance
	                    					}
	                    					else if( $booking_qnty==$booking_bal || $booking_bal>0)
	                    					{
	                    						$allocation_balance_status=5; // full pending and partial
	                    					}

	                    					$report_data[$book_no][$count_id][$composition_id][$yarn_type_id]['print_status']=1;
	                    					$report_data[$book_no][$count_id][$composition_id][$yarn_type_id]['al_balance_status']=$allocation_balance_status;

	                    					$booking_no_print_status[$book_no]=1;
	                    				}
	                    				else if( $cbo_allocation_balance_status==2 )// full pending
	                    				{
	                    					if($booking_qnty==$booking_bal)
	                    					{ 
	                    						$allocation_balance_status =2; 
	                    						$report_data[$book_no][$count_id][$composition_id][$yarn_type_id]['print_status']=1;
	                    						$report_data[$book_no][$count_id][$composition_id][$yarn_type_id]['al_balance_status']=$allocation_balance_status;
	                    						$booking_no_print_status[$book_no]=1;
	                    					}
	                    					else
	                    					{
	                    						$report_data[$book_no][$count_id][$composition_id][$yarn_type_id]['print_status']=0;
	                    						$booking_no_print_status[$book_no]=0;
	                    					}
	                    				}
	                    				else if($cbo_allocation_balance_status==3) // Partial
	                    				{
	                    					if($booking_bal>0 && $booking_allocation_qnty>0)
	                    					{
	                    						$allocation_balance_status=3;
	                    						$report_data[$book_no][$count_id][$composition_id][$yarn_type_id]['print_status']=1;
	                    						$report_data[$book_no][$count_id][$composition_id][$yarn_type_id]['al_balance_status']=$allocation_balance_status;
	                    						$booking_no_print_status[$book_no]=1;
	                    					}else{
	                    						$report_data[$book_no][$count_id][$composition_id][$yarn_type_id]['print_status']=0;
	                    						$booking_no_print_status[$book_no]=0;
	                    					}
	                    				}
	                    				else if($cbo_allocation_balance_status==4) // No Balance
	                    				{
	                    					if($booking_bal<0.01)
	                    					{
	                    						$allocation_balance_status=4;
	                    						$report_data[$book_no][$count_id][$composition_id][$yarn_type_id]['print_status']=1;
	                    						$report_data[$book_no][$count_id][$composition_id][$yarn_type_id]['al_balance_status']=$allocation_balance_status;
	                    						$booking_no_print_status[$book_no]=1;
	                    					}else{
	                    						$report_data[$book_no][$count_id][$composition_id][$yarn_type_id]['print_status']=0;
	                    						$booking_no_print_status[$book_no]=0;
	                    					}	
	                    				}
	                    				else if($cbo_allocation_balance_status==5) // full pending and partial
	                    				{
	                    					if( $booking_qnty==$booking_bal || $booking_bal>0)
	                    					{
	                    						$report_data[$book_no][$count_id][$composition_id][$yarn_type_id]['print_status']=1;
	                    						$booking_no_print_status[$book_no]=1;
	                    						
	                    						if($booking_qnty==$booking_bal)
	                    						{
	                    							$allocation_balance_status = 2;
	                    							$report_data[$book_no][$count_id][$composition_id][$yarn_type_id]['al_balance_status']=$allocation_balance_status;
	                    						}else{
	                    							$allocation_balance_status = 5;
	                    							$report_data[$book_no][$count_id][$composition_id][$yarn_type_id]['al_balance_status']=$allocation_balance_status;
	                    						}
	                    					}
	                    					else
	                    					{
	                    						$report_data[$book_no][$count_id][$composition_id][$yarn_type_id]['print_status']=0;
	                    						$booking_no_print_status[$book_no]=0;
	                    					}
	                    				}
	                    				else
	                    				{
	                    					$report_data[$book_no][$count_id][$composition_id][$yarn_type_id]['print_status']=0;
	                    					$booking_no_print_status[$book_no]=0;
	                    				}

	                    				if( $cbo_search_by== 4 && $txt_date_from!="" && $txt_date_to!="" ) // TNA Date condition
										{
											$task_start_date = strtotime($tna_data[48][$book_no]["task_start_date"]);
											$task_finish_date = strtotime($tna_data[48][$book_no]["task_finish_date"]);
											$from_date = strtotime($txt_date_from);
											$to_date = strtotime($txt_date_to);
											$task_number = $tna_data[48][$book_no]["task_number"];
	                                        
	                                        if ( $task_number && ($task_start_date >= $from_date &&  $task_start_date <= $to_date) || ($task_finish_date >= $from_date &&  $task_finish_date <= $to_date) ) 
	                                        {
	                                        	$report_data[$book_no][$count_id][$composition_id][$yarn_type_id]['print_status']=1;
	                                        	$booking_no_print_status[$book_no]=1;
	                                        }                                       
	                                        else
	                                        {
	                                        	$report_data[$book_no][$count_id][$composition_id][$yarn_type_id]['print_status']=0;
	                                        	$booking_no_print_status[$book_no]=0;
	                                        }
										}

	                    			}
	                    		}
	                    	}
	                    }
	                    // Data shorting/filtering by allocation balance status end

						//echo "<pre>";print_r($report_data);die;
						$i=1;         
						foreach($report_data as $book_no=>$book_data)
						{
							foreach($book_data as $count_id=>$count_data)
							{
								foreach($count_data as $composition_id=>$composition_data)
								{
									foreach($composition_data as $yarn_type_id=>$val)
									{
										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										
										$yet_to_rcv=$val['book_qnty']-$val['rcv_qnty'];
										$yet_to_allocate=$val['book_qnty']-$val['alocate_qnty'];
										$allocate_from_prev_st=$val['alocate_qnty']-$val['rcv_qnty'];
										$booking_bal=$val['book_qnty']-$val['alocate_qnty'];

										$allocation_booking_no = $allocation_data_arr[$book_no][$count_id][$composition_id][$yarn_type_id]["booking_no"];
										$allocation_byer = $allocation_data_arr[$book_no][$count_id][$composition_id][$yarn_type_id]["byer"];

										$allocation_title = $book_no."_".$count_id."_".$composition_id."_".$yarn_type_id."_".$val['alocate_qnty'];

										$plan_ih_startdate = $val['delivery_start_date'];
										$plan_ih_enddate =$val['delivery_end_date'];

										if($plan_ih_startdate =="" && $plan_ih_enddate=="")
										{
											$plan_ih_startdate = $val['yarn_inhouse_date'];
											$plan_ih_enddate = $val['yarn_inhouse_date'];
										}
                                        
                                        if( $val['booking_no']=="" )
										{
											$font_color="color:#F00;";
											
											if($val['booking_no']=="")
											{
												$val['booking_no'] = $allocation_booking_no;
											}
										}
										else
										{
											$font_color="color:#000;";	
										}	
                                     
										$company_name = $company_arr[$repeate_data[$val['booking_no']]["company_id"]];
										$buyer_name = $buyer_arr[$repeate_data[$val['booking_no']]["buyer_name"]];
										$booking_date = $repeate_data[$val['booking_no']]["booking_date"];
										$last_update = $repeate_data[$val['booking_no']]["last_update"];
										$ship_date = $repeate_data[$val['booking_no']]["ship_date"];

										//echo $booking_wise_allocation[$book_no]."==".$booking_wise_booking_qty[$book_no]."<br>";
										if($val['print_status']==1) 
										{
											?>

		                                    <tr title="<? echo $allocation_title;?>" bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>" style=" <? echo $font_color ?>">
												<td width="30" align="center"><? echo $i; ?></td>
		                                        <td width="120"><p><? echo $company_name; ?></p></td>
		                                        <td width="100"><p><? echo $buyer_name; ?></p></td>
		                                        <td width="110"><p><? echo $val['booking_no']; ?></p></td>
		                                        <td width="70" align="center"><p><? if($booking_date !="" && $booking_date != '0000-00-00') echo change_date_format($booking_date); ?></p></td>
		                                        <td width="70" align="center"><p><? if($last_update !="" && $last_update != '0000-00-00') echo change_date_format($last_update); ?></p></td>
		                                        <td width="70" align="center"><p><? if($ship_date !="" && $ship_date != '0000-00-00') echo change_date_format($ship_date); ?></p></td>
		                                        <td width="130"><p><? echo $val['requ_no']; ?></p></td>
		                                        <td width="70" align="center"><p><? if($val['requisition_date'] !="" && $val['requisition_date'] != '0000-00-00') echo change_date_format($val['requisition_date']); ?></p></td>
		                                        <td width="120"><p><? echo $val['wo_number']; ?></p></td>
		                                        <td width="70" align="center"><p><? if($val['wo_date'] !="" && $val['wo_date'] != '0000-00-00') echo change_date_format($val['wo_date']); ?>&nbsp;</p></td>
		                                        <td width="150"><p><? if($val['supplier_id']) echo $supplier_arr[$val['supplier_id']]; else echo $supplier_arr[$val['supplier_id_allocation']]; ?></p></td>
		                                        <td width="80" align="center"><p><? echo $count_arr[$count_id]; ?></p></td>
		                                        <td width="150"><p><? echo $composition[$composition_id]; ?></p></td>
		                                        <td width="100"><p><? echo $yarn_type[$yarn_type_id]; ?></p></td> 
		                                        <td width="80" align="right"><? echo number_format($val['book_qnty'],2); ?></td>  
		                                        <td width="80" align="right"><? echo number_format($val['req_qnty'],2); ?></td>
		                                        <td width="80" align="right"><? echo number_format($val['wo_qnty'],2); ?></td>
		                                        
		                                        <td width="70" align="center"><p><? if($tna_data[48][$book_no]["task_start_date"] !="" && $tna_data[48][$book_no]["task_start_date"] != '0000-00-00') echo change_date_format($tna_data[48][$book_no]["task_start_date"]); ?>&nbsp;</p></td>
		                                        <td width="70" align="center"><p><? if($tna_data[48][$book_no]["task_finish_date"] !="" && $tna_data[48][$book_no]["task_finish_date"] != '0000-00-00') echo change_date_format($tna_data[48][$book_no]["task_finish_date"]); ?>&nbsp;</p></td>
		                                        <td width="70" align="center"><p><? if($tna_data[50][$book_no]["task_start_date"] !="" && $tna_data[50][$book_no]["task_start_date"] != '0000-00-00') echo change_date_format($tna_data[50][$book_no]["task_start_date"]); ?>&nbsp;</p></td>
		                                        <td width="70" align="center"><p><? if($tna_data[50][$book_no]["task_finish_date"] !="" && $tna_data[50][$book_no]["task_finish_date"] != '0000-00-00') echo change_date_format($tna_data[50][$book_no]["task_finish_date"]); ?>&nbsp;</p></td>
		                                        
		                                        
		                                        <td width="70" align="center"><p><? if($tna_data[60][$book_no]["task_start_date"] !="" && $tna_data[60][$book_no]["task_start_date"] != '0000-00-00') echo change_date_format($tna_data[60][$book_no]["task_start_date"]); ?>&nbsp;</p></td>
		                                        <td width="70" align="center"><p><? if($tna_data[60][$book_no]["task_finish_date"] !="" && $tna_data[60][$book_no]["task_finish_date"] != '0000-00-00') echo change_date_format($tna_data[60][$book_no]["task_finish_date"]); ?>&nbsp;</p></td>
		                                        <td width="70" align="center"><p><? if($plan_ih_startdate !="" && $plan_ih_startdate != '0000-00-00') echo change_date_format($plan_ih_startdate); ?>&nbsp;</p></td>
		                                        <td width="70" align="center"><p><? if($plan_ih_enddate !="" && $plan_ih_enddate != '0000-00-00') echo change_date_format($plan_ih_enddate); ?>&nbsp;</p></td>
		                                        
		                                        <td width="80" align="right"><? echo number_format($val['rcv_qnty'],2); ?></td> 
		                                        <td width="70" align="center"><p><? if($val['rcv_date'] !="" && $val['rcv_date'] != '0000-00-00') echo change_date_format($val['rcv_date']); ?>&nbsp;</p></td> 
		                                        <td width="80" align="right"><? echo number_format($yet_to_rcv,2); ?></td>
		                                        <td width="80" align="right"><? echo number_format($val['alocate_qnty'],2); ?></td>                                        
		                                        <td width="70" align="center"><p><? if($val['allocation_date'] !="" && $val['allocation_date'] != '0000-00-00') echo change_date_format($val['allocation_date']); ?>&nbsp;</p></td>   
		                                        <td width="80" align="right"><? echo number_format($yet_to_allocate,2); ?></td>
		                                        <td width="80" align="right"><? echo number_format($allocate_from_prev_st,2); ?></td>
		                                        <td align="right" width="80"><? echo number_format($booking_bal,2); ?></td>
		                                        <td><p><? echo $allocation_balance_status_arr[$val['al_balance_status']]; ?></p></td>
		                                    </tr>
		                                    <?
											$booking_total_book_qnty+=$val['book_qnty'];
											$booking_total_req_qnty+=$val['req_qnty'];
											$booking_total_wo_qnty+=$val['wo_qnty'];
											$booking_total_rcv_qnty+=$val['rcv_qnty'];
											$booking_total_yet_to_rcv+=$yet_to_rcv;
											$booking_total_alocate_qnty+=$val['alocate_qnty'];
											$booking_total_yet_to_allocate+=$yet_to_allocate;
											$booking_total_prev_stock+=$allocate_from_prev_st;
											$booking_total_booking_bal+=$booking_bal;
											
											$i++;
										}
									}
								}
							}

							if($booking_no_print_status[$book_no]==1)
							{
							?>
		                        <tr class="tbl_bottom">
		                            <td colspan="15" align="right"><b>Booking No : <? echo $book_no; ?> Total:</b></td>
		                            <td align="right"><? echo number_format($booking_total_book_qnty,2,'.',''); $grand_tot_book_qnty+=$booking_total_book_qnty; ?></td>
		                            <td align="right"><? echo number_format($booking_total_req_qnty,2,'.',''); $grand_tot_req_qnty+=$booking_total_req_qnty; ?></td>
		                            <td align="right"><? echo number_format($booking_total_wo_qnty,2,'.',''); $grand_tot_wo_qnty+=$booking_total_wo_qnty; ?></td>
		                            <td>&nbsp;</td>
		                            <td>&nbsp;</td>
		                            <td>&nbsp;</td>
		                            <td>&nbsp;</td>
		                            
		                            <td>&nbsp;</td>
		                            <td>&nbsp;</td>
		                            <td>&nbsp;</td>
		                            <td>&nbsp;</td>
		                            <td align="right"><? echo number_format($booking_total_rcv_qnty,2,'.',''); $grand_tot_rcv_qnty+=$booking_total_rcv_qnty; ?></td>
		                            <td>&nbsp;</td>
		                            <td align="right"><? echo number_format($booking_total_yet_to_rcv,2,'.',''); $grand_tot_yet_to_rcv+=$booking_total_yet_to_rcv; ?></td>
		                            <td align="right"><? echo number_format($booking_total_alocate_qnty,2,'.',''); $grand_tot_alocate_qnty+=$booking_total_alocate_qnty; ?></td>
		                            <td>&nbsp;</td>
		                            <td align="right"><? echo number_format($booking_total_yet_to_allocate,2,'.',''); $grand_tot_yet_to_allocate+=$booking_total_yet_to_allocate; ?></td>
		                            <td align="right"><? echo number_format($booking_total_prev_stock,2,'.',''); $grand_tot_prev_stock+=$booking_total_prev_stock; ?></td>
		                            <td align="right"><? echo number_format($booking_total_booking_bal,2,'.',''); $grand_tot_booking_bal+=$booking_total_booking_bal; ?></td>
		                            <td>&nbsp;</td>
		                        </tr>
		                        <?
								$booking_total_book_qnty=0;
								$booking_total_req_qnty=0;
								$booking_total_wo_qnty=0;
								$booking_total_rcv_qnty=0;
								$booking_total_yet_to_rcv=0;
								$booking_total_alocate_qnty=0;
								$booking_total_yet_to_allocate=0;
								$booking_total_prev_stock=0;
								$booking_total_booking_bal=0;
							}
						}
						?>
	                    </tbody>				
	            </table> 
	        </div>
	        <table width="<?= $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table"  align="left">            	
	            <tfoot>
					<th width="30" align="right">&nbsp;</th>
	            	<th width="120" align="right">&nbsp;</th>
	            	<th width="100" align="right">&nbsp;</th>
	            	<th width="110" align="right">&nbsp;</th>
	            	<th width="70" align="right">&nbsp;</th>
	            	<th width="70" align="right">&nbsp;</th>
	            	<th width="70" align="right">&nbsp;</th>
	            	<th width="130" align="right">&nbsp;</th>
	            	<th width="70" align="right">&nbsp;</th>
	            	<th width="120" align="right">&nbsp;</th>
	            	<th width="70" align="right">&nbsp;</th>
	            	<th width="150" align="right">&nbsp;</th>
	            	<th width="80" align="right">&nbsp;</th> 
	                <th width="150" align="right">&nbsp;</th>
	                <th width="100"align="right">Grand Total :</th> 
	                <th width="80" align="right"><? echo number_format($grand_tot_book_qnty,2,'.','');?></th>  
	                <th width="80" align="right"><? echo number_format($grand_tot_req_qnty,2,'.','');?></th>
	                <th width="80" align="right"><? echo number_format($grand_tot_wo_qnty,2,'.','');?></th>
	                
	                <th width="70">&nbsp;</th>
	                <th width="70">&nbsp;</th>
	                <th width="70">&nbsp;</th>
	                <th width="70">&nbsp;</th>
	                <th width="70">&nbsp;</th>
	                <th width="70">&nbsp;</th>
	                <th width="70">&nbsp;</th>
	                <th width="70">&nbsp;</th>
	                
	                <th width="80" align="right"><? echo number_format($grand_tot_rcv_qnty,2,'.','');?></th> 
	                <th width="70">&nbsp;</th>  
	                <th width="80" align="right"><? echo number_format($grand_tot_yet_to_rcv,2,'.','');?></th>
	                <th width="80" align="right"><? echo number_format($grand_tot_alocate_qnty,2,'.','');?></th>
	                
	                <th width="70">&nbsp;</th>  
	                <th width="80" align="right"><? echo number_format($grand_tot_yet_to_allocate,2,'.','');?></th>
	                <th width="80" align="right"><? echo number_format($grand_tot_prev_stock,2,'.','');?></th>
	                <th width="80" align="right"><? echo number_format($grand_tot_booking_bal,2,'.','');?></th>
	                <th>&nbsp;</th>
	            </tfoot>
	        </table>
	    </fieldset>      
		<?
	}
	else
	{
		?>
	    <fieldset style="width:2650px;">
	        <table cellpadding="0" cellspacing="0" width="<?= $width;?>">
	            <tr  class="form_caption" style="border:none;">
	               <td align="center" width="100%" colspan="29" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
	            </tr>
	            <tr  class="form_caption" style="border:none;">
	               <td align="center" width="100%" colspan="29" style="font-size:16px"><strong>Company Name : <? echo $compName; ?></strong></td>
	            </tr>
	        </table>
	        <table width="<?= $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">            	
	            <thead>
	                <th width="30">SL</th>
	                <th width="120">Company</th>
	                <th width="100">Buyer</th>
	                <th width="110">Fab. Bk. No</th>
	                <th width="70">Bk. Date</th>
	                <th width="70">Bk. Last Update Dt.</th>
	                
	                <th width="70">1st Ship Date</th>
	                <th width="130">Req. No</th>
	                <th width="70">Req Date</th>
	                <th width="120">Y/PO. No</th>
	                
	                <th width="70">Y/PO. Date</th>
	                <th width="150">Supplier</th>
	                <th width="80">Yarn Count</th>
	                <th width="150">Composition</th>
	                
	                <th width="100">Yarn Type</th> 
	                <th width="80">Booking Qty</th>  
	                <th width="80">Requisition Qty</th>
	                <th width="80">Y/PO Qty</th>
	                
	                <th width="70">Yarn Allo TNA Start</th>
	                <th width="70">Yarn Allo TNA End</th>
	                
	                <th width="70">Yarn Issue TNA Start</th>
	                <th width="70">Yarn Issue TNA End</th>
	                
	                <th width="70">Knit TNA Start</th>
	                <th width="70">Knit TNA End</th>
	                
	                
	                <th width="70">Plan IH Start</th>
	                <th width="70">Plan IH End</th>
	                
	                <th width="80">Rcv Qty</th> 
	                <th width="70">1st Rcv Date</th>  
	                <th width="80">Yet to Rcv</th>
	                <th width="80">Allocate Qty</th>
	                
	                <th width="70">Allocation Dt.</th>  
	                <th width="80">Yet to allocate</th>
	                <th width="80">Allocated from Prev. Stock</th>
	                <th width="80">Booking Balance</th>
	                <th>Allocation Balance Status</th>
	            </thead>
	        </table>
	        <div style="width:<?= $width+20;?>px; overflow-y: scroll; max-height:350px;" id="scroll_body">
				<table width="<?= $width;?>" cellpadding="0" cellspacing="0" border="1" align="left" rules="all" class="rpt_table"> 
	                <tbody>
						<?
	                    // Data shorting/filtering by allocation balance status start  
	                    $allocation_balance_status_arr=array(1=>'All',2=>'Full Pending',3=>'Partial Balance',4=>'No Balance',5=>'Full Pending And Partial Balance');    
	                    foreach($report_data as $book_no=>$book_data)
	                    {
	                    	foreach($book_data as $count_id=>$count_data)
	                    	{
	                    		foreach($count_data as $composition_id=>$composition_data)
	                    		{
	                    			foreach($composition_data as $yarn_type_id=>$val)
	                    			{
	                    				$booking_qnty = $booking_wise_booking_qty[$book_no];
	                    				$booking_allocation_qnty = $booking_wise_allocation[$book_no];
	                    				$booking_bal = ($booking_wise_booking_qty[$book_no]-$booking_wise_allocation[$book_no]);
	                    				$allocation_balance_status = 0;
	                    				
	                    				if( $cbo_allocation_balance_status==1 ) // all
	                    				{
	                    					if($booking_qnty==$booking_bal)
	                    					{
	                    						$allocation_balance_status = 2; // full pending
	                    					}
	                    					else if($booking_bal>0 && $booking_allocation_qnty>0)
	                    					{
	                    						$allocation_balance_status=3; // partial
	                    					}
	                    					else if($booking_bal<0.01)
	                    					{
	                    						$allocation_balance_status=4; // No balance
	                    					}
	                    					else if( $booking_qnty==$booking_bal || $booking_bal>0)
	                    					{
	                    						$allocation_balance_status=5; // full pending and partial
	                    					}

	                    					$report_data[$book_no][$count_id][$composition_id][$yarn_type_id]['print_status']=1;
	                    					$report_data[$book_no][$count_id][$composition_id][$yarn_type_id]['al_balance_status']=$allocation_balance_status;

	                    					$booking_no_print_status[$book_no]=1;
	                    				}
	                    				else if( $cbo_allocation_balance_status==2 )// full pending
	                    				{
	                    					if($booking_qnty==$booking_bal)
	                    					{ 
	                    						$allocation_balance_status =2; 
	                    						$report_data[$book_no][$count_id][$composition_id][$yarn_type_id]['print_status']=1;
	                    						$report_data[$book_no][$count_id][$composition_id][$yarn_type_id]['al_balance_status']=$allocation_balance_status;
	                    						$booking_no_print_status[$book_no]=1;
	                    					}
	                    					else
	                    					{
	                    						$report_data[$book_no][$count_id][$composition_id][$yarn_type_id]['print_status']=0;
	                    						$booking_no_print_status[$book_no]=0;
	                    					}
	                    				}
	                    				else if($cbo_allocation_balance_status==3) // Partial
	                    				{
	                    					if($booking_bal>0 && $booking_allocation_qnty>0)
	                    					{
	                    						$allocation_balance_status=3;
	                    						$report_data[$book_no][$count_id][$composition_id][$yarn_type_id]['print_status']=1;
	                    						$report_data[$book_no][$count_id][$composition_id][$yarn_type_id]['al_balance_status']=$allocation_balance_status;
	                    						$booking_no_print_status[$book_no]=1;
	                    					}else{
	                    						$report_data[$book_no][$count_id][$composition_id][$yarn_type_id]['print_status']=0;
	                    						$booking_no_print_status[$book_no]=0;
	                    					}
	                    				}
	                    				else if($cbo_allocation_balance_status==4) // No Balance
	                    				{
	                    					if($booking_bal<0.01)
	                    					{
	                    						$allocation_balance_status=4;
	                    						$report_data[$book_no][$count_id][$composition_id][$yarn_type_id]['print_status']=1;
	                    						$report_data[$book_no][$count_id][$composition_id][$yarn_type_id]['al_balance_status']=$allocation_balance_status;
	                    						$booking_no_print_status[$book_no]=1;
	                    					}else{
	                    						$report_data[$book_no][$count_id][$composition_id][$yarn_type_id]['print_status']=0;
	                    						$booking_no_print_status[$book_no]=0;
	                    					}	
	                    				}
	                    				else if($cbo_allocation_balance_status==5) // full pending and partial
	                    				{
	                    					if( $booking_qnty==$booking_bal || $booking_bal>0)
	                    					{
	                    						$report_data[$book_no][$count_id][$composition_id][$yarn_type_id]['print_status']=1;
	                    						$booking_no_print_status[$book_no]=1;
	                    						
	                    						if($booking_qnty==$booking_bal)
	                    						{
	                    							$allocation_balance_status = 2;
	                    							$report_data[$book_no][$count_id][$composition_id][$yarn_type_id]['al_balance_status']=$allocation_balance_status;
	                    						}else{
	                    							$allocation_balance_status = 5;
	                    							$report_data[$book_no][$count_id][$composition_id][$yarn_type_id]['al_balance_status']=$allocation_balance_status;
	                    						}
	                    					}
	                    					else
	                    					{
	                    						$report_data[$book_no][$count_id][$composition_id][$yarn_type_id]['print_status']=0;
	                    						$booking_no_print_status[$book_no]=0;
	                    					}
	                    				}
	                    				else
	                    				{
	                    					$report_data[$book_no][$count_id][$composition_id][$yarn_type_id]['print_status']=0;
	                    					$booking_no_print_status[$book_no]=0;
	                    				}

	                    				if( $cbo_search_by== 4 && $txt_date_from!="" && $txt_date_to!="" ) // TNA Date condition
										{
											$task_start_date = strtotime($tna_data[48][$book_no]["task_start_date"]);
											$task_finish_date = strtotime($tna_data[48][$book_no]["task_finish_date"]);
											$from_date = strtotime($txt_date_from);
											$to_date = strtotime($txt_date_to);
											$task_number = $tna_data[48][$book_no]["task_number"];
	                                        
	                                        if ( $task_number && ($task_start_date >= $from_date &&  $task_start_date <= $to_date) || ($task_finish_date >= $from_date &&  $task_finish_date <= $to_date) ) 
	                                        {
	                                        	$report_data[$book_no][$count_id][$composition_id][$yarn_type_id]['print_status']=1;
	                                        	$booking_no_print_status[$book_no]=1;
	                                        }                                       
	                                        else
	                                        {
	                                        	$report_data[$book_no][$count_id][$composition_id][$yarn_type_id]['print_status']=0;
	                                        	$booking_no_print_status[$book_no]=0;
	                                        }
										}

	                    			}
	                    		}
	                    	}
	                    }
	                    // Data shorting/filtering by allocation balance status end

						//echo "<pre>";print_r($report_data);die;
						$i=1;         
						foreach($report_data as $book_no=>$book_data)
						{
							foreach($book_data as $count_id=>$count_data)
							{
								foreach($count_data as $composition_id=>$composition_data)
								{
									foreach($composition_data as $yarn_type_id=>$val)
									{
										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										
										$yet_to_rcv=$val['book_qnty']-$val['rcv_qnty'];
										$yet_to_allocate=$val['book_qnty']-$val['alocate_qnty'];
										$allocate_from_prev_st=$val['alocate_qnty']-$val['rcv_qnty'];
										$booking_bal=$val['book_qnty']-$val['alocate_qnty'];

										$allocation_booking_no = $allocation_data_arr[$book_no][$count_id][$composition_id][$yarn_type_id]["booking_no"];
										$allocation_byer = $allocation_data_arr[$book_no][$count_id][$composition_id][$yarn_type_id]["byer"];

										$allocation_title = $book_no."_".$count_id."_".$composition_id."_".$yarn_type_id."_".$val['alocate_qnty'];

										$plan_ih_startdate = $val['delivery_start_date'];
										$plan_ih_enddate =$val['delivery_end_date'];

										if($plan_ih_startdate =="" && $plan_ih_enddate=="")
										{
											$plan_ih_startdate = $val['yarn_inhouse_date'];
											$plan_ih_enddate = $val['yarn_inhouse_date'];
										}

										if( $val['booking_no']=="" )
										{
											$font_color="color:#F00;";
											
											if($val['booking_no']=="")
											{
												$val['booking_no'] = $allocation_booking_no;
											}
										}
										else
										{
											$font_color="color:#000;";	
										}	

										$company_name = $company_arr[$repeate_data[$val['booking_no']]["company_id"]];
										$buyer_name = $buyer_arr[$repeate_data[$val['booking_no']]["buyer_name"]];
										$booking_date = $repeate_data[$val['booking_no']]["booking_date"];
										$last_update = $repeate_data[$val['booking_no']]["last_update"];
										$ship_date = $repeate_data[$val['booking_no']]["ship_date"];

										//echo $booking_wise_allocation[$book_no]."==".$booking_wise_booking_qty[$book_no]."<br>";
										if($val['print_status']==1) 
										{
											?>
		                                    <tr title="<? echo $allocation_title;?>" bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>" style=" <? echo $font_color ?>">
												<td width="30" align="center"><? echo $i; ?></td>
		                                        <td width="120"><p><? echo $company_name; ?></p></td>
		                                        <td width="100"><p><? echo $buyer_name; ?></p></td>
		                                        <td width="110"><p><? echo $val['booking_no']; ?></p></td>
		                                        <td width="70" align="center"><p><? if($booking_date !="" && $booking_date != '0000-00-00') echo change_date_format($booking_date); ?></p></td>
		                                        <td width="70" align="center"><p><? if($last_update !="" && $last_update != '0000-00-00') echo change_date_format($last_update); ?></p></td>
		                                        <td width="70" align="center"><p><? if($ship_date !="" && $ship_date != '0000-00-00') echo change_date_format($ship_date); ?></p></td>
		                                        <td width="130"><p><? echo $val['requ_no']; ?></p></td>
		                                        <td width="70" align="center"><p><? if($val['requisition_date'] !="" && $val['requisition_date'] != '0000-00-00') echo change_date_format($val['requisition_date']); ?></p></td>
		                                        <td width="120"><p><? echo $val['wo_number']; ?></p></td>
		                                        <td width="70" align="center"><p><? if($val['wo_date'] !="" && $val['wo_date'] != '0000-00-00') echo change_date_format($val['wo_date']); ?>&nbsp;</p></td>
		                                        <td width="150"><p><? if($val['supplier_id']) echo $supplier_arr[$val['supplier_id']]; else echo $supplier_arr[$val['supplier_id_allocation']]; ?></p></td>
		                                        <td width="80" align="center"><p><? echo $count_arr[$count_id]; ?></p></td>
		                                        <td width="150"><p><? echo $composition[$composition_id]; ?></p></td>
		                                        <td width="100"><p><? echo $yarn_type[$yarn_type_id]; ?></p></td> 
		                                        <td width="80" align="right"><? echo number_format($val['book_qnty'],2); ?></td>  
		                                        <td width="80" align="right"><? echo number_format($val['req_qnty'],2); ?></td>
		                                        <td width="80" align="right"><? echo number_format($val['wo_qnty'],2); ?></td>
		                                        
		                                        <td width="70" align="center"><p><? if($tna_data[48][$book_no]["task_start_date"] !="" && $tna_data[48][$book_no]["task_start_date"] != '0000-00-00') echo change_date_format($tna_data[48][$book_no]["task_start_date"]); ?>&nbsp;</p></td>
		                                        <td width="70" align="center"><p><? if($tna_data[48][$book_no]["task_finish_date"] !="" && $tna_data[48][$book_no]["task_finish_date"] != '0000-00-00') echo change_date_format($tna_data[48][$book_no]["task_finish_date"]); ?>&nbsp;</p></td>
		                                        <td width="70" align="center"><p><? if($tna_data[50][$book_no]["task_start_date"] !="" && $tna_data[50][$book_no]["task_start_date"] != '0000-00-00') echo change_date_format($tna_data[50][$book_no]["task_start_date"]); ?>&nbsp;</p></td>
		                                        <td width="70" align="center"><p><? if($tna_data[50][$book_no]["task_finish_date"] !="" && $tna_data[50][$book_no]["task_finish_date"] != '0000-00-00') echo change_date_format($tna_data[50][$book_no]["task_finish_date"]); ?>&nbsp;</p></td>
		                                        
		                                        
		                                        <td width="70" align="center"><p><? if($tna_data[60][$book_no]["task_start_date"] !="" && $tna_data[60][$book_no]["task_start_date"] != '0000-00-00') echo change_date_format($tna_data[60][$book_no]["task_start_date"]); ?>&nbsp;</p></td>
		                                        <td width="70" align="center"><p><? if($tna_data[60][$book_no]["task_finish_date"] !="" && $tna_data[60][$book_no]["task_finish_date"] != '0000-00-00') echo change_date_format($tna_data[60][$book_no]["task_finish_date"]); ?>&nbsp;</p></td>
		                                        <td width="70" align="center"><p><? if($plan_ih_startdate !="" && $plan_ih_startdate != '0000-00-00') echo change_date_format($plan_ih_startdate); ?>&nbsp;</p></td>
		                                        <td width="70" align="center"><p><? if($plan_ih_enddate !="" && $plan_ih_enddate != '0000-00-00') echo change_date_format($plan_ih_enddate); ?>&nbsp;</p></td>
		                                        
		                                        <td width="80" align="right"><? echo number_format($val['rcv_qnty'],2); ?></td> 
		                                        <td width="70" align="center"><p><? if($val['rcv_date'] !="" && $val['rcv_date'] != '0000-00-00') echo change_date_format($val['rcv_date']); ?>&nbsp;</p></td> 
		                                        <td width="80" align="right"><? echo number_format($yet_to_rcv,2); ?></td>
		                                        <td width="80" align="right"><? echo number_format($val['alocate_qnty'],2); ?></td>                                        
		                                        <td width="70" align="center"><p><? if($val['allocation_date'] !="" && $val['allocation_date'] != '0000-00-00') echo change_date_format($val['allocation_date']); ?>&nbsp;</p></td>   
		                                        <td width="80" align="right"><? echo number_format($yet_to_allocate,2); ?></td>
		                                        <td width="80" align="right"><? echo number_format($allocate_from_prev_st,2); ?></td>
		                                        <td align="right" width="80"><? echo number_format($booking_bal,2); ?></td>
		                                        <td><p><? echo $allocation_balance_status_arr[$val['al_balance_status']]; ?></p></td>
		                                    </tr>
		                                    <?
											$booking_total_book_qnty+=$val['book_qnty'];
											$booking_total_req_qnty+=$val['req_qnty'];
											$booking_total_wo_qnty+=$val['wo_qnty'];
											$booking_total_rcv_qnty+=$val['rcv_qnty'];
											$booking_total_yet_to_rcv+=$yet_to_rcv;
											$booking_total_alocate_qnty+=$val['alocate_qnty'];
											$booking_total_yet_to_allocate+=$yet_to_allocate;
											$booking_total_prev_stock+=$allocate_from_prev_st;
											$booking_total_booking_bal+=$booking_bal;
											
											$i++;
										}
									}
								}
							}

							if($booking_no_print_status[$book_no]==1)
							{
							    $grand_tot_book_qnty+=$booking_total_book_qnty; 
	                            $grand_tot_req_qnty+=$booking_total_req_qnty; 
	                            $grand_tot_wo_qnty+=$booking_total_wo_qnty; 
	                            
	                            $grand_tot_rcv_qnty+=$booking_total_rcv_qnty;
	                            $grand_tot_yet_to_rcv+=$booking_total_yet_to_rcv; 
	                            $grand_tot_alocate_qnty+=$booking_total_alocate_qnty; 
	                            $grand_tot_yet_to_allocate+=$booking_total_yet_to_allocate; 
	                            $grand_tot_prev_stock+=$booking_total_prev_stock;
	                            $grand_tot_booking_bal+=$booking_total_booking_bal; 

								$booking_total_book_qnty=0;
								$booking_total_req_qnty=0;
								$booking_total_wo_qnty=0;
								$booking_total_rcv_qnty=0;
								$booking_total_yet_to_rcv=0;
								$booking_total_alocate_qnty=0;
								$booking_total_yet_to_allocate=0;
								$booking_total_prev_stock=0;
								$booking_total_booking_bal=0;
							}
						}
						?>
	                    </tbody>				
	            </table> 
	        </div>
	        <table width="<?= $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table"  align="left">            	
	            <tfoot>
	            	<th width="30" align="right">&nbsp;</th>
	            	<th width="120" align="right">&nbsp;</th>
	            	<th width="100" align="right">&nbsp;</th>
	            	<th width="110" align="right">&nbsp;</th>
	            	<th width="70" align="right">&nbsp;</th>
	            	<th width="70" align="right">&nbsp;</th>
	            	<th width="70" align="right">&nbsp;</th>
	            	<th width="130" align="right">&nbsp;</th>
	            	<th width="70" align="right">&nbsp;</th>
	            	<th width="120" align="right">&nbsp;</th>
	            	<th width="70" align="right">&nbsp;</th>
	            	<th width="150" align="right">&nbsp;</th>
	            	<th width="80" align="right">&nbsp;</th> 
	                <th width="150" align="right">&nbsp;</th>
	                <th width="100" align="right">Grand Total :</th>  

	                <th width="80" align="right"><? echo number_format($grand_tot_book_qnty,2,'.','');?></th>  
	                <th width="80" align="right"><? echo number_format($grand_tot_req_qnty,2,'.','');?></th>
	                <th width="80" align="right"><? echo number_format($grand_tot_wo_qnty,2,'.','');?></th>
	                
	                <th width="70">&nbsp;</th>
	                <th width="70">&nbsp;</th>
	                <th width="70">&nbsp;</th>
	                <th width="70">&nbsp;</th>
	                <th width="70">&nbsp;</th>
	                <th width="70">&nbsp;</th>
	                <th width="70">&nbsp;</th>
	                <th width="70">&nbsp;</th>
	                
	                <th width="80" align="right"><? echo number_format($grand_tot_rcv_qnty,2,'.','');?></th> 
	                <th width="70">&nbsp;</th>  
	                <th width="80" align="right"><? echo number_format($grand_tot_yet_to_rcv,2,'.','');?></th>
	                <th width="80" align="right"><? echo number_format($grand_tot_alocate_qnty,2,'.','');?></th>
	                
	                <th width="70">&nbsp;</th>  
	                <th width="80" align="right"><? echo number_format($grand_tot_yet_to_allocate,2,'.','');?></th>
	                <th width="80" align="right"><? echo number_format($grand_tot_prev_stock,2,'.','');?></th>
	                <th width="80" align="right"><? echo number_format($grand_tot_booking_bal,2,'.','');?></th>
	                <th>&nbsp;</th>
	            </tfoot>
	        </table>
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
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}
<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.yarns.php');
require_once('../../../includes/class4/class.trims.php');
require_once('../../../includes/class4/class.emblishments.php');
require_once('../../../includes/class4/class.washes.php');
require_once('../../../includes/class4/class.fabrics.php');
require_once('../../../includes/class4/class.conversions.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
$permission=$_SESSION['page_permission'];


if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 170, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",0 );
	exit();
}


if($action=="check_report_button")
{
	$sql="select format_id from lib_report_template where template_name='".trim($data)."' and report_id=208 and is_deleted=0 and status_active=1";
	$data_array=sql_select($sql);
	if(count($data_array)>0)
	{
		echo $data_array[0][csf('format_id')];
	}
	else
	{
		echo "";
	}
	exit();
}


if($action==='file_popup')
{
	echo load_html_head_contents("LC Info", '../../../', 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array; 
		var selected_name = new Array;
		
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
			$('#hidden_file_id').val( id );
			$('#hidden_file_no').val( name );
            //alert(id);
		}
	
    </script>
    </head>
    <body>
    <div align="center">
        <form name="file_form" id="file_form">
        <input type="hidden" name="file_no" name="file_no">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Year</th>
                    <th>Buyer</th>
                    <th>File No</th>
                    <th>LC/SC No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('file_form','search_div','','','','');"></th>
                    <input type="hidden" name="hidden_file_no" id="hidden_file_no" value="" />
                    <input type="hidden" name="hidden_file_id" id="hidden_file_id" value="" />
                </thead>
                <tbody>
                	<tr>
                    <td>
	                    <?
                        
						$sql=sql_select("select lc_year as lc_sc_year from com_export_lc where beneficiary_name='$cbo_company_name' and status_active=1 and is_deleted=0  union all select sc_year as lc_sc_year from com_sales_contract where beneficiary_name='$cbo_company_name' and status_active=1 and is_deleted=0");
						foreach($sql as $row)
						{
							$lc_sc_year[$row[csf("lc_sc_year")]]=$row[csf("lc_sc_year")];
						}
						echo create_drop_down( "cbo_year", 100,$lc_sc_year,"", 1, "-- Select --",$cbo_year);
						?>
	                    </td>
                        <td align="center">
                        <?	
                            if($buyer_id){
                                $buyer_con ="and buy.id = $buyer_id";
                            }
                    		echo create_drop_down( 'cbo_buyer_name', 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1  and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_name' $buyer_con and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name",'id,buyer_name', 1, '-- All Buyer --', $selected, '' ,0);
                    	?>	
                        </td>
                        <td align="center">
                        	<input type="text" style="width:100px" class="text_boxes" name="txt_file_no" id="txt_file_no" value=""/>
                        </td>
                        <td align="center">
                        	<input type="text" style="width:100px" class="text_boxes" name="txt_lc_sc_no" id="txt_lc_sc_no" value=""/>
                        </td>      
                        <td>        
                        <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $cbo_company_name; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_file_no').value+'**'+document.getElementById('txt_lc_sc_no').value+'**'+document.getElementById('cbo_year').value, 'file_search_list_view', 'search_div',  'file_wise_export_lc_reconciliation_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;"/>  
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



if($action==='lc_popup')
{
	echo load_html_head_contents("LC Info", '../../../', 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array; 
		var selected_name = new Array;
		
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
			//alert(str[0]);return;
			 
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
			
			$('#hidden_lc_sc_id').val( id );
			$('#hidden_lc_sc_no').val( name );
		}
	
    </script>
    </head>
    <body>
    <div align="center">
        <form name="lcsc_form" id="lcsc_form">
        <input type="hidden" name="lc_sc_id" name="lc_sc_id">	
        <input type="hidden" name="lc_sc_no" name="lc_sc_no">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>File No</th>
                    <th>LC/SC No</th>
                    <th colspan="2">LC/SC Date</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('lcsc_form','search_div','','','','');"></th>
                    <input type="hidden" name="hidden_lc_sc_no" id="hidden_lc_sc_no" value="" />
                    <input type="hidden" name="hidden_lc_sc_id" id="hidden_lc_sc_id" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        <?	
                            if($buyer_id){
                                $buyer_con ="and buy.id = $buyer_id";
                            }
                    		echo create_drop_down( 'cbo_buyer_name', 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_name' $buyer_con  and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name",'id,buyer_name', 1, '-- All Buyer --', $selected, '' ,0);
                    	?>	
                        </td>
                        <td align="center">
                        	<input type="text" style="width:100px" class="text_boxes" name="txt_file_no" id="txt_file_no" value=""/>
                        </td>
                        <td align="center">
                        	<input type="text" style="width:100px" class="text_boxes" name="txt_lc_sc_no" id="txt_lc_sc_no" value=""/>
                        </td>
                        <td align="center">
                        	<td>
								<input name="txt_date_from" id="txt_date_from" class="datepicker"  style="width:75px" placeholder="From Date">
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:75px" placeholder="To Date">
							</td>
                        </td>               
                        <td align="center"> 
                            
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $cbo_company_name; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_file_no').value+'**'+document.getElementById('txt_lc_sc_no').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+'<?echo $file_nos;?>', 'lc_search_list_view', 'search_div', 'file_wise_export_lc_reconciliation_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;"/>
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

if($action==='file_search_list_view')
{		  
	echo load_html_head_contents('Popup Info','../../../', 1, 1, $unicode);
	list($company,$buyer_name,$file_no,$lc_sc_no,$cbo_year)=explode('**',$data);
	//echo $company.'***'.$buyer_name.'***'.$file_no.'***'.$lc_sc_no.'***'.$year ;die;
	$buyer_cond=$file_cond='';
	$lc_search_con=$sc_search_con=$lc_date_cond=$sc_date_cond='';
	if($buyer_name != 0) $buyer_cond=" and buyer_name=$buyer_name";
	if($file_no != '') $file_cond=" and internal_file_no like('%$file_no')";
    if($cbo_year!=0)
	{
		$year_cond_sc="and sc_year='$cbo_year'";
		$year_cond_lc="and lc_year='$cbo_year'";
	}
	$buyer_arr=return_library_array( "select ID, BUYER_NAME from lib_buyer",'ID','BUYER_NAME');

	if($lc_sc_no != '') 
	{ 
		$lc_search_con = " and export_lc_no like('%$lc_sc_no')";
		$sc_search_con = " and contract_no like('%$lc_sc_no')";
	}

	if($date_from != '' && $date_to != '')
	{
		if($db_type==0)
		{
			$date_from = date("Y-m-d", strtotime($date_from));
			$date_to   = date("Y-m-d", strtotime($date_to));
			$lc_date_cond = " and lc_date between '$date_from' and '$date_to'";
			$sc_date_cond = " and contract_date between '$date_from' and '$date_to'";
		}
		else
		{
			$date_from = date("d-M-Y", strtotime($date_from));
			$date_to   = date("d-M-Y", strtotime($date_to));
			$lc_date_cond = " and lc_date between '$date_from' and '$date_to'";
			$sc_date_cond = " and contract_date between '$date_from' and '$date_to'";
		}
	}

	$sql = "SELECT ID, BUYER_NAME, INTERNAL_FILE_NO ,EXPORT_LC_NO AS LC_SC_NO,  1 AS TYPE,LC_YEAR as LC_SC_YEAR from com_export_lc where beneficiary_name=$company and status_active=1 and is_deleted=0 $buyer_cond $file_cond $lc_search_con $lc_date_cond $year_cond_lc
    group by ID, BUYER_NAME, INTERNAL_FILE_NO, EXPORT_LC_NO ,LC_YEAR
    UNION ALL
    SELECT ID, BUYER_NAME, INTERNAL_FILE_NO, CONTRACT_NO AS LC_SC_NO, 2 AS TYPE,SC_YEAR as LC_SC_YEAR from com_sales_contract where beneficiary_name=$company and status_active=1 and is_deleted=0 $buyer_cond $file_cond $sc_search_con $sc_date_cond $year_cond_sc
    group by ID, BUYER_NAME, INTERNAL_FILE_NO, CONTRACT_NO ,SC_YEAR
    ORDER BY ID DESC";
    //echo $sql;
	$sql_res = sql_select($sql);
    $filearr=array();
    foreach ($sql_res as $row) 
    {
        $filearr[$row['INTERNAL_FILE_NO']]['ID'] = $row['ID'];
        $filearr[$row['INTERNAL_FILE_NO']]['BUYER_NAME'] = $row['BUYER_NAME'];
        $filearr[$row['INTERNAL_FILE_NO']]['INTERNAL_FILE_NO'] = $row['INTERNAL_FILE_NO'];
       // $filearr[$row['INTERNAL_FILE_NO']]['LC_SC_NO'] = $row['LC_SC_NO'];
        $filearr[$row['INTERNAL_FILE_NO']]['TYPE'] = $row['TYPE'];
        $filearr[$row['INTERNAL_FILE_NO']]['LC_SC_YEAR'] = $row['LC_SC_YEAR'];
        $filearr[$row['INTERNAL_FILE_NO']]['LC_SC_NO'] .= $row['LC_SC_NO'].',';

    }
	
	?>
	<div style="width:510px;">
        <table  width="510" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
            <thead>
                <th width="50">SL</th>
                <th width="50">Year</th>
                <th width="80">Buyer</th>
                <th width="140">File No</th>
                <th width="190">LC/SC No</th>
                <!-- <th width="50">LC/SC</th> -->
                <!-- <th>LC/SC Date</th> -->
            </thead> 
       </table>          
       <div style="width:510px; overflow-y:scroll; max-height:300px" id="scroll_body">                
       		<table class="rpt_table" width="510" cellpadding="0" cellspacing="0" id="tbl_list_search" border="1" rules="all">
       			<?
       			$i=1;
       			foreach ($filearr as $row) 
       			{
       				if (fmod($i,2)==0)  $bgcolor="#E9F3FF";
                    else $bgcolor="#FFFFFF";
                    if ($row['TYPE'] == 1) $is_lc_sc = 'LC';
                    else $is_lc_sc = 'SC';
       				?>
	       			<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $i.'_'.$row['ID'].'_'.$row['INTERNAL_FILE_NO']; ?>')">
	       				<td width="50"><? echo $i; ?></td>
	       				<td width="50"><? echo $row['LC_SC_YEAR']; ?></td> 
	                    <td width="80"><p><? echo $buyer_arr[$row['BUYER_NAME']]; ?></p></td>
	                    <td width="140"><p><? echo $row['INTERNAL_FILE_NO']; ?></p></td>
	                    <td width="190"><p><? echo $row['LC_SC_NO']; ?></p></td>
	                </tr>
	                <?
	                $i++;
	            }
	            ?>            
       		</table>
       	</div>
    </div>
    <br>
    <div style="width:45%; float:left" align="left">
    	<input type="checkbox" name="check_all_lcsc" id="check_all_lcsc" onClick="check_all_data()" value="0" />&nbsp;&nbsp;Check All
    </div>
    <div style="width:30%; float:left" align="left">
        <input type="" class="formbutton" id="close" style="width:80px" onClick="parent.emailwindow.hide();" value="Close"/>
    </div>  
    <?
    exit();
}

if($action==='lc_search_list_view')
{		  
	echo load_html_head_contents('Popup Info','../../../', 1, 1, $unicode);
	list($company,$buyer_name,$file_no,$lc_sc_no,$date_from,$date_to,$file_nos)=explode('**',$data);
	//echo $company.'***'.$buyer_name.'***'.$file_no.'***'.$lc_sc_no.'***'.$date_from.'***'.$date_to.'***'.$file_nos;die;
	$buyer_cond=$file_cond='';
	$lc_search_con=$sc_search_con=$lc_date_cond=$sc_date_cond='';
	if($buyer_name != 0) $buyer_cond=" and buyer_name=$buyer_name";
	if($file_no != '') $file_cond=" and internal_file_no like('%$file_no')";

	$buyer_arr=return_library_array( "select ID, BUYER_NAME from lib_buyer",'ID','BUYER_NAME');

	if($lc_sc_no != '') 
	{ 
		$lc_search_con = " and export_lc_no like('%$lc_sc_no')";
		$sc_search_con = " and contract_no like('%$lc_sc_no')";
	}

	if($date_from != '' && $date_to != '')
	{
		if($db_type==0)
		{
			$date_from = date("Y-m-d", strtotime($date_from));
			$date_to   = date("Y-m-d", strtotime($date_to));
			$lc_date_cond = " and lc_date between '$date_from' and '$date_to'";
			$sc_date_cond = " and contract_date between '$date_from' and '$date_to'";
		}
		else
		{
			$date_from = date("d-M-Y", strtotime($date_from));
			$date_to   = date("d-M-Y", strtotime($date_to));
			$lc_date_cond = " and lc_date between '$date_from' and '$date_to'";
			$sc_date_cond = " and contract_date between '$date_from' and '$date_to'";
		}
	}

 
    $internal_file_con .= ($file_nos != "") ? " AND INTERNAL_FILE_NO IN ('" . str_replace(",", "','", $file_nos) . "')" : "";
  
	$sql = "SELECT ID, BENEFICIARY_NAME, BUYER_NAME, INTERNAL_FILE_NO, EXPORT_LC_NO AS LC_SC_NO, LC_DATE AS LC_SC_DATE, 1 AS TYPE from com_export_lc where beneficiary_name=$company and status_active=1 and is_deleted=0 $buyer_cond $file_cond $lc_search_con $lc_date_cond $internal_file_con 
    UNION ALL
    SELECT ID, BENEFICIARY_NAME, BUYER_NAME, INTERNAL_FILE_NO, CONTRACT_NO AS LC_SC_NO, CONTRACT_DATE AS LC_SC_DATE, 2 AS TYPE from com_sales_contract where beneficiary_name=$company and status_active=1 and is_deleted=0 $buyer_cond $file_cond $sc_search_con $sc_date_cond $internal_file_con 
    ORDER BY lc_sc_date DESC";
	$sql_res = sql_select($sql);	
	
	?>
	<div style=" width:697px;">
        <table  width="700" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
            <thead>
                <th width="50">SL</th>
                <th width="150">Buyer</th>
                <th width="150">File No</th>
                <th width="150">LC/SC No</th>
                <th width="50">LC/SC</th>
                <th>LC/SC Date</th>
            </thead> 
       </table>          
       <div style="width:700px; overflow-y:scroll; max-height:300px" id="scroll_body">                
       		<table class="rpt_table" width="680" cellpadding="0" cellspacing="0" id="tbl_list_search" border="1" rules="all">
       			<?
       			$i=1;
       			foreach ($sql_res as $row) 
       			{
       				if (fmod($i,2)==0)  $bgcolor="#E9F3FF";
                    else $bgcolor="#FFFFFF";
                    if ($row['TYPE'] == 1) $is_lc_sc = 'LC';
                    else $is_lc_sc = 'SC';
       				?>
	       			<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $i.'_'.$row['ID'].'_'.$row['LC_SC_NO']; ?>')">
	       				<td width="50"><? echo $i; ?></td>
	                    <td width="150"><p><? echo $buyer_arr[$row['BUYER_NAME']]; ?></p></td>
	                    <td width="150"><p><? echo $row['INTERNAL_FILE_NO']; ?></p></td>
	                    <td width="150"><p><? echo $row['LC_SC_NO']; ?></p></td>
	                    <td width="50" align="center"><p><? echo $is_lc_sc; ?></p></td>
	                    <td align="center"><p><? echo change_date_format($row['LC_SC_DATE']); ?></p></td>
	                </tr>
	                <?
	                $i++;
	            }
	            ?>            
       		</table>
       	</div>
    </div>
    <br>
    <div style="width:45%; float:left" align="left">
    	<input type="checkbox" name="check_all_lcsc" id="check_all_lcsc" onClick="check_all_data()" value="0" />&nbsp;&nbsp;Check All
    </div>
    <div style="width:30%; float:left" align="left">
        <input type="" class="formbutton" id="close" style="width:80px" onClick="parent.emailwindow.hide();" value="Close"/>
    </div>  
    <?
    exit();
}


$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
$lein_bank_arr=return_library_array( "select bank_name,id from lib_bank where is_deleted=0  and status_active=1 and lien_bank=1 order by bank_name",'id','bank_name');
$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer where is_deleted=0  and status_active=1 order by buyer_name",'id','buyer_name');
$suplier_name_arr=return_library_array( "select id,supplier_name from  lib_supplier where is_deleted=0  and status_active=1 order by supplier_name",'id','supplier_name');
$pi_no_arr=return_library_array( "select id,pi_number from  com_pi_master_details where status_active=1",'id','pi_number');

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_based_on=str_replace("'","",$cbo_based_on);
	$cbo_company_name=str_replace("'","",$cbo_company_name); 
    $cbo_buyer_name=str_replace("'","",$cbo_buyer_name); 
    $cbo_lein_bank=str_replace("'","",$cbo_lein_bank); 
    $txt_file_id=str_replace("'","",$txt_file_id);
    $txt_file_no=str_replace("'","",$txt_file_no);
    $txt_lc_sc_id = trim(str_replace("'","",$txt_lc_sc_id));
	//echo $hide_year;die;
	//echo $cbo_company_name.'____'.$cbo_buyer_name.'____'.$cbo_lein_bank.'____'.$txt_file_id.'___'.$txt_lc_sc_id.'___'.$txt_file_no; die;
	if($cbo_company_name!=0) $cbo_company_name = $cbo_company_name; else $cbo_company_name="%%";
	if($cbo_buyer_name == 0) $cbo_buyer_name="%%"; else $cbo_buyer_name = $cbo_buyer_name;
	if($cbo_lein_bank == 0) $cbo_lein_bank="%%"; else $cbo_lein_bank = $cbo_lein_bank;
	if(trim($txt_file_id)!="") $txt_file_id =$txt_file_id; else $txt_file_id="%%";
	if(trim($hide_year)!="") $hide_year =$hide_year; else $hide_year="%%";	

    if ($txt_file_id != '')
	{
		//$lc_file_cond=" and a.id in($txt_file_id)";
		//$sc_file_cond=" and a.id in($txt_file_id)";
	} 

    if ($txt_lc_sc_id != '')
	{
		$lc_no_cond=" and a.id in($txt_lc_sc_id)";
		$sc_no_cond=" and a.id in($txt_lc_sc_id)";
	} 

    // if ($txt_file_no != '')
	// {
	// 	$file_no_cond=" and a.internal_file_no in ('$txt_file_no')";
	// }
    $file_no_cond = ($txt_file_no != "") ? " AND a.internal_file_no IN ('" . str_replace(",", "','", $txt_file_no) . "')" : "";

    if ($cbo_lein_bank>0)
	{
		$lein_bank_cond=" and a.lien_bank = $cbo_lein_bank";
	}

	$company_cond=$buyer_cond='';
	if ($cbo_company_name != 0) $company_cond=" and a.beneficiary_name=$cbo_company_name";
	if ($cbo_buyer_name != 0) $buyer_cond=" and a.buyer_name=$cbo_buyer_name";

    $company_arr= return_library_array("select ID, COMPANY_NAME from lib_company", 'ID','COMPANY_NAME');
	$buyer_arr  = return_library_array("select ID, SHORT_NAME from lib_buyer", 'ID','SHORT_NAME');
    $country_arr=return_library_array( "select id,country_name from lib_country", "id", "country_name");
    $count_arr=return_library_array( "SELECT ID, YARN_COUNT FROM LIB_YARN_COUNT",'ID','YARN_COUNT');
    $color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );

    $summ_main_sql = "SELECT a.id AS LS_SC_ID, a.export_lc_no AS LC_SC_NO, a.lc_date AS LC_SC_DATE, a.lc_value AS LC_SC_VALUE, a.BUYER_NAME, a.INTERNAL_FILE_NO, sum(b.invoice_quantity) 
    as INVOICE_QUANTITY, sum(b.invoice_value) as INVOICE_VALUE, sum(b.net_invo_value) as NET_INVO_VALUE,
    SUM (c.net_invo_value)  AS BILL_VAL,
    max(b.ex_factory_date) as EX_FACTORY_DATE, max(b.shipping_mode) as SHIPPING_MODE, d.id as SUB_ID, d.BANK_REF_NO, 
    d.SUBMIT_DATE, d.REMARKS, c.IS_LC,1 as TYPE, a.replacement_lc as REPLACEMENT_LC,null as CONVERTIBLE_TO_LC
    FROM com_export_lc a
    left join com_export_invoice_ship_mst b on  a.id=b.lc_sc_id and b.is_lc=1 and b.status_active=1 and b.is_deleted=0
    left join com_export_doc_submission_invo c on  b.id=c.invoice_id and c.is_lc=1 and c.status_active=1 and c.is_deleted=0
    left join com_export_doc_submission_mst d on  c.doc_submission_mst_id=d.id and d.entry_form=40 and d.status_active=1 and d.is_deleted=0 
    WHERE a.status_active=1 and a.is_deleted=0
    $company_cond $buyer_cond $file_no_cond $sc_no_cond $sc_file_cond $lein_bank_cond GROUP BY a.id, a.export_lc_no, a.lc_date, a.lc_value, a.buyer_name,a.INTERNAL_FILE_NO, d.id, d.bank_ref_no, d.submit_date, d.remarks, c.is_lc,a.replacement_lc
    UNION ALL 
    SELECT a.id as LS_SC_ID, a.contract_no as LC_SC_NO, a.contract_date as LC_SC_DATE, a.contract_value as LC_SC_VALUE, 
    a.BUYER_NAME,a.INTERNAL_FILE_NO, sum(b.invoice_quantity) as INVOICE_QUANTITY, sum(b.invoice_value) as INVOICE_VALUE, sum(b.net_invo_value) as NET_INVO_VALUE, SUM (c.net_invo_value)  AS BILL_VAL,
    max(b.ex_factory_date) as EX_FACTORY_DATE, max(b.shipping_mode) as SHIPPING_MODE, d.id as SUB_ID, d.BANK_REF_NO, 
    d.SUBMIT_DATE, d.REMARKS, c.IS_LC,2 as TYPE,null as  REPLACEMENT_LC,a.convertible_to_lc as CONVERTIBLE_TO_LC
    FROM com_sales_contract a
    left join com_export_invoice_ship_mst b on  a.id=b.lc_sc_id and b.is_lc=2 and b.status_active=1 and b.is_deleted=0
    left join com_export_doc_submission_invo c on  b.id=c.invoice_id and c.is_lc=2 and c.status_active=1 and c.is_deleted=0
    left join com_export_doc_submission_mst d on  c.doc_submission_mst_id=d.id and d.entry_form=40 and d.status_active=1 and d.is_deleted=0
    WHERE a.status_active=1 and a.is_deleted=0  
    $company_cond $buyer_cond $file_no_cond $lc_no_cond $sc_file_cond $lein_bank_cond
    GROUP BY a.id, a.contract_no, a.contract_date, a.contract_value, a.buyer_name,a.INTERNAL_FILE_NO, d.id, d.bank_ref_no, 
    d.submit_date, d.remarks, c.is_lc,a.convertible_to_lc order by submit_date desc ";
    //echo $summ_main_sql;

    $con = connect();
    execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ENTRY_FORM=106");//1=Job, 2=Po, 3=PI, 4=WO
    oci_commit($con);
    
    $summ_sql_res = sql_select($summ_main_sql);
    $sc_lc_id_rr=array();
    $bill_id_rr=array();
    $summArr=array();
    foreach ($summ_sql_res as  $row)
    {
        $summArr[$row['INTERNAL_FILE_NO']]['LS_SC_ID'] = $row['LS_SC_ID'];
        $summArr[$row['INTERNAL_FILE_NO']]['LC_SC_NO'] .= $row['LC_SC_NO'].",";
        $summArr[$row['INTERNAL_FILE_NO']]['LC_SC_DATE'] = $row['LC_SC_DATE'];
        $summArr[$row['INTERNAL_FILE_NO']]['LC_SC_VALUE'] = $row['LC_SC_VALUE'];
        $summArr[$row['INTERNAL_FILE_NO']]['BUYER_NAME'] = $row['BUYER_NAME'];
        $summArr[$row['INTERNAL_FILE_NO']]['INTERNAL_FILE_NO'] = $row['INTERNAL_FILE_NO'];
        $summArr[$row['INTERNAL_FILE_NO']]['INVOICE_QUANTITY'] += $row['INVOICE_QUANTITY'];
        $summArr[$row['INTERNAL_FILE_NO']]['INVOICE_VALUE'] += $row['INVOICE_VALUE'];
        $summArr[$row['INTERNAL_FILE_NO']]['NET_INVO_VALUE'] = $row['NET_INVO_VALUE'];
        $summArr[$row['INTERNAL_FILE_NO']]['BILL_VAL'] = $row['BILL_VAL'];
        $summArr[$row['INTERNAL_FILE_NO']]['EX_FACTORY_DATE'] = $row['EX_FACTORY_DATE'];
        $summArr[$row['INTERNAL_FILE_NO']]['SHIPPING_MODE'] = $row['SHIPPING_MODE'];
        $summArr[$row['INTERNAL_FILE_NO']]['SUB_ID'] = $row['SUB_ID'];
        $summArr[$row['INTERNAL_FILE_NO']]['BANK_REF_NO'] = $row['BANK_REF_NO'];
        $summArr[$row['INTERNAL_FILE_NO']]['SUBMIT_DATE'] = $row['SUBMIT_DATE'];
        $summArr[$row['INTERNAL_FILE_NO']]['REMARKS'] = $row['REMARKS'];
        $summArr[$row['INTERNAL_FILE_NO']]['IS_LC'] = $row['IS_LC'];
        $summArr[$row['INTERNAL_FILE_NO']]['TYPE'] = $row['TYPE'];
        $summArr[$row['INTERNAL_FILE_NO']]['REPLACEMENT_LC'] = $row['REPLACEMENT_LC'];
        $summArr[$row['INTERNAL_FILE_NO']]['CONVERTIBLE_TO_LC'] = $row['CONVERTIBLE_TO_LC'];
        $sc_lc_id_rr[$row['LS_SC_ID']]=$row['LS_SC_ID'];
        $bill_id_rr[$row['SUB_ID']]=$row['SUB_ID'];

    }

    if(!empty($sc_lc_id_rr))
    {
    fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 106, 1, $sc_lc_id_rr, $empty_arr);

    $sql_order = "SELECT b.id as ATTACH_ID, b.ATTACHED_QNTY, b.ATTACHED_VALUE,  b.com_export_lc_id as LC_SC_ID, 1 as TYPE,c.INTERNAL_FILE_NO
    FROM GBL_TEMP_ENGINE a, com_export_lc_order_info b,com_export_lc c
    WHERE a.ref_val=b.com_export_lc_id and b.COM_EXPORT_LC_ID = c.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and a.entry_form=106 and a.ref_from=1 and a.USER_ID = $user_id
    UNION 
    SELECT b.id as ATTACH_ID, b.ATTACHED_QNTY, b.ATTACHED_VALUE, b.com_sales_contract_id as LC_SC_ID, 2 as TYPE ,c.INTERNAL_FILE_NO
    FROM GBL_TEMP_ENGINE a, com_sales_contract_order_info b,com_sales_contract c
    WHERE a.ref_val=b.com_sales_contract_id and b.COM_SALES_CONTRACT_ID = c.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form=106 and a.ref_from=1 and a.USER_ID = $user_id";
    //echo $sql_order;//die;
    $sql_order_res = sql_select($sql_order);
    $order_arr=array();
    $order_attach_arr=array();
    foreach ($sql_order_res as $row)
    {
        if($attach_id_check[$row['ATTACH_ID']] == '')
        {     
            $order_attach_arr[$row['INTERNAL_FILE_NO']]['ATTACHED_QNTY']  += $row['ATTACHED_QNTY'];
            $order_attach_arr[$row['INTERNAL_FILE_NO']]['ATTACHED_VALUE'] += $row['ATTACHED_VALUE'];
            //  $order_attach_arr[$attach_key]['ATTACHED_QNTY']  += $row['ATTACHED_QNTY'];
            // $order_attach_arr[$attach_key]['ATTACHED_VALUE'] += $row['ATTACHED_VALUE'];
        }						
    }

    $file_no_cond2 = ($txt_file_no != "") ? " AND f.internal_file_no IN ('" . str_replace(",", "','", $txt_file_no) . "')" : "";

    $sql_po_brkdwn = "SELECT c.id as PO_ID, c.PO_NUMBER,c.JOB_NO_MST,c.PUB_SHIPMENT_DATE, d.STYLE_REF_NO,d.BUYER_NAME, b.id as ATTACH_ID, b.ATTACHED_QNTY, b.ATTACHED_VALUE,  b.com_export_lc_id as LC_SC_ID, 1 as TYPE,SUM(e.EX_FACTORY_QNTY) AS EX_FACTORY_QNTY,f.INTERNAL_FILE_NO
    FROM GBL_TEMP_ENGINE a, com_export_lc_order_info b,com_export_lc f, wo_po_details_master d ,wo_po_break_down c
    left join pro_ex_factory_mst e on c.id=e.po_break_down_id and e.is_deleted=0 and e.entry_form != 85 and e.status_active =1
    WHERE a.ref_val=b.com_export_lc_id and b.COM_EXPORT_LC_ID = f.id and b.wo_po_break_down_id=c.id and c.job_no_mst=d.job_no   and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1  and d.is_deleted=0 and f.status_active=1 and a.entry_form=106 and a.ref_from=1 and a.USER_ID = $user_id $file_no_cond2
    group by c.id, c.PO_NUMBER,c.JOB_NO_MST,c.PUB_SHIPMENT_DATE, d.STYLE_REF_NO,d.BUYER_NAME, b.id, b.ATTACHED_QNTY, b.ATTACHED_VALUE,  b.com_export_lc_id,f.INTERNAL_FILE_NO
    UNION ALL
    SELECT c.id as PO_ID, c.PO_NUMBER,c.JOB_NO_MST,c.PUB_SHIPMENT_DATE, d.STYLE_REF_NO,d.BUYER_NAME, b.id as ATTACH_ID, b.ATTACHED_QNTY, b.ATTACHED_VALUE, b.com_sales_contract_id as LC_SC_ID, 2 as TYPE, SUM(e.EX_FACTORY_QNTY) AS EX_FACTORY_QNTY,f.INTERNAL_FILE_NO
    FROM GBL_TEMP_ENGINE a, com_sales_contract_order_info b ,com_sales_contract f, wo_po_details_master d ,wo_po_break_down c
    left join pro_ex_factory_mst e on c.id=e.po_break_down_id and e.is_deleted=0 and e.entry_form != 85 and e.status_active =1
    WHERE a.ref_val=b.com_sales_contract_id and b.COM_SALES_CONTRACT_ID = f.id and b.wo_po_break_down_id=c.id and c.job_no_mst=d.job_no  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and f.status_active=1 and a.entry_form=106 and a.ref_from=1 and a.USER_ID = $user_id $file_no_cond2
    group by c.id, c.PO_NUMBER,c.JOB_NO_MST,c.PUB_SHIPMENT_DATE, d.STYLE_REF_NO,d.BUYER_NAME, b.id, b.ATTACHED_QNTY, b.ATTACHED_VALUE, b.com_sales_contract_id,f.INTERNAL_FILE_NO
    ";
    //echo $sql_po_brkdwn;
    $sql_po_brkdwn_result = sql_select($sql_po_brkdwn);
    foreach($sql_po_brkdwn_result as $row)
    {
        $ex_fac_qty[$row['INTERNAL_FILE_NO']]['EX_FACTORY_QNTY'] += $row['EX_FACTORY_QNTY'];
    }
 
    // $exfact_qty_arr = return_library_array("SELECT a.LC_SC_NO, sum(a.ex_factory_qnty) as ex_factory_qnty from pro_ex_factory_mst a,GBL_TEMP_ENGINE tmp 
    // where a.lc_sc_no=tmp.ref_val and tmp.entry_form=106 and tmp.user_id=$user_id and tmp.ref_from=1 and a.entry_form != 85 and a.status_active=1 and a.LC_SC_NO>0 
    // group by a.LC_SC_NO", 'LC_SC_NO', 'ex_factory_qnty');
    }
    if(!empty($sc_lc_id_rr))
    {
    
        $exp_sql = "SELECT C.ACCOUNT_HEAD,SUM(C.DOCUMENT_CURRENCY) AS DOCUMENT_CURRENCY,C.TYPE ,D.INTERNAL_FILE_NO
        FROM COM_EXPORT_DOC_SUBMISSION_INVO A,COM_EXPORT_PROCEED_REALIZATION B, COM_EXPORT_PROCEED_RLZN_DTLS C,COM_EXPORT_LC D, GBL_TEMP_ENGINE G 
        WHERE A.DOC_SUBMISSION_MST_ID = B.INVOICE_BILL_ID AND B.ID=C.MST_ID AND A.LC_SC_ID =D.ID  AND A.LC_SC_ID = G.REF_VAL AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0  AND D.STATUS_ACTIVE=1 AND D.IS_DELETED=0 AND G.ENTRY_FORM=106 AND G.USER_ID=$user_id AND G.REF_FROM=1 GROUP BY C.ACCOUNT_HEAD,C.TYPE,D.INTERNAL_FILE_NO 
        UNION ALL
        SELECT C.ACCOUNT_HEAD,SUM(C.DOCUMENT_CURRENCY) AS DOCUMENT_CURRENCY,C.TYPE ,D.INTERNAL_FILE_NO
        FROM COM_EXPORT_DOC_SUBMISSION_INVO A,COM_EXPORT_PROCEED_REALIZATION B, COM_EXPORT_PROCEED_RLZN_DTLS C,COM_SALES_CONTRACT D, GBL_TEMP_ENGINE G 
        WHERE A.DOC_SUBMISSION_MST_ID = B.INVOICE_BILL_ID AND B.ID=C.MST_ID AND A.LC_SC_ID =D.ID  AND A.LC_SC_ID = G.REF_VAL AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0  AND D.STATUS_ACTIVE=1 AND D.IS_DELETED=0 AND G.ENTRY_FORM=106 AND G.USER_ID=$user_id AND G.REF_FROM=1 GROUP BY C.ACCOUNT_HEAD,C.TYPE,D.INTERNAL_FILE_NO 
        ";
        //echo $exp_sql;
        $exp_sql_result=sql_select($exp_sql);
        $exp_data_arr = array();
        $exp_data_deduc_arr = array();
        foreach ($exp_sql_result as $row)
        {
            if($row['TYPE']==1)
            {
                $exp_data_arr[$row['INTERNAL_FILE_NO']][$row['ACCOUNT_HEAD']]['DOCUMENT_CURRENCY'] += $row['DOCUMENT_CURRENCY'];
            }
            else{
                $exp_data_deduc_arr[$row['INTERNAL_FILE_NO']][$row['TYPE']]['DOCUMENT_CURRENCY'] += $row['DOCUMENT_CURRENCY'];
            }
        }
    }
   
    $sales_ref3= sql_select("SELECT A.ID, A.CONTRACT_VALUE AS LC_SC_VAL, 1 AS TYPE,A.INTERNAL_FILE_NO
    from com_sales_contract a
    where a.converted_from!=0 and a.is_deleted='0' and a.status_active='1' $company_cond $buyer_cond $file_no_cond $sc_no_cond $lein_bank_cond
    union all
    select A.ID, A.LC_VALUE AS LC_SC_VAL, 2 AS TYPE,A.INTERNAL_FILE_NO
    from com_export_lc a
    where a.is_deleted='0' and a.status_active='1' and a.replacement_lc='1' $company_cond $buyer_cond $file_no_cond $sc_no_cond $lein_bank_cond");                                                              
    $sales_contract_id="";$sales_contract_number="";
    foreach($sales_ref3 as $row)  
    {
        $total_sales_contct_value_top[$row['INTERNAL_FILE_NO']] += $row['LC_SC_VAL'];
    }
    
    $sales_ref_finance= sql_select("SELECT A.ID, A.BUYER_NAME, A.CONTRACT_VALUE,A.INTERNAL_FILE_NO from com_sales_contract a where a.is_deleted='0' and a.status_active='1' $company_cond $buyer_cond $file_no_cond $sc_no_cond $lein_bank_cond and ( a.converted_from is null or a.converted_from=0)  and  a.convertible_to_lc!=2");
    $sc_id_arr=array();$lc_id_arr=array();
    foreach($sales_ref_finance as $sal_ref) 
    {
        $total_sales_contct_value_finance[$sal_ref['INTERNAL_FILE_NO']] += $sal_ref['CONTRACT_VALUE'];
    }

    $sales_direct= sql_select("SELECT A.ID, A.CONTRACT_VALUE,A.INTERNAL_FILE_NO
    FROM COM_SALES_CONTRACT A
    where a.status_active='1' and a.is_deleted='0'  $company_cond $buyer_cond $file_no_cond $sc_no_cond $lein_bank_cond and a.convertible_to_lc=2  and  ( converted_from is null or converted_from=0)  order by id");
    $total_direct_sc_val=array();
    foreach($sales_direct as $row)  
    {
        $total_direct_sc_val[$row['INTERNAL_FILE_NO']] += $row['CONTRACT_VALUE'];
    }

    $exp_ref3= sql_select("SELECT A.ID, A.LC_VALUE AS LC_SC_VAL, A.INTERNAL_FILE_NO
    FROM COM_EXPORT_LC a
    WHERE A.IS_DELETED='0' AND A.STATUS_ACTIVE='1' $company_cond $buyer_cond $file_no_cond $sc_no_cond $lein_bank_cond  and a.replacement_lc=2");
    $total_direct_lc_val= array();
    foreach($exp_ref3 as $row)  
    {  
        $total_direct_lc_val[$row['INTERNAL_FILE_NO']] += $row['LC_SC_VAL'];
    }

    ob_start();
    if($rpt_type==1)
    {
        ?>
        <div style="width:2400px;" id="scroll_body">
            <fieldset style="width:100%">
                <table width="2400" cellpadding="0" cellspacing="0" id="caption" align="left">
                    <tr>
                        <td align="center" width="100%" colspan="14" class="form_caption" ><strong style="font-size:18px">Company Name:<? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
                    </tr>
                    <tr>
                        <td align="center" width="100%" colspan="14" class="form_caption" ><strong style="font-size:18px"><? echo $report_title; ?></strong></td>
                    </tr>
                </table>
              
                <table width="2400" cellpadding="0" cellspacing="0" border="0" rules="all" id="" align="left">
                    <tr>
                        <td><strong style="font:Arial, Helvetica, sans-serif; font-size:16px;">Summary:</strong></td>
                    </tr>
                </table>
                <table width="2400" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" id="" align="left">
                    <thead>
                        <tr>
                            <th width="50" rowspan="2">Sl</th>
                            <th width="80" rowspan="2">Buyer</th>
                            <th width="100" rowspan="2">File No.</th>
                            <th width="120" rowspan="2">LC/SC No</th>
                            <th width="100" rowspan="2">LC/SC Value</th>
                            <th width="80" rowspan="2">Attached Qty (Pcs)</th>
                            <th width="80" rowspan="2">Attached Value</th>
                            <th width="80" rowspan="2">Fac. Exit Qty.</th>
                            <th width="80" rowspan="2">Inv Qty</th>
                            <th width="80" rowspan="2">Inv. Value</th>
                            <th width="80" rowspan="2">PO Vs Inv.Qty.</th>
                            <th width="80" rowspan="2">PO Vs Inv. Value</th>
                            <th width="80" rowspan="2">Bill Qty.</th>
                            <th width="80" rowspan="2">Bill Value</th>
                            <th colspan="9" >Export Procced Received Through</th>
                        </tr>
                        <tr>
                           
                            <th width="100">CD Account</th>
                            <th width="100">ERQ Accounts</th>
                            <th width="100">FC Margin Accounts</th>
                            <th width="80">FDR</th>
                            <th width="100">Foreign Commission</th>
                            <th width="100">Local Commission</th>
                            <th width="100">Loan Adjustment</th>
                            <!-- <th width="100">Charges</th> -->
                            <th width="100">Short Realisations</th> 
                        </tr>
                    </thead>
                </table>
                <table width="2400" rules="all" class="rpt_table" align="left" id="" border="1">
                    <tbody>
                        <?
                        $i=1;$m=1;
                        foreach ($summArr as $key => $row)
                        {
                            if (fmod($i,2)==0) $bgcolor='#E9F3FF';
                            else $bgcolor='#FFFFFF';

                            // if($row['TYPE']==1 && $row['REPLACEMENT_LC']==2){
                            //     $total_direct_lc_val += $row['LC_SC_VALUE'];
                            // }
                            // if($row['TYPE']==2 && $row['CONVERTIBLE_TO_LC']==2){ 
                            //     $total_direct_sc_val += $row['LC_SC_VALUE'];
                            // }
                            $balance=$total_sales_contct_value_finance[$row['INTERNAL_FILE_NO']]-$total_sales_contct_value_top[$row['INTERNAL_FILE_NO']];
                            if($balance<0) $balance=0;
                            
                            //echo $total_sales_contct_value_top[$row['INTERNAL_FILE_NO']]."_p_".$balance."_p_".$total_direct_sc_val[$row['INTERNAL_FILE_NO']]."_p_".$total_direct_lc_val[$row['INTERNAL_FILE_NO']]."<br>";

                            $file_value=$total_sales_contct_value_top[$row['INTERNAL_FILE_NO']]+$balance+$total_direct_sc_val[$row['INTERNAL_FILE_NO']]+$total_direct_lc_val[$row['INTERNAL_FILE_NO']];

                            $avg_unit_price = $row['INVOICE_VALUE']/$row['INVOICE_QUANTITY'];
                            $sub_id = $row['SUB_ID'];

                            $all_ord_id_arr=array_unique(explode(',',$row['ALL_ORDER_NO']));
                            $po_no=$po_garments='';
                            $po_attach_qnty=0;
                            foreach ($all_ord_id_arr as $po_id)
                            {                	
                                $po_no .= $order_arr[$po_id]['PO_NUMBER'].',';
                                $po_garments .= $garm_item_arr[$order_arr[$po_id]['GMTS_ITEM_ID']].',';
                                $po_attach_qnty += $order_arr[$po_id]['ATTACHED_QNTY'];
                            }
                            // $lc_key = $row['LS_SC_ID'].'*'.$row['TYPE'];
                            // $lc_po_attach_qnty = $order_attach_arr[$lc_key]['ATTACHED_QNTY'];
                            // $lc_po_attach_value = $order_attach_arr[$lc_key]['ATTACHED_VALUE'];
                            $lc_po_attach_qnty = $order_attach_arr[$row['INTERNAL_FILE_NO']]['ATTACHED_QNTY'];
                            $lc_po_attach_value = $order_attach_arr[$row['INTERNAL_FILE_NO']]['ATTACHED_VALUE'];

                            $po_no = rtrim($po_no,',');
                            $po_garments  = implode(',',array_unique(explode(',',rtrim($po_garments,','))));
                            $inv_date_arr = array_unique(explode(',',$row['INVOICE_DATE']));
                            $all_inv_date = '';
                            foreach ($inv_date_arr as $inv_date)
                            {                	
                                $all_inv_date .= change_date_format($inv_date).',';
                            }
                            $all_inv_date = rtrim($all_inv_date,',');

                            $realized_value  = $realization_arr[$sub_id][1];
                            $short_realized  = $realization_arr[$sub_id][0];
                            $excess_ship_qty = $po_attach_qnty-$row['INVOICE_QUANTITY'];
                            $lc_sc_date      = change_date_format($row['LC_SC_DATE']);
                            $lc_sc_value     = $row['LC_SC_VALUE'];
                            $buyer_name      = $buyer_arr[$row['BUYER_NAME']];

                            ?>
                                <tr class=""  bgcolor="<? echo $bgcolor; ?>">
			                        <td width="50" align="center"><p><?=$i;?></p></td>
			                        <td width="80" align="left"> <p><? echo $buyer_name; ?></p></td>
                                    <td width="100" align="left"><p><?echo $row['INTERNAL_FILE_NO'];?></p></td>
                                    <td width="120" align="left"><p><?echo implode(',',array_unique(explode(',',rtrim($row['LC_SC_NO'],',')))); //echo $row['LC_SC_NO'];?></p></td>
                                    <td width="100" align="right"><p><?echo number_format($file_value,2);?></p></td>
                                    <td width="80" align="right"><p><?echo number_format($lc_po_attach_qnty,2);?></p></td>
                                    <td width="80" align="right"><p><?echo number_format($lc_po_attach_value,2);?></p></td>

                                    <td width="80" align="right"><p><?
                                    echo number_format($ex_fac_qty[$row['INTERNAL_FILE_NO']]['EX_FACTORY_QNTY'],2)
                                    //number_format($exfact_qty_arr[$row['LS_SC_ID']],2);?></p></td>
                                    <td width="80"  align="right"><p><?  echo number_format($row['INVOICE_QUANTITY'],2);?></p></td>
                                    <td width="80"  align="right"><p><?echo number_format($row['INVOICE_VALUE'],2);?></p></td>
                                    <td width="80"  align="right" title="(PO Atch. Qty - Inv. Qty)"><p><?
                                    $diff_qty = ($lc_po_attach_qnty-$row['INVOICE_QUANTITY']);
                                    echo number_format($diff_qty,2);
                                    ?></p>
                                    </td>
                                    <td width="80"  align="right" title="(PO Atch. Value - Inv. Value)"> <p><?
                                    $diff_val = $lc_po_attach_value - $row['INVOICE_VALUE'];
                                    echo number_format($diff_val,2);
                                    ?></p></td>
                                    
                                    <td width="80"  align="right"><p><?
                                        if($row['BILL_VAL']>0){
                                          echo number_format($row['INVOICE_QUANTITY'],2);
                                        }
                                        else echo "0.00";
                                        ?></p></td>
                                    <td width="80"  align="right"><p><?echo number_format($row['BILL_VAL'],2);?></p></td>


                                    <td width="100" align="right"><p><?
                                   // print_r($commercial_head[10]);
                                    echo number_format($exp_data_arr[$row['INTERNAL_FILE_NO']][10]['DOCUMENT_CURRENCY'],2); //cd account =10
                                    ?></p></td>
                                    <td width="100" align="right"><p><?
                                    echo number_format($exp_data_arr[$row['INTERNAL_FILE_NO']][6]['DOCUMENT_CURRENCY'],2); //ERQ Account =6
                                    ?></p></td>
                                    <td width="100" align="right"><p><?
                                    echo number_format($exp_data_arr[$row['INTERNAL_FILE_NO']][5]['DOCUMENT_CURRENCY'],2); //BTB Margin/DFC/BLO/DAD/RAD/FBPAR A/C ==5
                                    ?></p></td>
                                    <td width="80" align="right"><p><?
                                    echo number_format($exp_data_arr[$row['INTERNAL_FILE_NO']][187]['DOCUMENT_CURRENCY'],2); // FDR Build Up = 187
                                    ?></p></td>     
                                    <td width="100" align="right"><p><?
                                    echo number_format($exp_data_arr[$row['INTERNAL_FILE_NO']][61]['DOCUMENT_CURRENCY'],2); // Foreign Commission = 61
                                    ?></p></td>  
                                    <td width="100" align="right"><p><?
                                    echo number_format($exp_data_arr[$row['INTERNAL_FILE_NO']][62]['DOCUMENT_CURRENCY'],2); // Local  Commission = 62
                                    ?></p></td>  
                                    <td width="100" align="right"><p><?
                                    echo number_format($exp_data_arr[$row['INTERNAL_FILE_NO']][95]['DOCUMENT_CURRENCY'],2); // Loan A/C = 95
                                    ?></p></td> 
                                    <!-- <td width="100" align="right"><p><?
                                    // echo number_format($exp_data_arr[$row['INTERNAL_FILE_NO']][60]['DOCUMENT_CURRENCY'],2); // Other Charge = 60
                                    ?></p></td>  -->
                                    <td width="100" align="right"><p><?
                                    echo number_format($exp_data_deduc_arr[$row['INTERNAL_FILE_NO']][0]['DOCUMENT_CURRENCY'],2); // All deduction
                                    ?></p></td> 
                                </tr>
                            <?
                                 $i++;
                                 $total_lc_val += $file_value;
                                 $total_atc_qty += $lc_po_attach_qnty;
                                 $total_atc_val += $lc_po_attach_value;
                                 $total_exf_qty += $ex_fac_qty[$row['INTERNAL_FILE_NO']]['EX_FACTORY_QNTY'];//$exfact_qty_arr[$row['LS_SC_ID']];
                                 $total_inv_qty += $row['INVOICE_QUANTITY'];
                                 $total_inv_val += $row['INVOICE_VALUE'];
                                 $total_diff_qty += $diff_qty;
                                 $total_diff_val += $diff_val;
                                 $total_bill_qty += $row['INVOICE_QUANTITY'];
                                 $total_bill_val += $row['INVOICE_VALUE'];

                                 $total_cd_qty += $exp_data_arr[$row['INTERNAL_FILE_NO']][10]['DOCUMENT_CURRENCY'];
                                 $total_eqr_qty += $exp_data_arr[$row['INTERNAL_FILE_NO']][6]['DOCUMENT_CURRENCY'];
                                 $total_fcad_qty += $exp_data_arr[$row['INTERNAL_FILE_NO']][5]['DOCUMENT_CURRENCY'];
                                 $total_fdr_qty += $exp_data_arr[$row['INTERNAL_FILE_NO']][187]['DOCUMENT_CURRENCY'];
                                 $total_for_com_qty += $exp_data_arr[$row['INTERNAL_FILE_NO']][61]['DOCUMENT_CURRENCY'];
                                 $total_loc_com_qty += $exp_data_arr[$row['INTERNAL_FILE_NO']][62]['DOCUMENT_CURRENCY'];
                                 $total_loan_qty += $exp_data_arr[$row['INTERNAL_FILE_NO']][95]['DOCUMENT_CURRENCY'];
                                //  $total_other_ch_qty += $exp_data_arr[$row['INTERNAL_FILE_NO']][60]['DOCUMENT_CURRENCY'];
                                 $total_short_rel_qty += $exp_data_deduc_arr[$row['INTERNAL_FILE_NO']][0]['DOCUMENT_CURRENCY'];
                                 
                        }
                          
                      
                        ?>
                    </tbody>
                </table>

                <table width="2400" rules="all" border="1" class="rpt_table" align="left" style="margin-bottom:20px;">
                    <tfoot>
                        <tr align="right">
                            
                            <th width="353"> Total:</th>
                            <th width="100"><p><? echo number_format($total_lc_val,2)?></p></th>
                            <th width="80"><p><? echo number_format($total_atc_qty,2)?></p></th>
                            <th width="80"><p><? echo number_format($total_atc_val,2)?></p></th>
                            <th width="80"><p><? echo number_format($total_exf_qty,2)?></p></th>
                            <th width="80"><p><? echo number_format($total_inv_qty,2)?></p></th>
                            <th width="80"><p><? echo number_format($total_inv_val,2)?></p></th>
                            <th width="80"><p><? echo number_format($total_diff_qty,2)?></p></th>
                            <th width="80"><p><? echo number_format($total_diff_val,2)?></p></th>
                            <th width="80"><p><? echo number_format($total_bill_qty,2)?></p></th>
                            <th width="80"><p><? echo number_format($total_bill_val,2)?></p></th>

                            <th width="100"><p><? echo number_format($total_cd_qty,2)?></p></th>
                            <th width="100"><p><? echo number_format($total_eqr_qty,2)?></p></th>
                            <th width="100"><p><? echo number_format($total_fcad_qty,2)?></p></th>
                            <th width="80"><p><? echo number_format($total_fdr_qty,2)?></p></th>
                            <th width="100"><p><? echo number_format($total_for_com_qty,2)?></p></th>
                            <th width="100"><p><? echo number_format($total_loc_com_qty,2)?></p></th>
                            <th width="100"><p><? echo number_format($total_loan_qty,2)?></p></th>
                            <!-- <th width="100"><p><? echo number_format($total_other_ch_qty,2)?>&nbsp;</p></th> -->
                            <th width="100"><p><? echo number_format($total_short_rel_qty,2)?></p></th>
                        </tr>
                    </tfoot>
                </table>

                <!-- Yarn BTB Details Start: -->

                <?
                // $main_sql="SELECT A.IMPORT_MST_ID, A.CURRENT_DISTRIBUTION, A.LC_SC_ID, B.LC_VALUE, B.LC_NUMBER,d.ITEM_CATEGORY_ID, 1 as TYPE ,B.LC_DATE,B.ITEM_BASIS_ID,B.TOLERANCE,D.PI_NUMBER,E.ITEM_DESCRIPTION,e.QUANTITY,e.NET_PI_RATE,e.NET_PI_AMOUNT,e.WORK_ORDER_DTLS_ID,e.COUNT_NAME,E.YARN_COMPOSITION_PERCENTAGE1,E.YARN_TYPE,e.ID as PI_DTLS_ID,E.PI_ID,E.COLOR_ID
                // from COM_BTB_EXPORT_LC_ATTACHMENT A, COM_BTB_LC_MASTER_DETAILS B ,COM_BTB_LC_PI C,COM_PI_MASTER_DETAILS d,COM_PI_ITEM_DETAILS e,GBL_TEMP_ENGINE G
                // where A.LC_SC_ID =G.REF_VAL and A.IMPORT_MST_ID=B.ID 
                // AND B.ID = C.COM_BTB_LC_MASTER_DETAILS_ID
                // and C.PI_ID =d.id 
                // and d.id = e.PI_ID and A.IS_LC_SC=0 AND b.importer_id = $cbo_company_name
                // and B.LC_NUMBER IS NOT NULL  and A.status_active=1 and A.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 AND G.ENTRY_FORM=106 AND G.REF_FROM=1 AND g.USER_ID=$user_id 
                // union all
                // select A.IMPORT_MST_ID, A.CURRENT_DISTRIBUTION, A.LC_SC_ID, B.LC_VALUE, B.LC_NUMBER,d.ITEM_CATEGORY_ID, 2 as TYPE ,B.LC_DATE,B.ITEM_BASIS_ID,B.TOLERANCE,d.PI_NUMBER,e.ITEM_DESCRIPTION,e.QUANTITY,e.NET_PI_RATE,e.NET_PI_AMOUNT,e.WORK_ORDER_DTLS_ID,e.COUNT_NAME,E.YARN_COMPOSITION_PERCENTAGE1,E.YARN_TYPE,e.ID as PI_DTLS_ID,E.PI_ID,E.COLOR_ID
                // from COM_BTB_EXPORT_LC_ATTACHMENT A, COM_BTB_LC_MASTER_DETAILS B ,COM_BTB_LC_PI C,COM_PI_MASTER_DETAILS d,COM_PI_ITEM_DETAILS e,GBL_TEMP_ENGINE G
                // where A.LC_SC_ID = G.REF_VAL and A.IMPORT_MST_ID=B.ID 
                // AND B.ID = C.COM_BTB_LC_MASTER_DETAILS_ID
                // and C.PI_ID =d.id 
                // and d.id = e.PI_ID and A.IS_LC_SC=1 AND b.importer_id = $cbo_company_namPro Forma Invoice V2e
                // and B.LC_NUMBER IS NOT NULL  and A.status_active=1 and A.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 AND G.ENTRY_FORM=106 AND G.REF_FROM=1 AND g.USER_ID=$user_id ";
        

                $main_sql="SELECT A.IMPORT_MST_ID, A.CURRENT_DISTRIBUTION, A.LC_SC_ID, B.LC_VALUE, B.LC_NUMBER,d.ITEM_CATEGORY_ID, 1 as TYPE ,B.LC_DATE,B.ITEM_BASIS_ID,B.TOLERANCE,D.PI_NUMBER,E.ITEM_DESCRIPTION,e.QUANTITY,e.NET_PI_RATE,e.NET_PI_AMOUNT,e.WORK_ORDER_DTLS_ID,e.COUNT_NAME,E.YARN_COMPOSITION_PERCENTAGE1,E.YARN_TYPE,e.ID as PI_DTLS_ID,E.PI_ID,E.COLOR_ID,d.PI_BASIS_ID
                from GBL_TEMP_ENGINE G, COM_BTB_EXPORT_LC_ATTACHMENT A, COM_BTB_LC_MASTER_DETAILS B 
                left join COM_BTB_LC_PI C on B.ID = C.COM_BTB_LC_MASTER_DETAILS_ID 
                left join  COM_PI_MASTER_DETAILS d on C.PI_ID =d.id and d.status_active=1 and d.is_deleted=0
                left join  COM_PI_ITEM_DETAILS e on d.id = e.PI_ID and e.status_active=1 and e.is_deleted=0
                where A.LC_SC_ID =G.REF_VAL and A.IMPORT_MST_ID=B.ID  and A.IS_LC_SC=0 AND b.importer_id = $cbo_company_name
                and B.LC_NUMBER IS NOT NULL  and A.status_active=1 and A.is_deleted=0  AND G.ENTRY_FORM=106 AND G.REF_FROM=1 AND g.USER_ID=$user_id 
                union all
                select A.IMPORT_MST_ID, A.CURRENT_DISTRIBUTION, A.LC_SC_ID, B.LC_VALUE, B.LC_NUMBER,d.ITEM_CATEGORY_ID, 2 as TYPE ,B.LC_DATE,B.ITEM_BASIS_ID,B.TOLERANCE,d.PI_NUMBER,e.ITEM_DESCRIPTION,e.QUANTITY,e.NET_PI_RATE,e.NET_PI_AMOUNT,e.WORK_ORDER_DTLS_ID,e.COUNT_NAME,E.YARN_COMPOSITION_PERCENTAGE1,E.YARN_TYPE,e.ID as PI_DTLS_ID,E.PI_ID,E.COLOR_ID,d.PI_BASIS_ID
                from GBL_TEMP_ENGINE G, COM_BTB_EXPORT_LC_ATTACHMENT A, COM_BTB_LC_MASTER_DETAILS B 
                left join COM_BTB_LC_PI C on B.ID = C.COM_BTB_LC_MASTER_DETAILS_ID 
                left join  COM_PI_MASTER_DETAILS d on C.PI_ID =d.id and d.status_active=1 and d.is_deleted=0
                left join  COM_PI_ITEM_DETAILS e on d.id = e.PI_ID and e.status_active=1 and e.is_deleted=0
                where A.LC_SC_ID =G.REF_VAL and A.IMPORT_MST_ID=B.ID and A.IS_LC_SC=1 AND b.importer_id = $cbo_company_name
                and B.LC_NUMBER IS NOT NULL  and A.status_active=1 and A.is_deleted=0  AND G.ENTRY_FORM=106 AND G.REF_FROM=1 AND g.USER_ID=$user_id ";
                //echo $main_sql;die;
                $main_sql_result=sql_select($main_sql);     
                // echo "<pre>";
                //     print_r($main_sql_result);
                // echo "</pre>";
                $mst_id_arr=array();
                $pi_id_arr=array();
                $wo_dtls_id_arr=array();
                $row_count=array();
                $btb_yarn_arr=array(); $lc_nmbr_arr=array();
                foreach ($main_sql_result as $row)
                {                     
                    $btb_yarn_arr[$row['IMPORT_MST_ID']][$row['PI_DTLS_ID']]['IMPORT_MST_ID'] = $row['IMPORT_MST_ID'];
                    $btb_yarn_arr[$row['IMPORT_MST_ID']][$row['PI_DTLS_ID']]['CURRENT_DISTRIBUTION'] = $row['CURRENT_DISTRIBUTION'];
                    $btb_yarn_arr[$row['IMPORT_MST_ID']][$row['PI_DTLS_ID']]['LC_SC_ID'] = $row['LC_SC_ID'];
                    $btb_yarn_arr[$row['IMPORT_MST_ID']][$row['PI_DTLS_ID']]['LC_VALUE'] = $row['LC_VALUE'];
                    $btb_yarn_arr[$row['IMPORT_MST_ID']][$row['PI_DTLS_ID']]['LC_NUMBER'] = $row['LC_NUMBER'];
                    array_push($lc_nmbr_arr,$row['LC_NUMBER']);
                    $btb_yarn_arr[$row['IMPORT_MST_ID']][$row['PI_DTLS_ID']]['ITEM_CATEGORY_ID'] = $row['ITEM_CATEGORY_ID'];
                    $btb_yarn_arr[$row['IMPORT_MST_ID']][$row['PI_DTLS_ID']]['TYPE'] = $row['TYPE'];
                    $btb_yarn_arr[$row['IMPORT_MST_ID']][$row['PI_DTLS_ID']]['LC_DATE'] = $row['LC_DATE'];
                    $btb_yarn_arr[$row['IMPORT_MST_ID']][$row['PI_DTLS_ID']]['ITEM_BASIS_ID'] = $row['ITEM_BASIS_ID'];
                    $btb_yarn_arr[$row['IMPORT_MST_ID']][$row['PI_DTLS_ID']]['TOLERANCE'] = $row['TOLERANCE'];
                    $btb_yarn_arr[$row['IMPORT_MST_ID']][$row['PI_DTLS_ID']]['PI_NUMBER'] = $row['PI_NUMBER'];
                    $btb_yarn_arr[$row['IMPORT_MST_ID']][$row['PI_DTLS_ID']]['ITEM_DESCRIPTION'] = $row['ITEM_DESCRIPTION'];
                    $btb_yarn_arr[$row['IMPORT_MST_ID']][$row['PI_DTLS_ID']]['QUANTITY'] = $row['QUANTITY'];
                    $btb_yarn_arr[$row['IMPORT_MST_ID']][$row['PI_DTLS_ID']]['NET_PI_RATE'] = $row['NET_PI_RATE'];
                    $btb_yarn_arr[$row['IMPORT_MST_ID']][$row['PI_DTLS_ID']]['NET_PI_AMOUNT'] = $row['NET_PI_AMOUNT'];
                    $btb_yarn_arr[$row['IMPORT_MST_ID']][$row['PI_DTLS_ID']]['WORK_ORDER_DTLS_ID'] = $row['WORK_ORDER_DTLS_ID'];
                    $btb_yarn_arr[$row['IMPORT_MST_ID']][$row['PI_DTLS_ID']]['COLOR_ID'] = $row['COLOR_ID'];
                    $btb_yarn_arr[$row['IMPORT_MST_ID']][$row['PI_DTLS_ID']]['COUNT_NAME'] = $row['COUNT_NAME'];
                    $btb_yarn_arr[$row['IMPORT_MST_ID']][$row['PI_DTLS_ID']]['YARN_COMPOSITION_PERCENTAGE1'] = $row['YARN_COMPOSITION_PERCENTAGE1'];
                    $btb_yarn_arr[$row['IMPORT_MST_ID']][$row['PI_DTLS_ID']]['YARN_TYPE'] = $row['YARN_TYPE'];
                    $btb_yarn_arr[$row['IMPORT_MST_ID']][$row['PI_DTLS_ID']]['PI_DTLS_ID'] = $row['PI_DTLS_ID'];
                    $btb_yarn_arr[$row['IMPORT_MST_ID']][$row['PI_DTLS_ID']]['PI_ID'] = $row['PI_ID'];
                    $btb_yarn_arr[$row['IMPORT_MST_ID']][$row['PI_DTLS_ID']]['PI_BASIS_ID'] = $row['PI_BASIS_ID'];
                    $mst_id_arr[$row['IMPORT_MST_ID']]=$row['IMPORT_MST_ID'];
                    $pi_id_arr[$row['PI_ID']]=$row['PI_ID'];
                    $wo_dtls_id_arr[$row['WORK_ORDER_DTLS_ID']]=$row['WORK_ORDER_DTLS_ID'];
                    
                    if($row['ITEM_CATEGORY_ID']==4)
                    {
                        $pi_nos[$row['IMPORT_MST_ID']] .= $row['PI_NUMBER'].",";
                        $acc_lc_btb_id .= $row['IMPORT_MST_ID'].',';
                        $row_count[$row['LC_SC_ID']]++;
                        $categoryArr[$row['IMPORT_MST_ID']] =$row['ITEM_CATEGORY_ID']; 
                    }
                    elseif($row['ITEM_CATEGORY_ID']!=4 && $row['ITEM_CATEGORY_ID']!=1)
                    {
                        $pi_nos[$row['IMPORT_MST_ID']] .= $row['PI_NUMBER'].",";
                        $other_lc_btb_id .= $row['IMPORT_MST_ID'].',';
                        $row_count[$row['LC_SC_ID']]++;
                        $categoryArr[$row['IMPORT_MST_ID']] =$row['ITEM_CATEGORY_ID'];
                    }      
                }
                $lc_nmbr_cnt_arr=array_count_values($lc_nmbr_arr);
                // echo "<pre>";
                //     print_r($lc_nmbr_cnt_arr);
                // echo "</pre>";
                $rowspna_arr = array();
                foreach ($btb_yarn_arr as $mst_key => $row_mst) 
                {
                    $row_no=0;
                    foreach ($row_mst as $pi_key => $row) 
                    {
                        $row_no++;
                        //$rowspna_arr[$mst_key][$pi_key] = $row['LC_NUMBER']  ;
                         $rowspna_arr[$row['LC_NUMBER']]['LC']++;
                    }
                    $job_td_span[$mst_key] = $row_no;
                    
                }
            //    echo "<pre>";
            //    print_r($rowspna_arr);die;
              
                //$all_pi_nos = ltrim(implode(",", array_unique(explode(",", chop($pi_nos, ",")))), ',');
                $all_acc_lc_btb_id = ltrim(implode(",", array_unique(explode(",", chop($acc_lc_btb_id, ",")))), ',');
                $all_other_lc_btb_id = ltrim(implode(",", array_unique(explode(",", chop($other_lc_btb_id, ",")))), ',');
        

                if(!empty($pi_id_arr))
                {
                    fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 106, 3, $pi_id_arr, $empty_arr);     
                    $rcv_sql="SELECT  A.LC_NO,a.BOOKING_ID,a.ITEM_CATEGORY, B.ORDER_QNTY, B.ORDER_RATE, B.ORDER_AMOUNT, B.CONS_AMOUNT,MAX(A.RECEIVE_DATE) AS RCV_DATE,C.COLOR,C.YARN_TYPE,C.YARN_COUNT_ID
                    FROM INV_RECEIVE_MASTER A,INV_TRANSACTION B,product_details_master C ,GBL_TEMP_ENGINE G
                    WHERE A.BOOKING_ID = G.REF_VAL AND a.id=b.mst_id AND b.prod_id=c.id AND  b.transaction_type=1 AND a.entry_form=248 AND b.item_category=1 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0  AND G.ENTRY_FORM=106 AND G.USER_ID=$user_id AND G.REF_FROM=3 
                    GROUP BY  A.LC_NO,A.BOOKING_ID,a.ITEM_CATEGORY, B.ORDER_QNTY, B.ORDER_RATE, B.ORDER_AMOUNT, B.CONS_AMOUNT , A.RECEIVE_DATE,C.COLOR,C.YARN_TYPE,C.YARN_COUNT_ID";
                    // echo $rcv_sql;
                    $rcv_sql_result=sql_select($rcv_sql);
                    $exp_data_arr = array();
                    foreach ($rcv_sql_result as $row)
                    {
                        $in_house_arr[$row['BOOKING_ID']][$row['YARN_COUNT_ID']][$row['YARN_TYPE']][$row['COLOR']]['ORDER_QNTY'] = $row['ORDER_QNTY']; 
                        $in_house_arr[$row['BOOKING_ID']][$row['YARN_COUNT_ID']][$row['YARN_TYPE']][$row['COLOR']]['ORDER_AMOUNT'] = $row['ORDER_AMOUNT']; 
                        $in_house_arr[$row['BOOKING_ID']][$row['YARN_COUNT_ID']][$row['YARN_TYPE']][$row['COLOR']]['RCV_DATE'] = $row['RCV_DATE']; 
                    }     
                    
                    $sql_acc_inhouse = "SELECT a.id, a.booking_id, sum(a.amount) as AMOUNT,b.LC_NUMBER
                    from inv_trims_entry_dtls a,COM_BTB_LC_PI C,COM_BTB_LC_MASTER_DETAILS b , GBL_TEMP_ENGINE G
                    where A.BOOKING_ID = G.REF_VAL and A.BOOKING_ID = c.PI_ID  AND C.COM_BTB_LC_MASTER_DETAILS_ID =B.ID and  A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 and b.STATUS_ACTIVE=1 and c.STATUS_ACTIVE=1  AND G.ENTRY_FORM=106 AND G.USER_ID=$user_id AND G.REF_FROM=3 
                    group by a.id, a.booking_id,b.LC_NUMBER";
                    //echo $sql_acc_inhouse; //die();
                    $sql_acc_inhouse_result=sql_select($sql_acc_inhouse);
                    $in_house_acc_oth_arr = array();
                    foreach ($sql_acc_inhouse_result as $row)
                    {
                        $in_house_acc_oth_arr[$row['LC_NUMBER']]['AMOUNT'] = $row['AMOUNT']; 
                       
                    }   

                }
                if(!empty($mst_id_arr))
                {
                    fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 106, 5, $mst_id_arr, $empty_arr);   
                    $inv_sql = "SELECT A.CURRENT_ACCEPTANCE_VALUE,b.BTB_LC_ID, B.INVOICE_NO,b.INVOICE_DATE
                    FROM  COM_IMPORT_INVOICE_DTLS A, COM_IMPORT_INVOICE_MST B ,GBL_TEMP_ENGINE G
                    WHERE A.BTB_LC_ID = G.REF_VAL AND a.import_invoice_id = b.id AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0   AND G.ENTRY_FORM=106 AND G.USER_ID=$user_id AND G.REF_FROM=5";
                
                    // echo $inv_sql;
                    $inv_sql_result=sql_select($inv_sql);
                    $accp_arr = array();
                    foreach ($inv_sql_result as $row)
                    {
                        $accp_arr[$row['BTB_LC_ID']]['INVOICE_NO'] .= $row['INVOICE_NO']."<br>"; 
                        $accp_arr[$row['BTB_LC_ID']]['CURRENT_ACCEPTANCE_VALUE'] += $row['CURRENT_ACCEPTANCE_VALUE'];  
                        $accp_arr[$row['BTB_LC_ID']]['INVOICE_DATE'] .= change_date_format($row['INVOICE_DATE'])."<br>";  
                    }

                    $mrr_qnty = "SELECT a.id,c.PI_ID, sum(b.order_qnty) as RCV_QNTY 
                    from inv_receive_master a, inv_transaction b,COM_IMPORT_INVOICE_DTLS_MRR c,GBL_TEMP_ENGINE G
                    where c.pi_id = G.REF_VAL and a.id=b.mst_id and a.id=c.mrr_id and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
                    and b.payment_over_recv=0 and a.receive_basis=1 AND G.ENTRY_FORM=106 AND G.USER_ID=$user_id AND G.REF_FROM=3 
                    group by a.id, c.PI_ID";
                    //echo $mrr_qnty;
                    $mrr_qnty_result=sql_select($mrr_qnty);
                    $mrr_arr =array();
                    foreach ($mrr_qnty_result as $row)
                    {
                        $mrr_arr[$row['PI_ID']]['ACC_QNTY'] = $row['RCV_QNTY'];   
                    }
                }
             
                if(!empty($mst_id_arr))
                {
                    fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 106, 5, $mst_id_arr, $empty_arr);   
                    $pckg_sql = "SELECT b.BTB_LC_ID,b.PKG_QUANTITY
                    FROM  COM_IMPORT_INVOICE_DTLS A, COM_IMPORT_INVOICE_MST B ,GBL_TEMP_ENGINE G
                    WHERE A.BTB_LC_ID = G.REF_VAL AND a.import_invoice_id = b.id AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0   AND G.ENTRY_FORM=106 AND G.USER_ID=$user_id AND G.REF_FROM=5 group by b.BTB_LC_ID,b.PKG_QUANTITY";
                
                    // echo $inv_sql;
                    $pckg_sql_result=sql_select($pckg_sql);
                    $pckg_qnty_arr = array();
                    foreach ($pckg_sql_result as $row)
                    {
                        $pckg_qnty_arr[$row['BTB_LC_ID']]['PKG_QUANTITY'] += $row['PKG_QUANTITY']; 
                      
                    }               
                }
                
                ?>
                

                <table width="2400" cellpadding="0" cellspacing="0" border="0" rules="all" id="" align="left">
                    <tr>
                    <td><strong style="font:Arial, Helvetica, sans-serif; font-size:16px;">Yarn BTB Details:</strong></td>
                    </tr>
                </table>

                <table width="1600" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" id="" align="left">
                    <thead>
                        <tr>
                            <th width="50">Sl</th>
                            <th width="80">Category</th>
                            <th width="90">BTB No.</th>
                            <th width="80">BTB Date</th>                            
                            <th width="50">BTB <br> Tol.%</th>
                            <th width="80">BTB Value (USD)</th>
                            <th width="80">PI No</th>
                            <th width="100">PI Basis</th>
                            <th width="160">PI Item Description</th>
                            <th width="80">Item Qty (Lbs)</th>
                            <th width="80">Unit Price (Lbs)</th>
                            <th width="80">PI Item Value (USD)</th>
                            <th width="80">In House Qty. (Lbs)</th>
                            <th width="80">In House Value (USD)</th>
                            <th width="80">Last In House Date</th>
                            <th width="80">Acceptance No</th>
                            <th width="70">Acceptance Date</th>
                            <th width="70">Acc. Qty. (Lbs)</th>
                            <th width="80">Acc. Value USD</th>
                        </tr>
                    </thead>
                </table>

                <table width="1600" rules="all" class="rpt_table" align="left" id="" border="1">
                    <tbody>
                        <?
                        
                        foreach ($btb_yarn_arr as $mst_key => $row_mst)
                        {
                            $s=1;
                            $z=1;
                            $k=0;
                            foreach($row_mst as $pi_key => $row)
                            {                                 
                                $rowspan1= $job_td_span[$mst_key];
                                if($row['ITEM_CATEGORY_ID']==1)
                                {
                                    if (fmod($s,2)==0) $bgcolor='#E9F3FF';
                                    else $bgcolor='#FFFFFF';
                                    $yarn_rcv_qty =  $in_house_arr[$row['PI_ID']][$row['COUNT_NAME']][$row['YARN_TYPE']][$row['COLOR_ID']]['ORDER_QNTY'];
                                    $yarn_rcv_amnt = $in_house_arr[$row['PI_ID']][$row['COUNT_NAME']][$row['YARN_TYPE']][$row['COLOR_ID']]['ORDER_AMOUNT']; 
                                    $yarn_rcv_date = $in_house_arr[$row['PI_ID']][$row['COUNT_NAME']][$row['YARN_TYPE']][$row['COLOR_ID']]['RCV_DATE']; 
                                    ?>
                                    <tr class=""  bgcolor="<? echo $bgcolor; ?>">
                                        <td width="50" align="center"><p><?=$s;?></p></td>
                                        <td width="80" align="left"> <p><? echo $item_category[$row['ITEM_CATEGORY_ID']]; ?></p></td>
                                        <? if($k==0){ ?>
                                        <td width='90' rowspan="<? echo $rowspna_arr[$row['LC_NUMBER']]['LC']; ?>" align='left' valign='middle'><p><?echo $row['LC_NUMBER'];?></p></td>
                                         <? $k++; } ?>
                                        <td width="80" align="center"><p><?echo change_date_format($row['LC_DATE']);?></p></td>
                                        
                                        <td width="50" align="right"><p><?echo $row['TOLERANCE'];?></p></td>
                                        <?if($z == 1)
                                        {?>
                                        <td rowspan="<?= $rowspan1; ?>"  valign="middle"  width="80" align="right"><p><?echo number_format($row['LC_VALUE'],2);?></p></td>
                                        <?
                                        $tot_lc_val += $row['LC_VALUE'];
                                        }?>
                                        <td width="80" align="left"><p><?echo $row['PI_NUMBER'];?></p></td>
                                        <td width="100" align="center"><p><?echo $pi_basis[$row['PI_BASIS_ID']];?></p></td>
                                        <td width="160" align="left"><p><?echo $count_arr[$row['COUNT_NAME']].", ".$row['YARN_COMPOSITION_PERCENTAGE1']."% , ".$yarn_type[$row['YARN_TYPE']].",".$color_library[$row['COLOR_ID']];?></p></td>
                                        <td width="80" align="right"><p><?echo number_format($row['QUANTITY'],2);
                                        $total_itm_qty += $row['QUANTITY'];
                                        ?></p></td>
                                        <td width="80" align="right"><p><?
                                        echo number_format($row['NET_PI_RATE'],2);
                                        $total_itm_rate += $row['NET_PI_RATE'];
                                        ?></p></td>
                                        <td width="80" align="right"><p><?echo number_format($row['NET_PI_AMOUNT'],2);
                                        $total_itm_amnt += $row['NET_PI_AMOUNT'];
                                        ?></p></td>
                                        <td width="80" align="right"><p><?
                                        if($yarn_rcv_qty>0)
                                        echo number_format($yarn_rcv_qty,2);
                                        $total_in_house_qnty += $yarn_rcv_qty;
                                        ?></p></td>
                                        <td width="80" align="right"><p><?
                                        if($yarn_rcv_amnt>0)
                                        //$in_house_amnt = $in_house_arr[$row['PI_ID']][$row['ITEM_CATEGORY_ID']]['ORDER_QNTY']*3;
                                        echo number_format($yarn_rcv_amnt,2);
                                        $total_in_house_amnt += $yarn_rcv_amnt;
                                        ?></p></td>
                                        <td width="80" align="center"><p><? 
                                        echo change_date_format($yarn_rcv_date) ;?></p></td>
                                        <?if($z == 1)
                                        {?>
                                        <td rowspan="<?= $rowspan1; ?>"  valign="middle" width="80" align="left"><p><? echo $accp_arr[$row['IMPORT_MST_ID']]['INVOICE_NO'];?></p></td>
                                        <td rowspan="<?= $rowspan1; ?>"  valign="middle" width="70" align="center"><p><? echo $accp_arr[$row['IMPORT_MST_ID']]['INVOICE_DATE'];?></p></td>
                                        <td rowspan="<?= $rowspan1; ?>"  valign="middle" width="70" align="right"> <p><?
                                        //echo number_format($mrr_arr[$row['PI_ID']]['ACC_QNTY'],2);
                                        echo  number_format($pckg_qnty_arr[$row['IMPORT_MST_ID']]['PKG_QUANTITY'],2);
                                        ?></p></td>
                                        <td rowspan="<?= $rowspan1; ?>"  valign="middle" width="80" align="right"><p><? echo number_format($accp_arr[$row['IMPORT_MST_ID']]['CURRENT_ACCEPTANCE_VALUE'],2);?></p></td> 

                                        <?
                                        
                                        
                                        $total_rcv_qty +=$mrr_arr[$row['PI_ID']]['ACC_QNTY'];
                                        $total_accp_val += $accp_arr[$row['IMPORT_MST_ID']]['CURRENT_ACCEPTANCE_VALUE'];
                                        
                                        }
                                        $s++;
                                    }?>
                                    </tr>
                                <?      
                            $z++;
                        }
                        
                        }
                        ?>  
                    </tbody>
                </table>

                <table width="1600" rules="all" border="1" class="rpt_table" align="left" style="margin-bottom:20px;">
                    <tfoot>
                        <tr align="right">
                            <th width="353"> Total:</th>
                            <th width="80"><p><? echo number_format($tot_lc_val,2)?></p></th>
                            <th width="80"></th>
                            <th width="100"></th>
                            <th width="160"></th>
                            <th width="80"><p><? echo number_format($total_itm_qty,2)?></p></th>
                            <th width="80"><p><? echo number_format($total_itm_rate,2)?></p></th>
                            <th width="80"><p><? echo number_format($total_itm_amnt,2)?></p></th>
                            <th width="80"><p><? echo number_format($total_in_house_qnty,2)?></p></th>
                            <th width="80"><p><? echo number_format($total_in_house_amnt,2)?></p></th>
                            <th width="80"></th>
                            <th width="80"></th>
                            <th width="70"></th>
                            <th width="70"><p><? echo  number_format($pckg_qnty_arr[$row['IMPORT_MST_ID']]['PKG_QUANTITY'],2);?></p></th>
                            <th width="80"><p><? echo number_format($total_accp_val,2);?></p></th>
                        </tr>
                    </tfoot>
                </table>

                <!-- Accessories  BTB Details Start: -->

                <?
                            
                $main_acc_sql="SELECT A.IMPORT_MST_ID, A.CURRENT_DISTRIBUTION, A.LC_SC_ID, B.LC_VALUE, B.LC_NUMBER, 1 as TYPE ,B.LC_DATE,B.ITEM_BASIS_ID,B.TOLERANCE,B.PAYTERM_ID,
                C.CURRENT_ACCEPTANCE_VALUE,D.BTB_LC_ID, D.INVOICE_NO,D.MATURITY_DATE,d.ID as INV_MST
                from COM_BTB_EXPORT_LC_ATTACHMENT A, COM_BTB_LC_MASTER_DETAILS B 
                left join COM_IMPORT_INVOICE_DTLS c on B.ID = C.BTB_LC_ID and c.status_active=1 and c.is_deleted=0
                left join COM_IMPORT_INVOICE_MST d on C.IMPORT_INVOICE_ID =d.ID  and d.status_active=1 and d.is_deleted=0
                where A.IMPORT_MST_ID in($all_acc_lc_btb_id) and A.IMPORT_MST_ID=B.ID 
                and A.IS_LC_SC=0 
                and B.LC_NUMBER IS NOT NULL  and A.status_active=1 and A.is_deleted=0 and b.status_active=1 and b.is_deleted=0
                union all
                select A.IMPORT_MST_ID, A.CURRENT_DISTRIBUTION, A.LC_SC_ID, B.LC_VALUE, B.LC_NUMBER, 2 as TYPE ,B.LC_DATE,B.ITEM_BASIS_ID,B.TOLERANCE,B.PAYTERM_ID,
                C.CURRENT_ACCEPTANCE_VALUE,D.BTB_LC_ID, D.INVOICE_NO,D.MATURITY_DATE,d.ID as INV_MST
                from COM_BTB_EXPORT_LC_ATTACHMENT A, COM_BTB_LC_MASTER_DETAILS B 
                left join COM_IMPORT_INVOICE_DTLS c on B.ID = C.BTB_LC_ID and c.status_active=1 and c.is_deleted=0
                left join COM_IMPORT_INVOICE_MST d on C.IMPORT_INVOICE_ID =d.ID  and d.status_active=1 and d.is_deleted=0
                where A.IMPORT_MST_ID in($all_acc_lc_btb_id) and A.IMPORT_MST_ID=B.ID 
                and A.IS_LC_SC=1 
                and B.LC_NUMBER IS NOT NULL  and A.status_active=1 and A.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
                //echo $main_acc_sql;
                $main_acc_sql_result=sql_select($main_acc_sql);
                $btb_acc_arr = array();
                $total_acc_arr = array();
                foreach($main_acc_sql_result as $row)
                {  
                    $btb_acc_arr[$row['IMPORT_MST_ID']][$row['INV_MST']]['IMPORT_MST_ID'] = $row['IMPORT_MST_ID'];
                    $btb_acc_arr[$row['IMPORT_MST_ID']][$row['INV_MST']]['CURRENT_DISTRIBUTION'] = $row['CURRENT_DISTRIBUTION'];
                    $btb_acc_arr[$row['IMPORT_MST_ID']][$row['INV_MST']]['LC_SC_ID'] = $row['LC_SC_ID'];
                    $btb_acc_arr[$row['IMPORT_MST_ID']][$row['INV_MST']]['LC_VALUE'] = $row['LC_VALUE'];
                    $btb_acc_arr[$row['IMPORT_MST_ID']][$row['INV_MST']]['LC_NUMBER'] = $row['LC_NUMBER'];
                    $btb_acc_arr[$row['IMPORT_MST_ID']][$row['INV_MST']]['TYPE'] = $row['TYPE'];
                    $btb_acc_arr[$row['IMPORT_MST_ID']][$row['INV_MST']]['LC_DATE'] = $row['LC_DATE'];
                    $btb_acc_arr[$row['IMPORT_MST_ID']][$row['INV_MST']]['ITEM_BASIS_ID'] = $row['ITEM_BASIS_ID'];
                    $btb_acc_arr[$row['IMPORT_MST_ID']][$row['INV_MST']]['TOLERANCE'] = $row['TOLERANCE'];
                    $btb_acc_arr[$row['IMPORT_MST_ID']][$row['INV_MST']]['CURRENT_ACCEPTANCE_VALUE'] = $row['CURRENT_ACCEPTANCE_VALUE'];
                    $btb_acc_arr[$row['IMPORT_MST_ID']][$row['INV_MST']]['BTB_LC_ID'] = $row['BTB_LC_ID'];
                    $btb_acc_arr[$row['IMPORT_MST_ID']][$row['INV_MST']]['INVOICE_NO'] = $row['INVOICE_NO'];
                    $btb_acc_arr[$row['IMPORT_MST_ID']][$row['INV_MST']]['MATURITY_DATE'] = $row['MATURITY_DATE'];
                    $btb_acc_arr[$row['IMPORT_MST_ID']][$row['INV_MST']]['INV_MST'] = $row['INV_MST'];  
                    $inv_mst_id .=  $row['INV_MST'].",";
                    $total_acc_arr[$row['IMPORT_MST_ID']]['CURRENT_ACCEPTANCE_VALUE'] += $row['CURRENT_ACCEPTANCE_VALUE']; 
                }
                $all_inv_ids = ltrim(implode(",", array_unique(explode(",", chop($inv_mst_id, ",")))), ',');

                foreach ($btb_acc_arr as $mst_key => $row_mst) 
                {
                    $row_no=0;
                    foreach ($row_mst as $pi_key => $row) 
                    {
                        $row_no++;
                    }
                    $job_td_span_acc[$mst_key] = $row_no;
                }

                $payment_sql="SELECT a.INVOICE_ID,a.LC_ID,a.PAYMENT_DATE,sum(a.ACCEPTED_AMMOUNT) as ACCEPTED_AMMOUNT
                FROM com_import_payment_com a, COM_IMPORT_PAYMENT_COM_MST b where a.mst_id = b.id and  b.invoice_id in ($all_inv_ids) and A.status_active=1 and A.is_deleted=0 and b.status_active=1 and b.is_deleted=0
                group by a.invoice_id,a.lc_id,a.PAYMENT_DATE
                UNION ALL
                SELECT a.INVOICE_ID,a.LC_ID,a.PAYMENT_DATE,sum(a.ACCEPTED_AMMOUNT) as ACCEPTED_AMMOUNT
                FROM com_import_payment a,COM_IMPORT_PAYMENT_MST b where a.mst_id = b.id and b.invoice_id in ($all_inv_ids) and A.status_active=1 and A.is_deleted=0 and b.status_active=1 and b.is_deleted=0
                group by a.invoice_id,a.lc_id,a.PAYMENT_DATE";
                $payment_sql_result=sql_select($payment_sql);
                $payment_arr = array();
                //echo $payment_sql;
                foreach($payment_sql_result as $row)
                { 
                    $payment_arr[$row['LC_ID']][$row['INVOICE_ID']]['ACCEPTED_AMMOUNT'] = $row['ACCEPTED_AMMOUNT'];
                    $payment_arr[$row['LC_ID']][$row['INVOICE_ID']]['PAYMENT_DATE'] = $row['PAYMENT_DATE'];
                }
                ?>

                <table width="2400" cellpadding="0" cellspacing="0" border="0" rules="all" id="" align="left">
                    <tr>
                    <td><strong style="font:Arial, Helvetica, sans-serif; font-size:16px;">Accessories BTB Details :</strong></td>
                    </tr>
                </table>


                <table width="1600" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" id="" align="left">
                    <thead>
                        <tr>
                            <th width="50">Sl</th>
                            <th width="90">Category</th>
                            <th width="110">BTB No.</th>
                            <th width="80">BTB Date</th>
                            <th width="100">BTB Basis</th>
                            <th width="50">BTB Tol.%</th>
                            <th width="150">PI Nos</th>
                            <th width="100">BTB Value (USD)</th>
                            <th width="110">Invoice No.</th>
                            <th width="100">Acceptance Value (USD)</th>  
                            <th width="80">Maturity Date</th>
                            <th width="100">Acceptance Balance</th>
                            <th width="100">Payment Value (USD)</th>
                            <th width="80">Payment Date</th>
                            <th width="100">Payment Due</th>
                            <th width="100">Inhouse Vlaue (USD)</th>
                        </tr>
                    </thead>
                </table>

                <table width="1600" rules="all" class="rpt_table" align="left" id="" border="1">
                    <tbody>
                        <?
                        $l=1;
                        foreach ($btb_acc_arr as $mst_key => $row_mst)
                        {
                            
                            $k=1;
                            foreach($row_mst as $pi_key => $row)
                            {

                                $rowspan= $job_td_span_acc[$mst_key];
                                if (fmod($l,2)==0) $bgcolor='#E9F3FF';
                                else $bgcolor='#FFFFFF';
                                ?>
                                <tr class=""  bgcolor="<? echo $bgcolor; ?>">
                                <?
                                if($k == 1)
									{?>
			                        <td rowspan="<?= $rowspan; ?>"  valign="middle" width="50" align="center"><p><?=$l;?></p></td>
			                        <td rowspan="<?= $rowspan; ?>"  valign="middle" width="90" align="left"> <p><? echo $item_category[$categoryArr[$row['IMPORT_MST_ID']]]; ?></p></td>
                                    <td rowspan="<?= $rowspan; ?>"  valign="middle" width="110" align="left"><p><?echo $row['LC_NUMBER'];?></p></td>
                                    <td rowspan="<?= $rowspan; ?>"  valign="middle" width="80" align="center"><p><?echo change_date_format($row['LC_DATE']);?></p></td>
                                    <td rowspan="<?= $rowspan; ?>"  valign="middle" width="100" align="center"><p><?echo $lc_basis[$row['ITEM_BASIS_ID']];?></p></td>
                                    <td rowspan="<?= $rowspan; ?>"  valign="middle" width="50" align="center"><p><?echo $row['TOLERANCE'];?></p></td>
                                    <td rowspan="<?= $rowspan; ?>"  valign="middle" width="150" align="left"><p><? echo ltrim(implode(",", array_unique(explode(",", chop($pi_nos[$row['IMPORT_MST_ID']], ",")))), ',');
                                    
                                     //$all_pi_nos;// chop($pi_nos,",")?></p></td>
                                    <td rowspan="<?= $rowspan; ?>"  valign="middle" width="100" align="right"><p><?echo number_format($row['LC_VALUE'],2);
                                    $tot_lc_val2 += $row['LC_VALUE'];
                                    ?></p></td>
                                    <?}?>
                                    <td width="110" align="left"><p><? echo $row['INVOICE_NO'];?></p></td>
                                    <td width="100" align="RIGHT"><p><? echo number_format($row['CURRENT_ACCEPTANCE_VALUE'],2);
                                    $total_ac_val += $row['CURRENT_ACCEPTANCE_VALUE'];?></p></td>
                                    <td width="80" align="center"><p><? echo $row['MATURITY_DATE'];?></p></td>
                                    <? if($k == 1)
									{?>
                                    <td rowspan="<?= $rowspan; ?>"  valign="middle" title="BTB Val - Total Acc Val" width="100" align="right"><p><? $acc_acp_val = ($row['LC_VALUE']-$total_acc_arr[$row['IMPORT_MST_ID']]['CURRENT_ACCEPTANCE_VALUE']) ;
                                    echo number_format($acc_acp_val,2);
                                    $total_acc_acp_bal +=$acc_acp_val;
                                    ?></p></td>
                                    <?}?>
                                    <td width="100" align="right"><p><? echo number_format( $payment_arr[$row['IMPORT_MST_ID']][$row['INV_MST']]['ACCEPTED_AMMOUNT'],2) ;
                                    $total_payment_acc += $payment_arr[$row['IMPORT_MST_ID']][$row['INV_MST']]['ACCEPTED_AMMOUNT'];
                                    ?></p></td>
                                    <td width="80" align="center"><p><? echo $payment_arr[$row['IMPORT_MST_ID']][$row['INV_MST']]['PAYMENT_DATE'];?></p></td>
                                    <td width="100" align="right" title="Acceptance Val - Payment Val"><p><? $due_value = $row['CURRENT_ACCEPTANCE_VALUE'] -$payment_arr[$row['IMPORT_MST_ID']][$row['INV_MST']]['ACCEPTED_AMMOUNT'] ;
                                    echo number_format($due_value,2);
                                    $total_due += $due_value;
                                    ;?></p></td>
                                    <? if($k == 1)
									{?>
                                    <td  rowspan="<?= $rowspan; ?>"  valign="middle" width="100" align="right"><p><? 
                                    echo  number_format($in_house_acc_oth_arr[$row['LC_NUMBER']]['AMOUNT'],2);
                                    $total_inhouse_oth += $in_house_acc_oth_arr[$row['LC_NUMBER']]['AMOUNT'];
                                    ?></p></td>
                                    <?
                                    $l++;
                                    }?>

                                </tr>  
                                <?
                                
                                $k++;
                                
                            }
                        }
                      
                        ?>
                    </tbody>
                </table>

                <table width="1600" rules="all" border="1" class="rpt_table" align="left" style="margin-bottom:20px;">
                    <tfoot>
                        <tr align="right">
                            <th width="635"> Total:</th>
                            <th width="100"><p><? echo number_format($tot_lc_val2,2)?></p></th>
                            <th width="110"></th>
                            <th width="100"><p><? echo number_format($total_ac_val,2)?></p></th>
                            <th width="80"></th>
                            <th width="100"><? echo number_format($total_acc_acp_bal,2)?></th>
                            <th width="100"><? echo number_format($total_payment_acc,2)?></th>
                            <th width="80"></th>
                            <th width="100"><? echo number_format($total_due,2)?></th>
                            <th width="100"><? echo number_format($total_inhouse_oth,2)?></th>
                        </tr>
                    </tfoot>
                </table> 

                <!-- ***  Others  BTB Details Start: ***  -->
                <?
                
                $main_other_btb_sql="SELECT A.IMPORT_MST_ID, A.CURRENT_DISTRIBUTION, A.LC_SC_ID, B.LC_VALUE, B.LC_NUMBER, 1 as TYPE ,B.LC_DATE,B.ITEM_BASIS_ID,B.TOLERANCE,
                C.CURRENT_ACCEPTANCE_VALUE,D.BTB_LC_ID, D.INVOICE_NO,D.MATURITY_DATE,d.ID as INV_MST
                from COM_BTB_EXPORT_LC_ATTACHMENT A, COM_BTB_LC_MASTER_DETAILS B 
                left join COM_IMPORT_INVOICE_DTLS c on B.ID = C.BTB_LC_ID and c.status_active=1 and c.is_deleted=0
                left join COM_IMPORT_INVOICE_MST d on C.IMPORT_INVOICE_ID =d.ID  and d.status_active=1 and d.is_deleted=0
                where A.IMPORT_MST_ID in($all_other_lc_btb_id) and A.IMPORT_MST_ID=B.ID 
                and A.IS_LC_SC=0 
                and B.LC_NUMBER IS NOT NULL  and A.status_active=1 and A.is_deleted=0 and b.status_active=1 and b.is_deleted=0
                union all
                select A.IMPORT_MST_ID, A.CURRENT_DISTRIBUTION, A.LC_SC_ID, B.LC_VALUE, B.LC_NUMBER, 2 as TYPE ,B.LC_DATE,B.ITEM_BASIS_ID,B.TOLERANCE,
                C.CURRENT_ACCEPTANCE_VALUE,D.BTB_LC_ID, D.INVOICE_NO,D.MATURITY_DATE,d.ID as INV_MST
                from COM_BTB_EXPORT_LC_ATTACHMENT A, COM_BTB_LC_MASTER_DETAILS B 
                left join COM_IMPORT_INVOICE_DTLS c on B.ID = C.BTB_LC_ID and c.status_active=1 and c.is_deleted=0
                left join COM_IMPORT_INVOICE_MST d on C.IMPORT_INVOICE_ID =d.ID  and d.status_active=1 and d.is_deleted=0
                where A.IMPORT_MST_ID in($all_other_lc_btb_id) and A.IMPORT_MST_ID=B.ID 
                and A.IS_LC_SC=1 
                and B.LC_NUMBER IS NOT NULL  and A.status_active=1 and A.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
                //echo $main_other_btb_sql;
                $main_other_btb_sql_result=sql_select($main_other_btb_sql);
                $total_ot_arr = array();
                $btb_oth_arr = array();
                foreach($main_other_btb_sql_result as $row)
                {
                    $btb_oth_arr[$row['IMPORT_MST_ID']][$row['INV_MST']]['IMPORT_MST_ID'] = $row['IMPORT_MST_ID'];
                    $btb_oth_arr[$row['IMPORT_MST_ID']][$row['INV_MST']]['CURRENT_DISTRIBUTION'] = $row['CURRENT_DISTRIBUTION'];
                    $btb_oth_arr[$row['IMPORT_MST_ID']][$row['INV_MST']]['LC_SC_ID'] = $row['LC_SC_ID'];
                    $btb_oth_arr[$row['IMPORT_MST_ID']][$row['INV_MST']]['LC_VALUE'] = $row['LC_VALUE'];
                    $btb_oth_arr[$row['IMPORT_MST_ID']][$row['INV_MST']]['LC_NUMBER'] = $row['LC_NUMBER'];
                    $btb_oth_arr[$row['IMPORT_MST_ID']][$row['INV_MST']]['TYPE'] = $row['TYPE'];
                    $btb_oth_arr[$row['IMPORT_MST_ID']][$row['INV_MST']]['LC_DATE'] = $row['LC_DATE'];
                    $btb_oth_arr[$row['IMPORT_MST_ID']][$row['INV_MST']]['ITEM_BASIS_ID'] = $row['ITEM_BASIS_ID'];
                    $btb_oth_arr[$row['IMPORT_MST_ID']][$row['INV_MST']]['TOLERANCE'] = $row['TOLERANCE'];
                    $btb_oth_arr[$row['IMPORT_MST_ID']][$row['INV_MST']]['CURRENT_ACCEPTANCE_VALUE'] = $row['CURRENT_ACCEPTANCE_VALUE'];
                    $btb_oth_arr[$row['IMPORT_MST_ID']][$row['INV_MST']]['BTB_LC_ID'] = $row['BTB_LC_ID'];
                    $btb_oth_arr[$row['IMPORT_MST_ID']][$row['INV_MST']]['INVOICE_NO'] = $row['INVOICE_NO'];
                    $btb_oth_arr[$row['IMPORT_MST_ID']][$row['INV_MST']]['MATURITY_DATE'] = $row['MATURITY_DATE'];
                    $btb_oth_arr[$row['IMPORT_MST_ID']][$row['INV_MST']]['INV_MST'] = $row['INV_MST'];
                    $inv_ot_mst_id .= $row['INV_MST'].",";
                    $total_ot_arr[$row['IMPORT_MST_ID']]['CURRENT_ACCEPTANCE_VALUE'] += $row['CURRENT_ACCEPTANCE_VALUE']; 
                }
                $inv_ot_mst_ids = ltrim(implode(",", array_unique(explode(",", chop($inv_ot_mst_id, ",")))), ',');
                foreach($btb_oth_arr as $mst_key => $row_mst) 
                {
                    $row_no=0;
                    foreach ($row_mst as $pi_key => $row) 
                    {
                        $row_no++;
                    }
                    $job_td_span_oth[$mst_key] = $row_no;
                }
                
                $payment_oth_sql="SELECT a.INVOICE_ID,a.LC_ID,a.PAYMENT_DATE,sum(a.ACCEPTED_AMMOUNT) as ACCEPTED_AMMOUNT
                FROM com_import_payment_com a, COM_IMPORT_PAYMENT_COM_MST b where a.mst_id = b.id and  b.invoice_id in ($inv_ot_mst_ids) and A.status_active=1 and A.is_deleted=0 and b.status_active=1 and b.is_deleted=0
                group by a.invoice_id,a.lc_id,a.PAYMENT_DATE
                UNION ALL
                SELECT a.INVOICE_ID,a.LC_ID,a.PAYMENT_DATE,sum(a.ACCEPTED_AMMOUNT) as ACCEPTED_AMMOUNT
                FROM com_import_payment a,COM_IMPORT_PAYMENT_MST b where a.mst_id = b.id and b.invoice_id in ($inv_ot_mst_ids) and A.status_active=1 and A.is_deleted=0 and b.status_active=1 and b.is_deleted=0
                group by a.invoice_id,a.lc_id,a.PAYMENT_DATE";
                //echo $payment_oth_sql;
                $payment_sql_oth_result=sql_select($payment_oth_sql);
                $payment_ot_arr = array();
                foreach($payment_sql_oth_result as $row)
                { 
                    $payment_ot_arr[$row['LC_ID']][$row['INVOICE_ID']]['ACCEPTED_AMMOUNT'] = $row['ACCEPTED_AMMOUNT'];
                    $payment_ot_arr[$row['LC_ID']][$row['INVOICE_ID']]['PAYMENT_DATE'] = $row['PAYMENT_DATE'];
                }
                ?>

                <table border="0" width="2400"><tr>&nbsp;</tr></table><br>

                <table width="2400" cellpadding="0" cellspacing="0" border="0" rules="all" id="" align="left">
                    <tr>
                    <td><strong style="font:Arial, Helvetica, sans-serif; font-size:16px;">Others BTB Details:</strong></td>
                    </tr>
                </table>

                <table width="1600" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" id="" align="left">
                    <thead>
                        <tr>
                            <th width="50">Sl</th>
                            <th width="120">Category</th>
                            <th width="110">BTB No.</th>
                            <th width="80">BTB Date</th>
                            <th width="100">BTB Basis</th>
                            <th width="50">BTB Tol %</th>
                            <th width="120">PI Nos</th>
                            <th width="90">BTB Value (USD)</th>
                            <th width="80">Invoice No.</th>
                            <th width="90">Acceptance Value (USD)</th>
                            
                            <th width="80">Maturity Date</th>
                            <th width="90">Acceptance Balance</th>
                            <th width="90">Payment Value (USD)</th>
                            <th width="80">Payment Date</th>
                            <th width="90">Payment Due</th>
                            <th width="90">Inhouse Vlaue (USD)</th>
                        </tr>
                    </thead>
                </table>

                <table width="1600" rules="all" class="rpt_table" align="left" id="" border="1">
                    <tbody>
                        <?
                        $n=1;
                        foreach ($btb_oth_arr as $mst_key => $row_mst)
                        {
                            $m=1;
                            foreach($row_mst as $pi_key => $row)
                            {

                            $rowspan= $job_td_span_oth[$mst_key];
                            if (fmod($n,2)==0) $bgcolor='#E9F3FF';
                            else $bgcolor='#FFFFFF';
                            ?>
                            <tr class=""  bgcolor="<? echo $bgcolor; ?>">
                            <?
                            if($m == 1)
                            {?>
                            <td rowspan="<?= $rowspan; ?>"  valign="middle"  width="50" align="center"><p><?=$n;?></p></td>
                            <td rowspan="<?= $rowspan; ?>"  valign="middle" width="120" align="left"> <p><? echo $item_category[$categoryArr[$row['IMPORT_MST_ID']]]; ?></p></td>
                            <td rowspan="<?= $rowspan; ?>"  valign="middle" width="110" align="left"><p><?echo $row['LC_NUMBER'];?></p></td>
                            <td rowspan="<?= $rowspan; ?>"  valign="middle" width="80" align="center"><p><?echo change_date_format($row['LC_DATE']);?></p></td>
                            <td rowspan="<?= $rowspan; ?>"  valign="middle" width="100" align="center"><p><?echo $lc_basis[$row['ITEM_BASIS_ID']];?></p></td>
                            <td rowspan="<?= $rowspan; ?>"  valign="middle" width="50" align="center"><p><?echo $row['TOLERANCE'];?></p></td>
                            <td rowspan="<?= $rowspan; ?>"  valign="middle" width="120" align="left"><p><? echo ltrim(implode(",", array_unique(explode(",", chop($pi_nos[$row['IMPORT_MST_ID']], ",")))), ',');//$all_pi_nos;//?></p></td>
                            <td rowspan="<?= $rowspan; ?>"  valign="middle" width="90" align="right"><p><?echo number_format($row['LC_VALUE'],2);
                            $tot_lc_val3 += $row['LC_VALUE'];
                            ?></p></td>
                            <?}?>
                            <td width="80" align="left"><p><? echo $row['INVOICE_NO'];?></p></td>
                            <td width="90" align="RIGHT"><p><? echo number_format($row['CURRENT_ACCEPTANCE_VALUE'],2);
                            $total_ac_val2 += $row['CURRENT_ACCEPTANCE_VALUE'];?></p></td>
                            <td width="80" align="center"><p><? echo $row['MATURITY_DATE'];?></p></td>
                            <? if($m == 1)
                            {?>
                            <td rowspan="<?= $rowspan; ?>"  valign="middle" title="BTB Val - Total Acc Val" width="90" align="right"><p><? $oth_acp_val = ($row['LC_VALUE']-$total_ot_arr[$row['IMPORT_MST_ID']]['CURRENT_ACCEPTANCE_VALUE']) ;
                            echo number_format($oth_acp_val,2);
                            $total_oth_acp_bal +=$oth_acp_val;
                            ?></p></td>
                            <?}?>
                            <td width="90" align="right"><p><? echo number_format( $payment_ot_arr[$row['IMPORT_MST_ID']][$row['INV_MST']]['ACCEPTED_AMMOUNT'],2) ;
                            $total_payment_ot += $payment_ot_arr[$row['IMPORT_MST_ID']][$row['INV_MST']]['ACCEPTED_AMMOUNT'];
                            ?></p></td>
                            <td width="80" align="center"><p><? echo $payment_ot_arr[$row['IMPORT_MST_ID']][$row['INV_MST']]['PAYMENT_DATE'];?></p></td>
                            <td width="90" align="right" title="Acceptance Val - Payment Val"><p><? $due_value_ot = $row['CURRENT_ACCEPTANCE_VALUE'] -$payment_ot_arr[$row['IMPORT_MST_ID']][$row['INV_MST']]['ACCEPTED_AMMOUNT'] ;
                            echo number_format($due_value_ot,2);
                            $total_due_ot += $due_value_ot;
                            ;?></p></td>
                            <? if($m == 1)
							{?>
                            <td rowspan="<?= $rowspan; ?>"  valign="middle" width="90" align="right"><p><?
                            $in_house_amnt_oth = $in_house_acc_oth_arr[$row['LC_NUMBER']]['AMOUNT'];
                            echo  number_format($in_house_amnt_oth,2)
                            ?></p></td>
                            <?
                            $n++;
                            }?>

                            </tr>  
                            <?
                            
                            $m++;
                        }
                        }
                      
                        ?>
                    </tbody>
                </table>

                <table width="1600" rules="all" border="1" class="rpt_table" align="left" style="margin-bottom:20px;">
                    <tfoot>
                        <tr align="right">
                            <th width="635"> Total:</th>
                            <th width="90"><p><? echo number_format($tot_lc_val3,2)?></p></th>
                            <th width="80"></th>
                            <th width="90"><p><? echo number_format($total_ac_val2,2)?></p></th>
                            <th width="80"></th>
                            <th width="90"><? echo number_format($total_oth_acp_bal,2);?></th>
                            <th width="90"><? echo number_format($total_payment_ot,2)?></th>
                            <th width="80"></th>
                            <th width="90"><? echo number_format($total_due_ot,2)?></th>
                            <th width="90"><? echo number_format($in_house_amnt_oth,2)?></th>
                        </tr>
                    </tfoot>
                </table>


                <!-- *** PO Country Breakdown : ***  -->
                <?
                
                // if(!empty($wo_dtls_id_arr))
                // {
                //     fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 106, 4, $wo_dtls_id_arr, $empty_arr);
                //     $wo_sql="SELECT A.ID ,a.JOB_ID AS JOB_ID  ,a.JOB_NO
                //     FROM WO_NON_ORDER_INFO_DTLS A,GBL_TEMP_ENGINE G
                //     WHERE A.ID = G.REF_VAL AND A.STATUS_ACTIVE =1 AND A.IS_DELETED = 0 AND G.ENTRY_FORM=106 AND G.USER_ID=$user_id AND G.REF_FROM=4
                //     UNION ALL
                //     SELECT A.ID ,0 AS JOB_ID ,a.JOB_NO
                //     FROM WO_BOOKING_DTLS A,GBL_TEMP_ENGINE G
                //     WHERE A.ID = G.REF_VAL AND A.STATUS_ACTIVE =1 AND A.IS_DELETED = 0 AND G.ENTRY_FORM=106 AND G.USER_ID=$user_id AND G.REF_FROM=4";
                //     // echo $wo_sql;
                //     $wo_sql_result=sql_select($wo_sql);
                //     // $exp_data_arr = array();
                //     foreach ($wo_sql_result as $row)
                //     {
                //         $job_no .= "'".$row['JOB_NO']."'".",";
                //     }
                //     $all_job_no = ltrim(implode(",", array_unique(explode(",", chop($job_no, ",")))), ',');

                //     // $sql_order="SELECT A.ID AS ORDER_ID,A.JOB_NO_MST, A.PO_NUMBER,A.PUB_SHIPMENT_DATE,B.STYLE_REF_NO,B.BUYER_NAME,SUM(C.EX_FACTORY_QNTY) AS EX_FACTORY_QNTY,D.COUNTRY_ID ,
                //     // SUM(D.ORDER_QUANTITY) AS PO_QTY,D.ULTIMATE_COUNTRY_ID
                //     // from wo_po_break_down a,wo_po_details_master b,pro_ex_factory_mst c ,WO_PO_COLOR_SIZE_BREAKDOWN d where a.job_id = b.id and a.id=c.po_break_down_id and A.JOB_NO_MST = d.JOB_NO_MST and b.company_name=20 and a.is_deleted=0 and a.status_active =1 and b.is_deleted=0 and b.status_active =1 
                //     // and c.is_deleted=0 and c.entry_form != 85 and c.status_active =1 and d.status_active =1 and b.job_no in ($all_job_no)
                //     // group by A.ID,A.JOB_NO_MST, A.PO_NUMBER,A.PUB_SHIPMENT_DATE,B.STYLE_REF_NO,B.BUYER_NAME,d.country_id,d.ultimate_country_id ";

                //     $sql_order="SELECT A.ID AS ORDER_ID,A.JOB_NO_MST, A.PO_NUMBER,A.PUB_SHIPMENT_DATE,B.STYLE_REF_NO,B.BUYER_NAME,SUM(C.EX_FACTORY_QNTY) AS EX_FACTORY_QNTY
                //     from wo_po_details_master b,wo_po_break_down a
                //     left join pro_ex_factory_mst c on a.id=c.po_break_down_id and c.is_deleted=0 and c.entry_form != 85 and c.status_active =1
                //     where a.job_id = b.id  and b.company_name=20 and a.is_deleted=0 and a.status_active =1 and b.is_deleted=0 and b.status_active =1 
                //     and b.job_no in ($all_job_no)
                //     group by A.ID,A.JOB_NO_MST, A.PO_NUMBER,A.PUB_SHIPMENT_DATE,B.STYLE_REF_NO,B.BUYER_NAME ";
                //     //echo $sql_order;
                //     $sql_order_result=sql_select($sql_order);
                // }


                    
            
                ?>

                <table border="0" width="1900"><tr>&nbsp;</tr></table><br>

                <table width="2400" cellpadding="0" cellspacing="0" border="0" rules="all" id="" align="left">
                    <tr>
                    <td><strong style="font:Arial, Helvetica, sans-serif; font-size:16px;">PO Breakdown :</strong></td>
                    </tr>
                </table>

                <table width="570" cellpadding="0" cellspacing="0" border="1" rules="all" align="left" class="rpt_table">
                    <thead>
                        <tr>
                            <th width="30">Sl</td>
                            <th width="80">Buyer</td>
                            <th width="90">Style Ref</td>
                            <th width="100">Job No</td>
                            <th width="100">PO No</td>
                            <!-- <th width="100">Conutry</td> -->
                            <th width="80">Publish Ship Date</td>
                            <!-- <th width="130">Country Qty</td> -->
                            <!-- <th width="120">FOB</td> -->
                            <!-- <th width="130">Country Value</td> -->
                            <th width="80">Ex-Factory</td>
        
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    $t=1;
                    foreach($sql_po_brkdwn_result as $row)
                    {
                        if (fmod($t,2)==0)  $bgcolor="#E9F3FF";
                        else $bgcolor="#FFFFFF";
                        ?>
                        <tr class=""  bgcolor="<? echo $bgcolor; ?>">
                            <td width="30"><? echo $t; ?></td>
                            <td width="80"><p><? echo $buyer_arr[$row['BUYER_NAME']]; ?></p></td>
                            <td width="90"><p><? echo $row['STYLE_REF_NO']; ?></p></td>
                            <td width="100"  align="center"><p><? echo $row['JOB_NO_MST']; ?></p></td>
                            <td width="100"><p><? echo $row['PO_NUMBER']; ?></p></td>
                            <!-- <td width="100" align="center"><p><?// echo $country_arr[$row['COUNTRY_ID']];?></p></td> -->
                            <td width="80" align="center"><p><? echo change_date_format($row['PUB_SHIPMENT_DATE']); ?></p></td>
                            <!-- <td width="130" align="center"><p><?// echo number_format($row['PO_QTY'],2);?></p></td> -->
                            <!-- <td width="120"><p></p></td> -->
                            <!-- <td width="130"><p></p></td> -->
                            <td align="right" width="80"><p><? echo number_format($row['EX_FACTORY_QNTY'],2); ?></p></td>       
                        </tr>
                     <?
                     $t++;
                     $total_ex_fact +=$row['EX_FACTORY_QNTY']; 
	                }
                    ?>
                    </tbody>
                    <tfoot>
                        <tr align="right">
                            <th colspan="6"><strong>Total : </strong></td>
                            <th width="80"><p><? echo number_format($total_ex_fact,2); ?></p></td>    
                        </tr>
                    </tfoot>
                </table>

                <table border="0" width="1900"><tr>&nbsp;</tr></table><br>

                
            </fieldset>
        </div>
        <?
        $con = connect();
        execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ENTRY_FORM=106");
        oci_commit($con);
        disconnect($con);
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



if ($action=="load_drop_down_search")
{
	$data=explode('_',$data);
	if($data[1]==1) echo '<input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes" style="width:160px" autocomplete=off />';
	if($data[1]==2) echo create_drop_down( "txt_search_common", 170, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0);
	if($data[1]==3) echo create_drop_down( "txt_search_common", 170, "select bank_name,id from lib_bank where is_deleted=0  and status_active=1 and lien_bank=1 order by bank_name","id,bank_name", 1, "-- All Lein Bank --", $selected, "",0,"" );

    if($data[1]==4) echo '<input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes" style="width:160px" autocomplete=off />';
	exit();
}




if ($action=="search_file_info")
{
	$ex_data = explode("_",$data);
	//print_r($ex_data);die;
	$cbo_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$company_id = $ex_data[2];
	$buyer_id = $ex_data[3];
	$lien_bank_id = $ex_data[4];
	$cbo_year = $ex_data[5];
	//echo $cbo_year; die;
	if($buyer_id!=0) $buy_query="and buyer_name='$buyer_id'"; else  $buy_query="";
	if($lien_bank_id!=0) $lien_bank_id="and lien_bank='$lien_bank_id'"; else  $lien_bank_id="";
	if($cbo_year!=0)
	{
		$year_cond_sc="and sc_year='$cbo_year'";
		$year_cond_lc="and lc_year='$cbo_year'";
	}
	else
	{
		$year_cond_sc="";
		$year_cond_lc="";
	}
	//$year_cond_sc="and sc_year='".date("Y")."'";
	//$year_cond_lc="and lc_year='".date("Y")."'";
	//echo $lien_bank_id;die;

	//if($txt_search_common==0)$txt_search_common="";

    $txt_search_common = trim($txt_search_common);
    $search_cond ="";$search_cond_lc="";$search_cond_sc="";
    if($txt_search_common!="")
    {
        if($cbo_search_by==1)
        {
            $search_cond .= " and internal_file_no like '%$txt_search_common%'";
        }
        else if($cbo_search_by==2)
        {
            $search_cond .= " and buyer_name='$txt_search_common'";
        }
        else if($cbo_search_by==3)
        {
            $search_cond .= " and lien_bank='$txt_search_common'";
        }
        else if($cbo_search_by==4)
        {
            $search_cond_lc .= " and export_lc_no='$txt_search_common'";
            $search_cond_sc .= " and contract_no='$txt_search_common'";
        }
    }
    //echo $cbo_search_by."**".$txt_search_common; die;
    //echo $cbo_search_by."**".$search_cond_lc."**".$search_cond_sc; die;
    if($db_type == 0)
    {
        $sql = "select a.id,a.beneficiary_name, a.internal_file_no, a.lien_bank, a.buyer_name ,  a.lc_sc_year , group_concat(a.export_lc_no) as export_lc_no
        from (
             select id,beneficiary_name, internal_file_no, lien_bank, buyer_name , lc_year as lc_sc_year, group_concat(export_lc_no) as export_lc_no, 'export' as type
              from com_export_lc
             where beneficiary_name='$company_id' and status_active=1 and is_deleted=0 $buy_query $lien_bank_id $year_cond_lc $search_cond $search_cond_lc
             group by id,internal_file_no, lc_year, beneficiary_name, buyer_name , lien_bank
             union all
             select id,beneficiary_name, internal_file_no, lien_bank, buyer_name, sc_year as lc_sc_year,group_concat(contract_no) as export_lc_no, 'import' as type
             from com_sales_contract
             where beneficiary_name='$company_id'
              and status_active=1 and is_deleted=0 $buy_query $lien_bank_id $year_cond_sc $search_cond $search_cond_sc
             group by id,internal_file_no, sc_year, beneficiary_name, buyer_name , lien_bank
         ) a
          group by a.id,a.beneficiary_name, a.internal_file_no, a.lien_bank, a.buyer_name , a.lc_sc_year";
    }
    else
    {
        $sql = "select a.id,a.beneficiary_name, a.internal_file_no, a.lien_bank, a.buyer_name ,  a.lc_sc_year , listagg(cast(a.export_lc_no as varchar(4000)),',') within group(order by a.export_lc_no) as export_lc_no
        from (
             select id,beneficiary_name, internal_file_no, lien_bank, buyer_name , lc_year as lc_sc_year, listagg(cast(export_lc_no as varchar(4000)),',') within group(order by export_lc_no) as export_lc_no, 'export' as type
              from com_export_lc
             where beneficiary_name='$company_id' and status_active=1 and is_deleted=0 $buy_query $lien_bank_id $year_cond_lc $search_cond $search_cond_lc
             group by id,internal_file_no, lc_year, beneficiary_name, buyer_name , lien_bank
             union all
             select id,beneficiary_name, internal_file_no, lien_bank, buyer_name, sc_year as lc_sc_year,listagg(cast(contract_no as varchar(4000)),',') within group(order by contract_no) as export_lc_no, 'import' as type
             from com_sales_contract
             where beneficiary_name='$company_id'
              and status_active=1 and is_deleted=0 $buy_query $lien_bank_id $year_cond_sc $search_cond $search_cond_sc
             group by id,internal_file_no, sc_year, beneficiary_name, buyer_name , lien_bank
         ) a
          group by a.id,a.beneficiary_name, a.internal_file_no, a.lien_bank, a.buyer_name , a.lc_sc_year";
		  //echo $sql;
    }	
   
	?>
    <div style="width:560px">
    <form name="display_file"  id="display_file">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" width="100%">
            <thead>
                <th width="60">Sl NO.</td>
                <th width="80">File NO</td>
                <th width="80">Year</td>
                <th width="130"> Buyer</td>
                <th width="100"> Lien Bank</td>
                <th >SC/LC No.</td>

            </thead>
            </table>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" width="100%" id="list_view">
            <tbody>
            <?
			$sll_result=sql_select($sql);
			$i=1;
			foreach($sll_result as $row)
			{
				if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				//echo $row[csf("internal_file_no")];die;
			    ?>
                <tr bgcolor="<? echo $bgcolor; ?>"  onclick="js_set_value('<? echo $row[csf("internal_file_no")];?>,<? echo $row[csf("lc_sc_year")];?>,<? echo $row[csf("buyer_name")];?>,<? echo $row[csf("lien_bank")];?>,<? echo $row[csf("id")];?>')" id="search<? echo $row[csf("id")]; ?>" style="cursor:pointer">
                    <td align="center" width="60"> <? echo $i;?></td>
                    <td align="center" width="80"><? echo $row[csf("internal_file_no")];  ?></td>
                    <td align="center" width="80"><? echo $row[csf("lc_sc_year")];  ?></td>
                    <td width="130"><? echo $buyer_name_arr[$row[csf("buyer_name")]];  ?></td>
                    <td width="100"><? echo $lein_bank_arr[$row[csf("lien_bank")]];  ?></td>
                    <td><p><? echo $row[csf("export_lc_no")];  ?></p></td>
                </tr>
                <?
				$i++;
			}
			?>
            </tbody>
            <input type="hidden" id="hide_file_no" name="hide_file_no"  />
        </table>
    </form>
    <script>setFilterGrid('list_view',-1)</script>
    </div>
    <?
}



disconnect($con);
?>

<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../../includes/common.php');
require_once('../../../../includes/class.reports.php');
require_once('../../../../includes/class.yarns.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];

$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );

if($db_type==2) $select_date=" to_char(a.insert_date,'YYYY')";
else if ($db_type==0) $select_date=" year(a.insert_date)";

if ($action=="load_drop_down_buyer")
{
    echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0);
    exit();
}


if ($action=="job_popup")
{
    echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
    extract($_REQUEST);
    ?>
    <script>
        function js_set_value(str)
        {
            $("#hide_job_no").val(str);
            parent.emailwindow.hide();
        }
    </script>
    <?
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
            $year_cond=" and YEAR(insert_date)=$cbo_year";
            $year_field="YEAR(insert_date)";
        }
        else
        {
            $year_cond=" and to_char(insert_date,'YYYY')=$cbo_year";
            $year_field="to_char(insert_date,'YYYY')";
        }
    }
    else $year_cond="";

    $arr=array (2=>$company_library,3=>$buyer_arr);
    $sql= "select a.job_no_prefix_num, a.job_no, a.company_name,a.buyer_name,a.style_ref_no,$year_field as year from wo_po_details_master a where a.company_name=$company_id $buyer_cond $year_cond order by a.id";
    //echo $sql;
    echo  create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No", "70,70,120,100,100","570","320",0, $sql , "js_set_value", "year,job_no", "", 1, "0,0,company_name,buyer_name,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no", "","setFilterGrid('list_view',-1)",'0,0,0,0,0');
    echo "<input type='hidden' id='hide_job_no' />";

    exit();
}


if($action=="style_refarence_surch")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
    <script>
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
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
				selected_no.push( str );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				selected_no.splice( i, 1 );
			}
			var id = ''; var name = ''; var job = ''; var num='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				num += selected_no[i] + ',';
			}
			id 		= id.substr( 0, id.length - 1 );
			name 	= name.substr( 0, name.length - 1 );
			num 	= num.substr( 0, num.length - 1 );
			//alert(num);
			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name );
			$('#txt_selected_no').val( num );
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="100">Please Enter Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:80px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <?
								echo create_drop_down( "cbo_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>
                        <td align="center">
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>
                        <td align="center" id="search_by_td">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
                        </td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $txt_style_ref_no; ?>'+'**'+'<? echo $txt_style_ref_id; ?>'+'**'+'<? echo $txt_style_ref; ?>', 'style_refarence_surch_list_view', 'search_div', 'woven_style_order_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
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

//style search------------------------------//
if($action=="style_refarence_surch_list_view")
{
    extract($_REQUEST);
    //echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
    list($company,$buyer,$search_type,$search_value,$cbo_year,$txt_style_ref_no,$txt_style_ref_id,$txt_style_ref)=explode('**',$data);

    $buyer=str_replace("'","",$buyer);
    $company=str_replace("'","",$company);
    $cbo_year=str_replace("'","",$cbo_year);
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
    if($db_type==0) if($cbo_year!=0) $job_cond=" and year(a.insert_date)='$cbo_year'";
    else if($cbo_year!=0) $job_cond=" and to_char(a.insert_date,'YYYY')='$cbo_year'";

    if($search_type==1 && $search_value!=''){
        $search_con=" and a.job_no like('%$search_value')";
    }
    else if($search_type==2 && $search_value!=''){
        $search_con=" and a.style_ref_no like('%$search_value%')";
    }



    if($buyer!=0) $buyer_cond="and a.buyer_name=$buyer"; else $buyer_cond="";
    $sql = "select a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,$select_date as job_year from wo_po_details_master a where a.company_name=$company $buyer_cond $year_cond $job_cond $search_con and is_deleted=0 order by a.id DESC";
    //echo $sql; die;
    echo create_list_view("list_view", "Style Ref No,Job No,Year","160,90,100","400","200",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "0", $arr, "style_ref_no,job_no_prefix_num,job_year", "","setFilterGrid('list_view',-1)","0","",1) ;
    echo "<input type='hidden' id='txt_selected_id' />";
    echo "<input type='hidden' id='txt_selected' />";
    echo "<input type='hidden' id='txt_selected_no' />";
    ?>
    <script language="javascript" type="text/javascript">
        var style_no='<? echo $txt_style_ref_no;?>';
        var style_id='<? echo $txt_style_ref_id;?>';
        var style_des='<? echo $txt_style_ref;?>';
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
if($action=="order_surch")
{

	echo load_html_head_contents("Order No Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>

		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
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
			var str_or = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			//$('#txt_individual_id' + str).val(splitSTR[1]);
			//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');

			toggle( document.getElementById( 'tr_' + str_or ), '#FFFFCC' );

			if( jQuery.inArray( selectID, selected_id ) == -1 ) {
				selected_id.push( selectID );
				selected_name.push( selectDESC );
				selected_no.push( str_or );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				selected_no.splice( i, 1 );
			}
			var id = ''; var name = ''; var job = ''; var num='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				num += selected_no[i] + ',';
			}
			id 		= id.substr( 0, id.length - 1 );
			name 	= name.substr( 0, name.length - 1 );
			num 	= num.substr( 0, num.length - 1 );
			//alert(num);
			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name );
			$('#txt_selected_no').val( num );
		}
    </script>

</head>

<body>
<div align="center">
	<form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up">Please Enter Order No</th>
                    <th>Shipment Date</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:80px;"></th>
                    <input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
                    <input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <?
								echo create_drop_down( "cbo_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>
                        <td align="center">
                    	<?
                       		$search_by_arr=array(1=>"Order No",2=>"Style Ref",3=>"Job No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>
                        <td align="center" id="search_by_td" width="130">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
                        </td>
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
                        </td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+'<? echo $cbo_year; ?>'+'**'+'<? echo $txt_style_ref; ?>', 'order_surch_list_view', 'search_div', 'woven_style_order_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
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

if($action=="order_surch_list_view")
{
    extract($_REQUEST);
    //echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
    list($company,$buyer,$search_type,$search_value,$start_date,$end_date,$cbo_year,$txt_style_ref)=explode('**',$data);
    ?>
    <script>
    </script>
    <?
    $buyer=str_replace("'","",$buyer);
    $company=str_replace("'","",$company);
    $txt_style_ref=str_replace("'","",$txt_style_ref);
    $cbo_year=str_replace("'","",$cbo_year);
    if(trim($cbo_year)!=0)
    {
        if($db_type==0)
        {
            $year_cond=" and YEAR(b.insert_date)=$cbo_year";
        }
        else
        {
            $year_cond=" and to_char(b.insert_date,'YYYY')=$cbo_year";
        }
    }

    if($search_type==1 && $search_value!=''){
        $search_con=" and a.po_number like('%$search_value')";
    }
    elseif($search_type==2 && $search_value!=''){
        $search_con=" and a.style_ref_no like('%$search_value')";
    }
    elseif($search_type==3 && $search_value!=''){
        $search_con=" and a.job_no_mst like('%$search_value')";
    }


    if($start_date!="" && $end_date!="")
    {
        if($db_type==0)
        {
            $date_cond="and a.pub_shipment_date between '".change_date_format(trim($start_date),"yyyy-mm-dd")."' and '".change_date_format(trim($end_date),"yyyy-mm-dd")."'";
        }
        else
        {
            $date_cond="and a.pub_shipment_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
        }
    }
    else
    {
        $date_cond="";
    }




    if($buyer!=0) $buyer_cond="and b.buyer_name=$buyer"; else $buyer_cond="";
    if($txt_style_ref!="")
    {
        if($db_type==0) $style_cond="and b.job_no_prefix_num in($txt_style_ref) and year(b.insert_date)= '$cbo_year' ";
        else $style_cond="and b.job_no_prefix_num in($txt_style_ref) and to_char(b.insert_date,'YYYY')= '$cbo_year' ";
    }
    else $style_cond="";

    //echo $style_cond."jahid";die;
    $sql = "select a.id,a.po_number,a.job_no_mst,b.style_ref_no,b.job_no_prefix_num,$select_date as job_year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and b.company_name=$company $buyer_cond $year_cond $style_cond $search_con $date_cond and a.status_active=1 order by a.id desc";
    //echo $sql;
    echo create_list_view("list_view", "Order NO,Job No,Year,Style Ref No","150,80,70,150","500","150",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "po_number,job_no_prefix_num,job_year,style_ref_no", "","setFilterGrid('list_view',-1)","0","",1) ;
    echo "<input type='hidden' id='txt_selected_id' />";
    echo "<input type='hidden' id='txt_selected' />";
    echo "<input type='hidden' id='txt_selected_no' />";
    ?>
    <script language="javascript" type="text/javascript">
        var style_no='<? echo $txt_order_id_no;?>';
        var style_id='<? echo $txt_order_id;?>';
        var style_des='<? echo $txt_order;?>';
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

if($action=="report_generate")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));

    $cbo_search_type=str_replace("'","",$cbo_search_type);
    $txt_style_ref=str_replace("'","",$txt_style_ref);
    $txt_order=str_replace("'","",$txt_order);
    $txt_order_id=str_replace("'","",$txt_order_id);
    $style_ref_id=str_replace("'","",$txt_style_ref_id);
    $txt_date_from=str_replace("'","",$txt_date_from);
    $txt_date_to=str_replace("'","",$txt_date_to);
    $txt_ex_date_form=str_replace("'","",$txt_ex_date_form);
    $txt_ex_date_to=str_replace("'","",$txt_ex_date_to);

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
        $buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
    }

    $cbo_year=str_replace("'","",$cbo_year);
    if(trim($cbo_year)!=0)
    {
        if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
        else $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
    }
    $ship_date_cond="";
    if($txt_date_from!="" && $txt_date_to!="")
    {
        $ship_date_cond="and b.shipment_date between '$txt_date_from' and '$txt_date_to' ";
    }
    $ex_fact_date_cond="";
    if($txt_ex_date_form!="" && $txt_ex_date_to!="")
    {
        $ex_fact_date_cond="and c.ex_factory_date between '$txt_ex_date_form' and '$txt_ex_date_to' ";
    }

    $job_no_cond="";
    if(trim($txt_style_ref)!="") $job_no_cond="and a.id  in($style_ref_id)";
    $order_cond="";
    //if(trim($txt_order)!="") $order_cond="and b.po_number='$txt_order'";
    if($txt_order_id!="") $order_cond="and b.id in($txt_order_id)";
    if($db_type==0) $select_job_year="year(a.insert_date) as job_year"; else $select_job_year="to_char(a.insert_date,'YYYY') as job_year";
    if($txt_ex_date_form!="" && $txt_ex_date_to!="")
    {
        $sql_po="select a.buyer_name, a.job_no, a.job_no_prefix_num, $select_job_year, a.style_ref_no, a.total_set_qnty as ratio, b.id as po_id, b.po_number, b.po_quantity as po_qnty, b.plan_cut, b.pub_shipment_date, c.ex_factory_date,
		CASE WHEN c.entry_form!=85 THEN c.ex_factory_qnty ELSE 0 END as ex_factory_qnty

		from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c
		where a.job_no=b.job_no_mst and  b.id=c.po_break_down_id and a.company_name=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $buyer_id_cond $year_cond $job_no_cond $order_cond $ex_fact_date_cond order by a.job_no, b.pub_shipment_date, b.id";
    }
    else
    {
        $sql_po="select a.buyer_name, a.job_no, a.job_no_prefix_num, $select_job_year, a.style_ref_no, a.total_set_qnty as ratio, b.id as po_id, b.po_number, b.po_quantity as po_qnty, b.plan_cut, b.pub_shipment_date, c.ex_factory_date,
		CASE WHEN c.entry_form!=85 THEN c.ex_factory_qnty ELSE 0 END as ex_factory_qnty

		from wo_po_details_master a, wo_po_break_down b left join pro_ex_factory_mst c on b.id=c.po_break_down_id and c.is_deleted=0 and c.status_active=1
		where a.job_no=b.job_no_mst and a.company_name=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $buyer_id_cond $year_cond $job_no_cond $order_cond $ship_date_cond order by a.job_no, b.pub_shipment_date, b.id";
    }


    $sql_po_result=sql_select($sql_po);
    $result_data_arr=$result_job_wise=array();$all_po_id=""; $JobArr=array();
    foreach($sql_po_result as $row)
    {
        if($all_po_id=="") $all_po_id=$row[csf("po_id")]; else $all_po_id.=",".$row[csf("po_id")];
        $result_data_arr[$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
        $result_data_arr[$row[csf("po_id")]]["buyer_name"]=$row[csf("buyer_name")];
        $result_data_arr[$row[csf("po_id")]]["job_no"]=$row[csf("job_no")];
        $result_data_arr[$row[csf("po_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
        $result_data_arr[$row[csf("po_id")]]["job_year"]=$row[csf("job_year")];
        $result_data_arr[$row[csf("po_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
        $result_data_arr[$row[csf("po_id")]]["ratio"]=$row[csf("ratio")];
        $result_data_arr[$row[csf("po_id")]]["po_number"]=$row[csf("po_number")];
        $result_data_arr[$row[csf("po_id")]]["po_qnty"]=$row[csf("po_qnty")]*$row[csf("ratio")];
        $result_data_arr[$row[csf("po_id")]]["plan_cut"]=$row[csf("plan_cut")];
        $result_data_arr[$row[csf("po_id")]]["pub_shipment_date"]=$row[csf("pub_shipment_date")];
        $result_data_arr[$row[csf("po_id")]]["ex_factory_date"]=$row[csf("ex_factory_date")];
        $result_data_arr[$row[csf("po_id")]]["ex_factory_qnty"]+=$row[csf("ex_factory_qnty")];
        $result_job_wise[$row[csf("job_no")]].=$row[csf("po_id")].",";
        $JobArr[]="'".$row[csf('job_no')]."'";
    }
    $yarn= new yarn($JobArr,'job');
    $yarn_qty_arr=$yarn->getOrderWiseYarnQtyArray();
    //print_r($yarn_qty_arr);
    $all_po_id=implode(",",array_unique(explode(",",$all_po_id)));
    $booking_req_arr=array();
    $sql_wo=sql_select("SELECT b.po_break_down_id,
	sum(CASE WHEN a.fabric_source in(1,2,3) and a.item_category=2 THEN b.grey_fab_qnty ELSE 0 END) AS grey_req_qnty,
	sum(CASE WHEN a.fabric_source in(1,2,3) and a.item_category=3 THEN b.grey_fab_qnty ELSE 0 END) AS woven_req_qnty,
	sum(b.fin_fab_qnty) as fin_fab_qnty
	from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id in ($all_po_id) group by b.po_break_down_id");

    foreach ($sql_wo as $brow)
    {
        $booking_req_arr[$brow[csf("po_break_down_id")]]['gray']=$brow[csf("grey_req_qnty")];
        $booking_req_arr[$brow[csf("po_break_down_id")]]['woven']=$brow[csf("woven_req_qnty")];
        $booking_req_arr[$brow[csf("po_break_down_id")]]['fin']=$brow[csf("fin_fab_qnty")];
    }
    $sql_res=sql_select("select b.po_break_down_id as po_id,
		sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as return_qnty
		from  pro_ex_factory_mst b  where b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id");
    $ex_factory_qty_arr=array();
    foreach($sql_res as $row)
    {
        $ex_factory_qty_arr[$row[csf('po_id')]]=$row[csf('return_qnty')];
    }

    $all_po_id=implode(",",array_unique(explode(",",$all_po_id)));
    $dataArrayYarnReq=array();
    $yarn_sql="select job_no, sum(avg_cons_qnty) as qnty from wo_pre_cost_fab_yarn_cost_dtls where status_active=1 and is_deleted=0 group by job_no";
    $resultYarn=sql_select($yarn_sql);
    foreach($resultYarn as $yarnRow)
    {
        $dataArrayYarnReq[$yarnRow[csf('job_no')]]=$yarnRow[csf('qnty')];
    }

    $reqDataArray=sql_select("select  a.po_break_down_id, sum((b.requirment/b.pcs)*a.plan_cut_qnty) as grey_req, sum((b.cons/b.pcs)*a.plan_cut_qnty) as finish_req from wo_po_color_size_breakdown a, wo_pre_cos_fab_co_avg_con_dtls b where a.po_break_down_id=b.po_break_down_id and a.color_number_id=b.color_number_id and a.size_number_id=b.gmts_sizes and a.is_deleted=0 and a.status_active=1 and a.po_break_down_id in ($all_po_id) group by a.po_break_down_id");
    $grey_finish_require_arr=array();
    foreach($reqDataArray as $row)
    {
        $grey_finish_require_arr[$row[csf("po_break_down_id")]]["grey_req"]=$row[csf("grey_req")];
        $grey_finish_require_arr[$row[csf("po_break_down_id")]]["finish_req"]=$row[csf("finish_req")];
    }

    $yarnDataArr=sql_select("select a.po_breakdown_id,
    sum(CASE WHEN a.entry_form=3 and c.entry_form=3 and c.knit_dye_source!=3 THEN a.quantity ELSE 0 END) AS issue_qnty_in,
    sum(CASE WHEN a.entry_form=3 and c.entry_form=3 and c.knit_dye_source=3 THEN a.quantity ELSE 0 END) AS issue_qnty_out
    from order_wise_pro_details a, inv_transaction b, inv_issue_master c
    where a.trans_id=b.id and b.mst_id=c.id and a.po_breakdown_id in($all_po_id) and b.item_category=1 and c.issue_purpose!=2 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1
    group by a.po_breakdown_id");
    $yarn_issue_arr=array();
    foreach($yarnDataArr as $row)
    {
        $yarn_issue_arr[$row[csf("po_breakdown_id")]]["issue_qnty_in"]=$row[csf("issue_qnty_in")];
        $yarn_issue_arr[$row[csf("po_breakdown_id")]]["issue_qnty_out"]=$row[csf("issue_qnty_out")];
    }

    $yarnReturnDataArr=sql_select("select a.po_breakdown_id,
    sum(CASE WHEN a.entry_form=9 and c.entry_form=9 and c.knitting_source!=3 THEN a.quantity ELSE 0 END) AS return_qnty_in,
    sum(CASE WHEN a.entry_form=9 and c.entry_form=9 and c.knitting_source=3 THEN a.quantity ELSE 0 END) AS return_qnty_out
    from order_wise_pro_details a, inv_transaction b, inv_receive_master c
    where a.trans_id=b.id and b.mst_id=c.id and a.po_breakdown_id in($all_po_id) and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1
    group by a.po_breakdown_id");


    $yarn_issue_rtn_arr=array();
    foreach($yarnReturnDataArr as $row)
    {
        $yarn_issue_rtn_arr[$row[csf("po_breakdown_id")]]["return_qnty_in"]=$row[csf("return_qnty_in")];
        $yarn_issue_rtn_arr[$row[csf("po_breakdown_id")]]["return_qnty_out"]=$row[csf("return_qnty_out")];
    }


    $dataArrayTrans=sql_select("SELECT po_breakdown_id,
    sum(CASE WHEN entry_form =11 and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_yarn, 
    sum(CASE WHEN entry_form in(11,258) and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_yarn, 
    sum(CASE WHEN entry_form in(13,258) and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_knit, 
    sum(CASE WHEN entry_form in(1,258) and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_knit, 
    sum(CASE WHEN entry_form in(15,258) and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_finish, 
    sum(CASE WHEN entry_form in(15,258) and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_finish 
    from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in(11,13,15,258) and po_breakdown_id in($all_po_id)
    group by po_breakdown_id");

    $transfer_data_arr=array();
    foreach($dataArrayTrans as $row)
    {
        $transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_in_qnty_yarn"]=$row[csf("transfer_in_qnty_yarn")];
        $transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_out_qnty_yarn"]=$row[csf("transfer_out_qnty_yarn")];
        $transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_in_qnty_knit"]=$row[csf("transfer_in_qnty_knit")];
        $transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_out_qnty_knit"]=$row[csf("transfer_out_qnty_knit")];
        $transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_in_qnty_finish"]=$row[csf("transfer_in_qnty_finish")];
        $transfer_data_arr[$row[csf("po_breakdown_id")]]["transfer_out_qnty_finish"]=$row[csf("transfer_out_qnty_finish")];
    }


    $prodKnitDataArr=sql_select("select a.po_breakdown_id,
    sum(CASE WHEN c.knitting_source!=3 and a.entry_form=2 THEN a.quantity ELSE 0 END) AS knit_qnty_in,
    sum(CASE WHEN c.knitting_source=3 and a.entry_form=2 THEN a.quantity ELSE 0 END) AS knit_qnty_out,
    sum(CASE WHEN a.entry_form=22 THEN a.quantity ELSE 0 END) AS knit_qnty_rec
    from order_wise_pro_details a, pro_grey_prod_entry_dtls b, inv_receive_master c where a.dtls_id=b.id and b.mst_id=c.id and a.po_breakdown_id in($all_po_id) and c.item_category=13 and a.entry_form in(2,22) and c.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 group by a.po_breakdown_id");// and c.receive_basis<>9
    $kniting_prod_arr=array();
    foreach($prodKnitDataArr as $row)
    {
        $kniting_prod_arr[$row[csf("po_breakdown_id")]]["knit_qnty_in"]=$row[csf("knit_qnty_in")];
        $kniting_prod_arr[$row[csf("po_breakdown_id")]]["knit_qnty_out"]=$row[csf("knit_qnty_out")];
        $kniting_prod_arr[$row[csf("po_breakdown_id")]]["knit_qnty_rec"]=$row[csf("knit_qnty_rec")];
    }

    //$prodFinDataArr=sql_select("select a.po_breakdown_id,sum(CASE WHEN c.knitting_source!=3 and c.item_category=2 and c.entry_form in(7,37,66) THEN a.quantity ELSE 0 END) AS finish_qnty_in,sum(CASE WHEN c.knitting_source=3 and c.item_category=2 and c.entry_form in(7,37,66) THEN a.quantity ELSE 0 END) AS finish_qnty_out,sum(CASE WHEN c.item_category=3 and c.entry_form=17 THEN a.quantity ELSE 0 END) AS woven_rec from order_wise_pro_details a, pro_finish_fabric_rcv_dtls b, inv_receive_master c where a.dtls_id=b.id and b.mst_id=c.id and a.po_breakdown_id in($all_po_id) and c.item_category in (2,3) and a.entry_form in(7,17,37,66) and c.entry_form in(7,17,37,66) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and c.receive_basis<>9 group by a.po_breakdown_id");

    $prodFinDataArr=sql_select("select a.po_breakdown_id, sum(CASE WHEN c.knitting_source!=3 and c.item_category=2 and c.entry_form in(7,37,66) THEN a.quantity ELSE 0 END) AS finish_qnty_in, sum(CASE WHEN c.knitting_source=3 and c.item_category=2 and c.entry_form in(7,37,66) THEN a.quantity ELSE 0 END) AS finish_qnty_out, sum(CASE WHEN c.item_category=3 and c.entry_form=17 THEN a.quantity ELSE 0 END) AS woven_rec from order_wise_pro_details a, pro_finish_fabric_rcv_dtls b, inv_receive_master c where a.trans_id=b.trans_id and b.mst_id=c.id and a.po_breakdown_id in($all_po_id) and c.item_category in (2,3) and a.entry_form in(7,17,37,66) and c.entry_form in(7,17,37,66) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and c.receive_basis<>9 group by a.po_breakdown_id");


    $finish_prod_arr=array();
    foreach($prodFinDataArr as $row)
    {
        $finish_prod_arr[$row[csf("po_breakdown_id")]]["finish_qnty_in"]=$row[csf("finish_qnty_in")];
        $finish_prod_arr[$row[csf("po_breakdown_id")]]["finish_qnty_out"]=$row[csf("finish_qnty_out")];
        $finish_prod_arr[$row[csf("po_breakdown_id")]]["woven_rec"]=$row[csf("woven_rec")];
    }
    $issueData=sql_select("select po_breakdown_id,
    sum(CASE WHEN entry_form=16 THEN quantity ELSE 0 END) AS grey_issue_qnty,
    sum(CASE WHEN entry_form=61 THEN quantity ELSE 0 END) AS grey_issue_qnty_roll_wise,
    sum(CASE WHEN entry_form=18 THEN quantity ELSE 0 END) AS issue_to_cut_qnty,
    sum(CASE WHEN entry_form=71 THEN quantity ELSE 0 END) AS issue_to_cut_qnty_roll_wise,
    sum(CASE WHEN entry_form=19 THEN quantity ELSE 0 END) AS woven_issue
    from order_wise_pro_details where po_breakdown_id in($all_po_id) and entry_form in(16,18,19,61,71) and status_active=1 and is_deleted=0 group by po_breakdown_id");


    $grey_cut_issue_arr=array();
    foreach($issueData as $row)
    {
        $grey_cut_issue_arr[$row[csf("po_breakdown_id")]]["grey_issue_qnty"]=$row[csf("grey_issue_qnty")]+$row[csf("grey_issue_qnty_roll_wise")];
        $grey_cut_issue_arr[$row[csf("po_breakdown_id")]]["issue_to_cut_qnty"]=$row[csf("issue_to_cut_qnty")]+$row[csf("issue_to_cut_qnty_roll_wise")];
        $grey_cut_issue_arr[$row[csf("po_breakdown_id")]]["woven_issue"]=$row[csf("woven_issue")];
    }
    $trimsDataArr=sql_select("select a.po_breakdown_id,
    sum(CASE WHEN a.entry_form=24 THEN a.quantity ELSE 0 END) AS recv_qnty,
    sum(CASE WHEN a.entry_form=25 THEN a.quantity ELSE 0 END) AS issue_qnty
    from order_wise_pro_details a, product_details_master b where a.prod_id=b.id and a.po_breakdown_id in($all_po_id) and b.item_category_id=4 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 group by a.po_breakdown_id");
    foreach($trimsDataArr as $row)
    {
        $trims_array[$row[csf('po_breakdown_id')]]['recv']=$row[csf('recv_qnty')];
        $trims_array[$row[csf('po_breakdown_id')]]['iss']=$row[csf('issue_qnty')];
    }

    $sql_consumtiont_qty=sql_select("select b.po_break_down_id, c.body_part_id, avg(b.cons) as requirment, avg(b.pcs) pcs
    from  wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls c
    where b.pre_cost_fabric_cost_dtls_id=c.id and b.pcs>0 and b.po_break_down_id in ($all_po_id) group by b.po_break_down_id, c.body_part_id ");
    $finish_consumtion_arr=array();
    foreach($sql_consumtiont_qty as $row_consum)
    {
        $con_avg=0;
        $con_avg= $row_consum[csf('requirment')]/$row_consum[csf('pcs')];///str_replace("'","",$row_sew[csf("pcs")]);


        $finish_consumtion_arr[$row_consum[csf('po_break_down_id')]]+=$con_avg;
    }
    $gmtsProdDataArr=sql_select("select  po_break_down_id,
    sum(CASE WHEN production_type=1 THEN production_quantity ELSE 0 END) AS cutting_qnty,
    sum(CASE WHEN production_type=2 and embel_name=1 and production_source=1 THEN production_quantity ELSE 0 END) AS print_issue_qnty_in,
    sum(CASE WHEN production_type=2 and embel_name=1 and production_source=3 THEN production_quantity ELSE 0 END) AS print_issue_qnty_out,
    sum(CASE WHEN production_type=3 and embel_name=1 and production_source=1 THEN production_quantity ELSE 0 END) AS print_recv_qnty_in,
    sum(CASE WHEN production_type=3 and embel_name=1 and production_source=3 THEN production_quantity ELSE 0 END) AS print_recv_qnty_out,
    sum(CASE WHEN production_type=2 and embel_name=2 and production_source=1 THEN production_quantity ELSE 0 END) AS emb_issue_qnty_in,
    sum(CASE WHEN production_type=2 and embel_name=2 and production_source=3 THEN production_quantity ELSE 0 END) AS emb_issue_qnty_out,
    sum(CASE WHEN production_type=3 and embel_name=2 and production_source=1 THEN production_quantity ELSE 0 END) AS emb_recv_qnty_in,
    sum(CASE WHEN production_type=3 and embel_name=2 and production_source=3 THEN production_quantity ELSE 0 END) AS emb_recv_qnty_out,
    sum(CASE WHEN production_type=4 and production_source=1 THEN production_quantity ELSE 0 END) AS sew_input_qnty_in,
    sum(CASE WHEN production_type=4 and production_source=3 THEN production_quantity ELSE 0 END) AS sew_input_qnty_out,
    sum(CASE WHEN production_type=5 and production_source=1 THEN production_quantity ELSE 0 END) AS sew_recv_qnty_in,
    sum(CASE WHEN production_type=5 and production_source=3 THEN production_quantity ELSE 0 END) AS sew_recv_qnty_out,
    sum(CASE WHEN production_type=8 and production_source=1 THEN production_quantity ELSE 0 END) AS finish_qnty_in,
    sum(CASE WHEN production_type=8 and production_source=3 THEN production_quantity ELSE 0 END) AS finish_qnty_out,
    sum(CASE WHEN production_type=3 and embel_name=3 and production_source=1 THEN production_quantity ELSE 0 END) AS wash_recv_qnty_in,
    sum(CASE WHEN production_type=3 and embel_name=3 and production_source=3 THEN production_quantity ELSE 0 END) AS wash_recv_qnty_out,
    sum(CASE WHEN production_type=3 and embel_name=1 THEN reject_qnty ELSE 0 END) AS print_reject_qnty,
    sum(CASE WHEN production_type=3 and embel_name=2 THEN reject_qnty ELSE 0 END) AS emb_reject_qnty,
    sum(CASE WHEN production_type=5 THEN reject_qnty ELSE 0 END) AS sew_reject_qnty,
    sum(CASE WHEN production_type=8 THEN reject_qnty ELSE 0 END) AS finish_reject_qnty,
    sum(CASE WHEN production_type=1 THEN reject_qnty ELSE 0 END) AS cutting_reject_qnty,
    sum(CASE WHEN production_type=7 THEN reject_qnty ELSE 0 END) AS iron_rej_qnty
    from pro_garments_production_mst where po_break_down_id in($all_po_id) and is_deleted=0 and status_active=1 group by po_break_down_id");

    $garment_prod_data_arr=array();
    foreach($gmtsProdDataArr as $row)
    {
        $garment_prod_data_arr[$row[csf("po_break_down_id")]]['cutting_qnty']=$row[csf("cutting_qnty")];
        $garment_prod_data_arr[$row[csf("po_break_down_id")]]['print_issue_qnty_in']=$row[csf("print_issue_qnty_in")];
        $garment_prod_data_arr[$row[csf("po_break_down_id")]]['print_issue_qnty_out']=$row[csf("print_issue_qnty_out")];
        $garment_prod_data_arr[$row[csf("po_break_down_id")]]['print_recv_qnty_in']=$row[csf("print_recv_qnty_in")];
        $garment_prod_data_arr[$row[csf("po_break_down_id")]]['print_recv_qnty_out']=$row[csf("print_recv_qnty_out")];
        $garment_prod_data_arr[$row[csf("po_break_down_id")]]['emb_issue_qnty_in']=$row[csf("emb_issue_qnty_in")];
        $garment_prod_data_arr[$row[csf("po_break_down_id")]]['emb_issue_qnty_out']=$row[csf("emb_issue_qnty_out")];
        $garment_prod_data_arr[$row[csf("po_break_down_id")]]['emb_recv_qnty_in']=$row[csf("emb_recv_qnty_in")];
        $garment_prod_data_arr[$row[csf("po_break_down_id")]]['emb_recv_qnty_out']=$row[csf("emb_recv_qnty_out")];
        $garment_prod_data_arr[$row[csf("po_break_down_id")]]['sew_input_qnty_in']=$row[csf("sew_input_qnty_in")];
        $garment_prod_data_arr[$row[csf("po_break_down_id")]]['sew_input_qnty_out']=$row[csf("sew_input_qnty_out")];
        $garment_prod_data_arr[$row[csf("po_break_down_id")]]['sew_recv_qnty_in']=$row[csf("sew_recv_qnty_in")];
        $garment_prod_data_arr[$row[csf("po_break_down_id")]]['sew_recv_qnty_out']=$row[csf("sew_recv_qnty_out")];
        $garment_prod_data_arr[$row[csf("po_break_down_id")]]['finish_qnty_in']=$row[csf("finish_qnty_in")];
        $garment_prod_data_arr[$row[csf("po_break_down_id")]]['finish_qnty_out']=$row[csf("finish_qnty_out")];
        $garment_prod_data_arr[$row[csf("po_break_down_id")]]['wash_recv_qnty_in']=$row[csf("wash_recv_qnty_in")];
        $garment_prod_data_arr[$row[csf("po_break_down_id")]]['wash_recv_qnty_out']=$row[csf("wash_recv_qnty_out")];
        $garment_prod_data_arr[$row[csf("po_break_down_id")]]['print_reject_qnty']=$row[csf("print_reject_qnty")];
        $garment_prod_data_arr[$row[csf("po_break_down_id")]]['emb_reject_qnty']=$row[csf("emb_reject_qnty")];
        $garment_prod_data_arr[$row[csf("po_break_down_id")]]['sew_reject_qnty']=$row[csf("sew_reject_qnty")];
        $garment_prod_data_arr[$row[csf("po_break_down_id")]]['finish_reject_qnty']=$row[csf("finish_reject_qnty")];
        $garment_prod_data_arr[$row[csf("po_break_down_id")]]['cutting_reject_qnty']=$row[csf("cutting_reject_qnty")];
        $garment_prod_data_arr[$row[csf("po_break_down_id")]]['iron_rej_qnty']=$row[csf("iron_rej_qnty")];
    }
    if(empty($all_po_id))
    {
        echo '<div align="left" style="width:1000px;"><h1 align="center" style="color:#f00;">Order not found</h></div>'; die;
    }
    if($cbo_search_type==1)
    {
        $tbl_width=6500;

        $ship_date_html="Shipment Date";
        $ex_fact_date_html="Ex-Fact. Date";
    }
    else
    {
        $tbl_width=6300;
        $ship_date_html="Last Shipment Date";
        $ex_fact_date_html="Last Ex-Fact. Date";
    }
    ob_start();
    ?>
    <div style="width:100%">
        <table width="<? echo $tbl_width;?>">
            <tr>
                <td align="center" width="100%" colspan="70" class="form_caption"><? echo $company_library[str_replace("'","",$cbo_company_name)]; ?></td>
            </tr>
        </table>

        <table width="<? echo $tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1">
            <thead>
                <tr style="font-size:13px">
                    <th width="40" rowspan="2">SL</th>
                    <th width="110" rowspan="2">Buyer</th>
                    <th width="52" rowspan="2">Job Year</th>
                    <th width="50" rowspan="2">Job No</th>
                    <th width="103" rowspan="2">Style No</th>
                    <?
                    if($cbo_search_type==1)
                    {
                        ?>
                        <th width="100" rowspan="2">Order No</th>
                        <?
                    }
                    ?>
                    <th width="80" rowspan="2">Order Qty. (Pcs)</th>
                    <?
                    if($cbo_search_type==1)
                    {
                        ?>
                        <th width="72" rowspan="2"><? echo $ship_date_html; ?></th>
                        <th width="70" rowspan="2"><? echo $ex_fact_date_html; ?></th>
                        <?
                    }
                    ?>
                    <th width="80" rowspan="2">Fin Fab Req. <br/><font style="font-size:9px; font-weight:100">(As Per Booking)</font></th>

                    <th colspan="3" width="244">Fabric Receive</th>
                    <th colspan="3" width="244">Fabric Issue</th>

                    <th rowspan="2" width="80">Fin Left Over</th>
                    <th rowspan="2" width="80">Gmts. Req (Po Qty)</th>
                    <th rowspan="2" width="80">Cutting Qty</th>

                    <th colspan="3" width="243">Gmts. Print Issue</th>
                    <th colspan="3" width="243">Gmts. Print Receive</th>

                    <th rowspan="2" width="80">Gmts. Reject</th>

                    <th colspan="4" width="324">Sewing Input</th>

                    <th rowspan="2" width="100">Accessories Status</th>

                    <th colspan="5" width="405">Sewing Output</th>
                    <th colspan="4" width="324">Wash</th>
                    <th colspan="5" width="405">Finishing</th>

                    <th rowspan="2" width="81">Total Reject</th>
                    <th rowspan="2" width="80">Ex-Factory</th>
                    <th rowspan="2" width="82">Left Over</th>
                    <th rowspan="2" width="80">Short Ex-Fac. Qty</th>
                    <th rowspan="2" width="82">Process Loss Cutting</th>
                    <th rowspan="2" width="80">Process Loss cutting &#37;</th>
                </tr>
                <tr>
                     <th width="80">Received</th>
                     <th width="80">Trans. In</th>
                     <th width="84">Total</th>

                     <th width="80">Issue To Cutting</th>
                     <th width="80">Trans. Out</th>
                     <th width="83">Total</th>

                     <th width="80">In-house</th>
                     <th width="80">SubCon</th>
                     <th width="80">Total</th>

                     <th width="80">In-house</th>
                     <th width="80">SubCon</th>
                     <th width="80">Total</th>

                     <th width="80">In-house</th>
                     <th width="80">SubCon</th>
                     <th width="80">Total</th>
                     <th width="82">Balance</th>

                     <th width="82">In-house</th>
                     <th width="82">SubCon</th>
                     <th width="82">Total</th>
                     <th width="81">Balance</th>
                     <th width="81">Reject</th>

                     <th width="83">In-house</th>
                     <th width="81">SubCon</th>
                     <th width="81">Total</th>
                     <th width="81">Balance</th>

                     <th width="80">In-house</th>
                     <th width="82">SubCon</th>
                     <th width="80">Total</th>
                     <th width="82">Balance</th>
                     <th width="81">Reject</th>
                </tr>
            </thead>
        </table>

        <div style="width:<? echo $tbl_width+18;?>px; overflow-y:scroll; max-height:300px;font-size:12px; overflow-x:hidden;" id="scroll_body">
            <table width="<? echo $tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
                <?
                $i=1;
                if($cbo_search_type==1)
                {
                    foreach($result_data_arr as $po_id=>$val)
                    {
                        $ratio=$val["ratio"];
                        $tot_po_qnty=$val["po_qnty"];
                        $exfactory_qnty=$val["ex_factory_qnty"]-$ex_factory_qty_arr[$po_id];
                        $plan_cut_qty=$val["plan_cut"];
                        $job_no=$val["job_no"];
                        $dzn_qnty=0; $balance=0; $job_mkt_required=0; $yarn_issued=0;
                        if($costing_per_id_library[$job_no]==1) $dzn_qnty=12;
                        else if($costing_per_id_library[$job_no]==3) $dzn_qnty=12*2;
                        else if($costing_per_id_library[$job_no]==4) $dzn_qnty=12*3;
                        else if($costing_per_id_library[$job_no]==5) $dzn_qnty=12*4;
                        else $dzn_qnty=1;
                        $dzn_qnty=$dzn_qnty*$ratio;

                        $yarn_req_job=$yarn_qty_arr[$po_id];//$dataArrayYarnReq[$job_no];
                        $yarn_required=$yarn_qty_arr[$po_id];//$plan_cut_qty*($yarn_req_job/$dzn_qnty);
                        $yarn_issue_inside=$yarn_issue_arr[$po_id]["issue_qnty_in"]-$yarn_issue_rtn_arr[$po_id]["return_qnty_in"];
                        $yarn_issue_outside=$yarn_issue_arr[$po_id]["issue_qnty_out"]-$yarn_issue_rtn_arr[$po_id]["return_qnty_out"];
                        $transfer_in_qnty_yarn=$transfer_data_arr[$po_id]["transfer_in_qnty_yarn"];
                        $transfer_out_qnty_yarn=$transfer_data_arr[$po_id]["transfer_out_qnty_yarn"];
                        $total_issued=$yarn_issue_inside+$yarn_issue_outside+$transfer_in_qnty_yarn-$transfer_out_qnty_yarn;
                        $under_over_issued=$yarn_required-$total_issued;

                        $grey_fabric_req_qnty=$booking_req_arr[$po_id]['gray'];//$grey_finish_require_arr[$po_id]["grey_req"];
                        $knit_qnty_in=$kniting_prod_arr[$po_id]["knit_qnty_in"];
                        $knit_qnty_out=$kniting_prod_arr[$po_id]["knit_qnty_out"];
                        $knit_gray_rec=$kniting_prod_arr[$po_id]["knit_qnty_rec"];
                        $transfer_in_qnty_knit=$transfer_data_arr[$po_id]["transfer_in_qnty_knit"];
                        $transfer_out_qnty_knit=$transfer_data_arr[$po_id]["transfer_out_qnty_knit"];

                        $total_knitting=$knit_qnty_in+$knit_qnty_out+$transfer_in_qnty_knit-$transfer_out_qnty_knit;

                        $process_loss=($yarn_issue_inside+$yarn_issue_outside)-($knit_qnty_in+$knit_qnty_out);
                        $under_over_prod=$grey_fabric_req_qnty-$total_knitting;
                        $issuedToDyeQnty=$grey_cut_issue_arr[$po_id]["grey_issue_qnty"];
                        $left_over=$total_knitting-$issuedToDyeQnty;

                        $finish_fabric_req_qnty=$booking_req_arr[$po_id]['fin'];//$grey_finish_require_arr[$po_id]["finish_req"];
                        $finish_qnty_in=$finish_prod_arr[$po_id]["finish_qnty_in"];
                        $finish_qnty_out=$finish_prod_arr[$po_id]["finish_qnty_out"];
                        $transfer_in_qnty_finish=$transfer_data_arr[$po_id]["transfer_in_qnty_finish"];
                        $transfer_out_qnty_finish=$transfer_data_arr[$po_id]["transfer_out_qnty_finish"];
                        $total_finishing=$finish_qnty_in+$finish_qnty_out+$transfer_in_qnty_finish-$transfer_out_qnty_finish;
                        $process_loss_dyeing=$issuedToDyeQnty-($finish_qnty_in+$finish_qnty_out);
                        $under_over_finish_prod=$finish_fabric_req_qnty-$total_finishing;
                        $issuedToCutQnty=$grey_cut_issue_arr[$po_id]["issue_to_cut_qnty"];
                        $finish_left_over=$total_finishing-$issuedToCutQnty;

                        $wovenReqQty=$booking_req_arr[$po_id]['woven'];
                        $wovenRecQty=$finish_prod_arr[$po_id]["woven_rec"];
                        $wovenFabRecBal=$wovenReqQty-$wovenRecQty;
                        $wovenIssueQty=$grey_cut_issue_arr[$po_id]["woven_issue"];
                        $wovenFabIssueBal=$wovenRecQty-$wovenIssueQty;


                        $cuttingQty=$garment_prod_data_arr[$po_id]['cutting_qnty'];
                        if($finish_consumtion_arr[$po_id] !=0){
                            $possible_cut_pcs=$issuedToCutQnty/$finish_consumtion_arr[$po_id];
                        }
                        else{
                            $possible_cut_pcs = 0;
                        }

                        $cutting_process_loss=$possible_cut_pcs-$cuttingQty;

                        $print_issue_qnty_in=$garment_prod_data_arr[$po_id]['print_issue_qnty_in'];
                        $print_issue_qnty_out=$garment_prod_data_arr[$po_id]['print_issue_qnty_out'];
                        $total_print_issued=$print_issue_qnty_in+$print_issue_qnty_out;
                        $print_recv_qnty_in=$garment_prod_data_arr[$po_id]['print_recv_qnty_in'];
                        $print_recv_qnty_out=$garment_prod_data_arr[$po_id]['print_recv_qnty_out'];
                        $total_print_recv=$print_recv_qnty_in+$print_recv_qnty_out;
                        $print_reject_qnty=$garment_prod_data_arr[$po_id]['print_reject_qnty'];

                        $sew_input_qnty_in=$garment_prod_data_arr[$po_id]['sew_input_qnty_in'];
                        $sew_input_qnty_out=$garment_prod_data_arr[$po_id]['sew_input_qnty_out'];
                        $total_sew_input=$sew_input_qnty_in+$sew_input_qnty_out;
                        $sew_input_balance_qnty=$tot_po_qnty-$total_sew_input;

                        $sew_recv_qnty_in=$garment_prod_data_arr[$po_id]['sew_recv_qnty_in'];
                        $sew_recv_qnty_out=$garment_prod_data_arr[$po_id]['sew_recv_qnty_out'];
                        $total_sew_recv=$sew_recv_qnty_in+$sew_recv_qnty_out;
                        $sew_balance_recv_qnty=$tot_po_qnty-$total_sew_recv;
                        $sew_reject_qnty=$garment_prod_data_arr[$po_id]['sew_reject_qnty'];
                        $cutting_reject_qnty=$garment_prod_data_arr[$po_id]['cutting_reject_qnty'];

                        $wash_recv_qnty_in=$garment_prod_data_arr[$po_id]['wash_recv_qnty_in'];
                        $wash_recv_qnty_out=$garment_prod_data_arr[$po_id]['wash_recv_qnty_out'];
                        $total_wash_recv=$wash_recv_qnty_in+$wash_recv_qnty_out;
                        $wash_balance_qnty=$tot_po_qnty-$total_wash_recv;

                        $gmt_finish_in=$garment_prod_data_arr[$po_id]['finish_qnty_in'];
                        $gmt_finish_out=$garment_prod_data_arr[$po_id]['finish_qnty_out'];
                        $total_gmts_finish_qnty=$gmt_finish_in+$gmt_finish_out;
                        $finish_balance_qnty=$tot_po_qnty-$total_gmts_finish_qnty;
                        $finish_reject_qnty=$garment_prod_data_arr[$po_id]['finish_reject_qnty'];
                        $left_over_finish_gmts=$total_gmts_finish_qnty-$exfactory_qnty;

                        $short_excess_exFactoryQty=$tot_po_qnty-$exfactory_qnty;

                        $trims_recv=$trims_array[$po_id]['recv'];
                        $trims_issue=$trims_array[$po_id]['iss'];
                        $tot_trims_left_over_qnty=$trims_recv+$trims_issue;

                        $emb_reject_qnty=$garment_prod_data_arr[$po_id]['emb_reject_qnty'];
                        $process_loss_finishing=($finish_qnty_in+$finish_qnty_out)-$total_finishing;


                        $iron_rej_qnty=$garment_prod_data_arr[$po_id]['iron_rej_qnty'];
                        $total_reject_qnty=$sew_reject_qnty+$cutting_reject_qnty+$finish_reject_qnty+$iron_rej_qnty+$emb_reject_qnty;
                        $reject_button='<p><a href="##" onclick="openmypage_rej('.$po_id.",".$cbo_company_name.",'reject_qty','$cbo_search_type'".')">'.number_format($total_reject_qnty,2).'</a></p>';
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="font-size:13px">
                            <td width="62"><? echo $i; ?></td>
                            <td width="175"><div style="word-wrap:break-word; width:110px"><? echo $buyer_arr[$val["buyer_name"]]; ?></div></td>
                            <td width="80" align="center"><p><? echo $val["job_year"]; ?>&nbsp;</p></td>
                            <td width="80" align="center"><p><? echo $val["job_no_prefix_num"]; ?>&nbsp;</p></td>
                            <td width="160"><div style="word-wrap:break-word; width:100px"><? echo $val["style_ref_no"]; ?></div></td>
                            <td width="158"><div style="word-wrap:break-word; width:100px"><? echo $val["po_number"]; ?></div></td>
                            <td width="128" align="right" bgcolor="#FFFFCC"><p><? echo number_format($tot_po_qnty); ?></p></td>
                            <td width="112"><p><? if(trim($val["pub_shipment_date"])!="" && trim($val["pub_shipment_date"])!='0000-00-00') echo change_date_format($val["pub_shipment_date"]); ?>&nbsp;</p></td>
                            <td width="112"><p><? if(trim($val["ex_factory_date"])!="" && trim($val["ex_factory_date"])!='0000-00-00') echo change_date_format($val["ex_factory_date"]); ?>&nbsp;</p></td>
                            <td align="right" width="125"><? echo number_format($finish_fabric_req_qnty,2); ?></td>

                            <td align="right" width="128"><? echo number_format($wovenRecQty,2); ?></td>
                            <td align="right" width="128"><? echo number_format($transfer_in_qnty_finish,2); ?></td>
                            <td align="right" width="128" title="<? echo $po_id; ?>"><? echo number_format($wovenFabRecBal,2); ?></td>

                            <td align="right" width="128"><? echo number_format($wovenIssueQty,2); ?></td>
                            <td align="right" width="128"><? echo number_format($transfer_out_qnty_finish,2); ?></td>
                            <td align="right" width="128"><? echo number_format($wovenFabIssueBal,2); ?></td>

                            <td align="right" width="128" title="<? echo $total_finishing.'-'.$issuedToCutQnty; ?>"><? echo number_format($finish_left_over,2); ?></td>
                            <td align="right" width="128" bgcolor="#FFFFCC"><? echo number_format($tot_po_qnty); ?></td>
                            <td align="right" width="128"><? echo number_format($cuttingQty); ?></td>


                            <td align="right" width="128"><? echo number_format($print_issue_qnty_in); ?></td>
                            <td align="right" width="128"><? echo number_format($print_issue_qnty_out); ?></td>
                            <td align="right" width="128"><? echo number_format($total_print_issued); ?></td>

                            <td align="right" width="128"><? echo number_format($print_recv_qnty_in); ?></td>
                            <td align="right" width="128"><? echo number_format($print_recv_qnty_out); ?></td>
                            <td align="right" width="128"><? echo number_format($total_print_recv); ?></td>


                            <td align="right" width="128"><? echo number_format($print_reject_qnty); ?></td>


                            <td align="right" width="128"><? echo number_format($sew_input_qnty_in); ?></td>
                            <td align="right" width="128"><? echo number_format($sew_input_qnty_out); ?></td>
                            <td align="right" width="128"><? echo number_format($total_sew_input); ?></td>
                            <td align="right" width="128"><? echo number_format($sew_input_balance_qnty); ?></td>


                            <td align="center" width="161"><a href="javascript:open_trims_dtls('<? echo $po_id;?>','<? echo $tot_po_qnty; ?>','<? echo $ratio; ?>','Trims Info','trims_popup')">View</a></td>


                            <td align="right" width="128"><? echo number_format($sew_recv_qnty_in); ?></td>
                            <td align="right" width="128"><? echo number_format($sew_recv_qnty_out); ?></td>
                            <td align="right" width="128"><? echo number_format($total_sew_recv); ?></td>
                            <td align="right" width="128"><? echo number_format($sew_balance_recv_qnty); ?></td>
                            <td align="right" width="128"><? echo number_format($sew_reject_qnty); ?></td>


                            <td align="right" width="128"><? echo number_format($wash_recv_qnty_in); ?></td>
                            <td align="right" width="128"><? echo number_format($wash_recv_qnty_out); ?></td>
                            <td align="right" width="128"><? echo number_format($total_wash_recv); ?></td>
                            <td align="right" width="128"><? echo number_format($wash_balance_qnty); ?></td>

                            <td align="right" width="128"><? echo number_format($gmt_finish_in); ?></td>
                            <td align="right" width="128"><? echo number_format($gmt_finish_out); ?></td>
                            <td align="right" width="128"><? echo number_format($total_gmts_finish_qnty); ?></td>
                            <td align="right" width="128"><? echo number_format($finish_balance_qnty); ?></td>
                            <td align="right" width="128"><? echo number_format($finish_reject_qnty); ?></td>


                            <td align="right" width="128"><? echo $reject_button; ?></td>
                            <td align="right" width="128">
                                <a href="##" onClick="generate_ex_factory_popup('ex_factory_popup','<? echo $val["job_no_prefix_num"];?>','<? echo $po_id; ?>','550px')"><? echo  number_format($exfactory_qnty); ?></a>
                            </td>
                            <td align="right" width="128"><? echo number_format($left_over_finish_gmts); ?></td>
                            <td align="right" width="128"><? echo number_format($short_excess_exFactoryQty); ?></td>
                            <td align="right" width="128"><? echo number_format($cutting_process_loss); ?></td>
                            <td align="right" width="128">
                                <?
                                    if($tot_po_qnty!=0){
                                        $process_loss_cutting_per = ($cuttingQty-$tot_po_qnty)*100/$tot_po_qnty;
                                    }
                                    else{
                                        $process_loss_cutting_per = 0;
                                    }
                                    echo number_format($process_loss_cutting_per);
                                ?>
                            </td>
                        </tr>
                        <?
                        $tot_order_qty+=$tot_po_qnty;

                        $tot_fin_req_qty += $finish_fabric_req_qnty;

                        $tot_wovenRecQty+=$wovenRecQty;
                        $tot_fin_transIn_qty+=$transfer_in_qnty_finish;
                        $tot_wovenRecBalQty+=$wovenFabRecBal;

                        $tot_wovenIssueQty+=$wovenIssueQty;
                        $tot_fin_transOut_qty+=$transfer_out_qnty_finish;
                        $tot_wovenIssueBalQty+=$wovenFabIssueBal;

                        $tot_fin_lftOver_qty+=$finish_left_over;
                        $tot_gmt_qty+=$tot_po_qnty;

                        $tot_cutting_qty+=$cuttingQty;
                        $tot_printIssIn_qty+=$print_issue_qnty_in;
                        $tot_printIssOut_qty+=$print_issue_qnty_out;
                        $tot_printIssue_qty+=$total_print_issued;

                        $tot_printRcvIn_qty+=$print_recv_qnty_in;
                        $tot_printRcvOut_qty+=$print_recv_qnty_out;
                        $tot_printRcv_qty+=$total_print_recv;
                        $tot_printRjt_qty+=$print_reject_qnty;

                        $tot_sewInInput_qty+=$sew_input_qnty_in;
                        $tot_sewInOutput_qty+=$sew_input_qnty_out;
                        $tot_sewIn_qty+=$total_sew_input;
                        $tot_sewInBal_qty+=$sew_input_balance_qnty;

                        $tot_sewRcvIn_qty+=$sew_recv_qnty_in;
                        $tot_sewRcvOut_qty+=$sew_recv_qnty_out;
                        $tot_sewRcv_qty+=$total_sew_recv;
                        $tot_sewRcvBal_qty+=$sew_balance_recv_qnty;
                        $tot_sewRcvRjt_qty+=$sew_reject_qnty;

                        $tot_washRcvIn_qty+=$wash_recv_qnty_in;
                        $tot_washRcvOut_qty+=$wash_recv_qnty_out;
                        $tot_washRcv_qty+=$total_wash_recv;
                        $tot_washRcvBal_qty+=$wash_balance_qnty;

                        $tot_gmtFinIn_qty+=$gmt_finish_in;
                        $tot_gmtFinOut_qty+=$gmt_finish_out;
                        $tot_gmtFin_qty+=$total_gmts_finish_qnty;
                        $tot_gmtFinBal_qty+=$finish_balance_qnty;
                        $tot_gmtFinRjt_qty+=$finish_reject_qnty;

                        $tot_gmtEx_qty+=$exfactory_qnty;
                        $tot_gmtFinLeftOver_qty+=$left_over_finish_gmts;
                        $tot_shortExcess_exFactory_qty+=$short_excess_exFactoryQty;
                        $tot_prLossCut_qty+=$cutting_process_loss;
                        $tot_process_loss_cutting_per += $process_loss_cutting_per;

                        //old

                        /*$tot_fin_in_qty+=$finish_qnty_in;
                        $tot_fin_out_qty+=$finish_qnty_out;
                        $tot_fin_qty+=$total_finishing;
                        $tot_fin_prLoss_qty+=$process_loss_finishing;
                        $tot_fin_undOver_qty+=$under_over_finish_prod;
                        $tot_fin_issCut_qty+=$issuedToCutQnty;
                        $tot_wovenReqQty+=$wovenReqQty;
                        $tot_prLoss_qty+=$process_loss;
                        $tot_prLossDye_qty+=$process_loss_dyeing;
                        $tot_process_loss_yern_per += $process_loss_yern_per;
                        $tot_process_loss_dyeing_per += $process_loss_dyeing_per;*/

                        $i++;
                    }
                }
                else
                {
                    foreach($result_job_wise as $po_id_job)
                    {
                        $po_id_arr=array_unique(explode(",",substr($po_id_job,0,-1)));
                        $tot_po_qnty=$yarn_required=$tot_exfactory_qnty=$grey_fabric_req_qnty=$finish_fabric_req_qnty=$yarn_issue_inside=$yarn_issue_outside=$transfer_in_qnty_yarn=$transfer_in_qnty_yarn=$transfer_out_qnty_yarn=$transfer_in_qnty_knit=$transfer_in_qnty_finish=$transfer_out_qnty_finish=$knit_qnty_in=$issuedToDyeQnty=$issuedToCutQnty=$finish_qnty_in=$finish_qnty_out=$finish_reject_qnty=$print_issue_qnty_in=$print_issue_qnty_out=$print_recv_qnty_in=$print_recv_qnty_out=$print_reject_qnty=$sew_input_qnty_in=$sew_input_qnty_out=$sew_recv_qnty_in=$sew_recv_qnty_out=$sew_reject_qnty=$wash_recv_qnty_in=$wash_recv_qnty_out=$trims_recv=$trims_issue=$emb_reject_qnty=$gmt_finish_in=$gmt_finish_out=$gmt_finish_reject_qnty=$total_reject_qnty=$tot_process_loss_yern_per=$tot_process_loss_dyeing_per=0;
                        foreach($po_id_arr as $po_id)
                        {
                            $tot_po_qnty +=$result_data_arr[$po_id]["po_qnty"];
                            $tot_exfactory_qnty +=$result_data_arr[$po_id]["ex_factory_qnty"]-$ex_factory_qty_arr[$po_id];
                            $yarn_required+=$yarn_qty_arr[$po_id];
                            $grey_fabric_req_qnty +=$booking_req_arr[$po_id]['gray'];;
                            $finish_fabric_req_qnty +=$booking_req_arr[$po_id]['fin'];

                            $yarn_issue_inside +=$yarn_issue_arr[$po_id]["issue_qnty_in"]-$yarn_issue_rtn_arr[$po_id]["return_qnty_in"];
                            $yarn_issue_outside +=$yarn_issue_arr[$po_id]["issue_qnty_out"]-$yarn_issue_rtn_arr[$po_id]["return_qnty_out"];
                            $transfer_in_qnty_yarn +=$transfer_data_arr[$po_id]["transfer_in_qnty_yarn"];
                            $transfer_out_qnty_yarn +=$transfer_data_arr[$po_id]["transfer_out_qnty_yarn"];
                            $transfer_in_qnty_knit +=$transfer_data_arr[$po_id]["transfer_in_qnty_knit"];
                            $transfer_out_qnty_knit +=$transfer_data_arr[$po_id]["transfer_out_qnty_knit"];
                            $transfer_in_qnty_finish +=$transfer_data_arr[$po_id]["transfer_in_qnty_finish"];
                            $transfer_out_qnty_finish +=$transfer_data_arr[$po_id]["transfer_out_qnty_finish"];

                            $total_issued =$yarn_issue_inside+$yarn_issue_outside+$transfer_in_qnty_yarn-$transfer_out_qnty_yarn;
                            $under_over_issued =$grey_fabric_req_qnty-$total_issued;

                            $knit_qnty_in +=$kniting_prod_arr[$po_id]["knit_qnty_in"];
                            $knit_qnty_out +=$kniting_prod_arr[$po_id]["knit_qnty_out"];
                            $total_knitting=$knit_qnty_in+$knit_qnty_out+$transfer_in_qnty_knit-$transfer_out_qnty_knit;
                            $process_loss=($knit_qnty_in+$knit_qnty_out)-$total_issued;
                            $under_over_prod=$grey_fabric_req_qnty-$total_knitting;
                            $issuedToDyeQnty +=$grey_cut_issue_arr[$po_id]["grey_issue_qnty"];
                            $left_over=$total_knitting-$issuedToDyeQnty;

                            $issuedToCutQnty +=$grey_cut_issue_arr[$po_id]["issue_to_cut_qnty"];

                            $finish_qnty_in +=$finish_prod_arr[$po_id]["finish_qnty_in"];
                            $finish_qnty_out +=$finish_prod_arr[$po_id]["finish_qnty_out"];
                            $total_finish_qnty=$finish_qnty_in+$finish_qnty_out;
                            $finish_balance_qnty=$tot_po_qnty-$total_finish_qnty;
                            $finish_reject_qnty +=$garment_prod_data_arr[$po_id]['finish_reject_qnty'];
                            $left_over_finish_gmts=$total_finish_qnty-$tot_exfactory_qnty;

                            $total_finishing=$finish_qnty_in+$finish_qnty_out+$transfer_in_qnty_finish-$transfer_out_qnty_finish;
                            $process_loss_finishing=($finish_qnty_in+$finish_qnty_out)-$total_finishing;
                            $under_over_finish_prod=$finish_fabric_req_qnty-$total_finishing;
                            $finish_left_over=$total_finishing-$issuedToCutQnty;

                            $print_issue_qnty_in +=$garment_prod_data_arr[$po_id]['print_issue_qnty_in'];
                            $print_issue_qnty_out +=$garment_prod_data_arr[$po_id]['print_issue_qnty_out'];
                            $print_recv_qnty_in +=$garment_prod_data_arr[$po_id]['print_recv_qnty_in'];
                            $print_recv_qnty_out +=$garment_prod_data_arr[$po_id]['print_recv_qnty_out'];
                            $print_reject_qnty +=$garment_prod_data_arr[$po_id]['print_reject_qnty'];

                            $total_print_issued=$print_issue_qnty_in+$print_issue_qnty_out;
                            $total_print_recv=$print_recv_qnty_in+$print_recv_qnty_out;

                            $sew_input_qnty_in +=$garment_prod_data_arr[$po_id]['sew_input_qnty_in'];
                            $sew_input_qnty_out +=$garment_prod_data_arr[$po_id]['sew_input_qnty_out'];
                            $total_sew_issued=$sew_input_qnty_in+$sew_input_qnty_out;
                            $sew_balance_qnty=$tot_po_qnty-$total_sew_issued;

                            $sew_recv_qnty_in +=$garment_prod_data_arr[$po_id]['sew_recv_qnty_in'];
                            $sew_recv_qnty_out +=$garment_prod_data_arr[$po_id]['sew_recv_qnty_out'];
                            $total_sew_recv=$sew_recv_qnty_in+$sew_recv_qnty_out;

                            $sew_balance_recv_qnty=$tot_po_qnty-$total_sew_recv;

                            $sew_reject_qnty +=$garment_prod_data_arr[$po_id]['sew_reject_qnty'];

                            $wash_recv_qnty_in +=$garment_prod_data_arr[$po_id]['wash_recv_qnty_in'];
                            $wash_recv_qnty_out +=$garment_prod_data_arr[$po_id]['wash_recv_qnty_out'];
                            $total_wash_recv=$wash_recv_qnty_in+$wash_recv_qnty_out;
                            $wash_balance_qnty=$tot_po_qnty-$total_wash_recv;

                            $gmt_finish_in+=$garment_prod_data_arr[$po_id]['finish_qnty_in'];
                            $gmt_finish_out+=$garment_prod_data_arr[$po_id]['finish_qnty_out'];
                            $total_gmts_finish_qnty=$gmt_finish_in+$gmt_finish_out;
                            $finish_balance_qnty=$tot_po_qnty-$total_gmts_finish_qnty;
                            $gmt_finish_reject_qnty+=$garment_prod_data_arr[$po_id]['finish_reject_qnty'];
                            $left_over_finish_gmts=$total_gmts_finish_qnty-$tot_exfactory_qnty;

                            $trims_recv+=$trims_array[$po_id]['recv'];
                            $trims_issue+=$trims_array[$po_id]['iss'];
                            $tot_trims_left_over_qnty=$trims_recv+$trims_issue;

                            $emb_reject_qnty +=$garment_prod_data_arr[$po_id]['emb_reject_qnty'];
                            $cutting_reject_qnty +=$garment_prod_data_arr[$po_id]['cutting_reject_qnty'];
                            $iron_rej_qnty=$garment_prod_data_arr[$po_id]['iron_rej_qnty'];
                            $tot_reject_qnty=$sew_reject_qnty+$cutting_reject_qnty+$gmt_finish_reject_qnty+$iron_rej_qnty+$emb_reject_qnty;
                            $reject_button='<p><a href="##" onclick="openmypage_rej('.$po_id.",".$cbo_company_name.",'reject_qty','$cbo_search_type'".')">'.number_format($tot_reject_qnty,2).'</a></p>';
                        }
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="font-size:13px">
                            <td width="40"><? echo $i; ?></td>
                            <td width="110"><div style="word-wrap:break-word; width:110px"><? echo $buyer_arr[$result_data_arr[$po_id]["buyer_name"]]; ?></div></td>
                            <td width="50" align="center"><? echo $result_data_arr[$po_id]["job_year"]; ?></td>
                            <td width="50" align="center"><? echo $result_data_arr[$po_id]["job_no_prefix_num"]; ?></td>
                            <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $result_data_arr[$po_id]["style_ref_no"]; ?></div></td>
                            <td width="80" align="right" bgcolor="#FFFFCC"><? echo number_format($tot_po_qnty,0); ?></td>


                            <td align="right" width="80"><? echo number_format($finish_fabric_req_qnty,2); ?></td>

                            <!-- Fabric Receive -->
                            <td align="right" width="80"><? echo number_format($finish_qnty_in,2); ?></td>
                            <td align="right" width="80"><? echo number_format($finish_qnty_out,2); ?></td>
                            <td align="right" width="80"><? echo number_format($transfer_in_qnty_finish,2); ?></td>
                            <td align="right" width="80"><? echo number_format($transfer_out_qnty_finish,2); ?></td>
                            <td align="right" width="80"><? echo number_format($total_finishing,2); ?></td>
                            <td align="right" width="80"><? echo number_format($process_loss_finishing,2); ?></td>
                            <td align="right" width="80"><? echo number_format($under_over_finish_prod,2); ?></td>
                            <td align="right" width="80"><? echo number_format($issuedToCutQnty,2); ?></td>
                            <td align="right" width="80"><? echo number_format($finish_left_over,2); ?></td>

                            <td align="right" width="80" bgcolor="#FFFFCC"><? echo number_format($tot_po_qnty); ?></td>
                            <td align="right" width="80"><? echo number_format($print_issue_qnty_in); ?></td>
                            <td align="right" width="80"><? echo number_format($print_issue_qnty_out); ?></td>
                            <td align="right" width="80"><? echo number_format($total_print_issued); ?></td>
                            <td align="right" width="80"><? echo number_format($print_recv_qnty_in); ?></td>
                            <td align="right" width="80"><? echo number_format($print_recv_qnty_out); ?></td>
                            <td align="right" width="80"><? echo number_format($total_print_recv); ?></td>
                            <td align="right" width="80"><? echo number_format($print_reject_qnty); ?></td>

                            <td align="right" width="80"><? echo number_format($sew_input_qnty_in); ?></td>
                            <td align="right" width="80"><? echo number_format($sew_input_qnty_out); ?></td>
                            <td align="right" width="80"><? echo number_format($total_sew_issued); ?></td>
                            <td align="right" width="80"><? echo number_format($sew_balance_qnty); ?></td>

                            <td width="100" align="center">View</td>

                            <td align="right" width="80"><? echo number_format($sew_recv_qnty_in); ?></td>
                            <td align="right" width="80"><? echo number_format($sew_recv_qnty_out); ?></td>
                            <td align="right" width="80"><? echo number_format($total_sew_recv); ?></td>
                            <td align="right" width="80"><? echo number_format($sew_balance_recv_qnty); ?></td>
                            <td align="right" width="80"><? echo number_format($sew_reject_qnty); ?></td>

                            <td width="80" align="right"><? echo number_format($wash_recv_qnty_in); ?></td>
                            <td width="80" align="right"><? echo number_format($wash_recv_qnty_out); ?></td>
                            <td width="80" align="right"><? echo number_format($total_wash_recv); ?></td>
                            <td width="80" align="right"><? echo number_format($wash_balance_qnty); ?></td>

                            <td width="80" align="right"><? echo number_format($gmt_finish_in); ?></td>
                            <td width="80" align="right"><? echo number_format($gmt_finish_out); ?></td>
                            <td width="80" align="right"><? echo number_format($total_gmts_finish_qnty); ?></td>
                            <td width="80" align="right"><? echo number_format($finish_balance_qnty); ?></td>
                            <td width="80" align="right"><? echo number_format($gmt_finish_reject_qnty); ?></td>
                            <td align="right" width="80"><? echo $reject_button; ?></td>

                            <td align="right" width="80">
                                <a href="##" onClick="generate_ex_factory_popup('ex_factory_popup','<? echo $result_data_arr[$po_id]["job_no_prefix_num"];?>','<? echo $po_id; ?>','550px')"><? echo  number_format($tot_exfactory_qnty); ?></a>
                                <? //echo number_format($tot_exfactory_qnty); ?></td>
                            <td align="right" width="80"><? echo number_format($left_over_finish_gmts); ?></td>

                            <td align="right" width="80"><? echo number_format($left_over); ?></td>
                            <td align="right" width="80"><? echo number_format($finish_left_over); ?></td>
                            <td align="right" width="80"><? echo number_format($left_over_finish_gmts); ?></td>
                            <td align="right" width="80"><? echo number_format($tot_trims_left_over_qnty); ?></td>

                            <td align="right" width="80"><? echo number_format($print_reject_qnty); ?></td>
                            <td align="right" width="80"><? echo number_format($emb_reject_qnty); ?></td>
                            <td align="right" width="80"><? echo number_format($sew_reject_qnty); ?></td>
                            <td align="right" width="80"><? echo number_format($finish_reject_qnty); ?></td>

                            <td align="right" width="80"><? echo number_format($process_loss); ?></td>
                            <td align="right" width="80"><?
                                if($total_issued!=0){
                                    $process_loss_yern_per = ($process_loss*100)/$total_issued;
                                }
                                else{
                                    $process_loss_yern_per = 0;
                                }
                                echo number_format($process_loss_yern_per);
                                ?></td>
                            <td align="right" width="80"><? echo number_format($process_loss_dyeing); ?></td>
                            <td align="right" width="80"><?
                                if($issuedToDyeQnty != 0){
                                    $process_loss_dyeing_per = ($issuedToDyeQnty-$total_finishing)*100/$issuedToDyeQnty;
                                }
                                else{
                                    $process_loss_dyeing_per = 0;
                                }
                                echo number_format($process_loss_dyeing_per);
                                ?></td>
                            <td align="right" width="80"><? echo number_format($process_loss_cutting); ?></td>
                            <td align="right"><?
                                if($tot_po_qnty!=0){
                                    $process_loss_cutting_per = ($cuttingQty-$tot_po_qnty)*100/$tot_po_qnty;
                                }
                                else{
                                    $process_loss_cutting_per = 0;
                                }
                                echo number_format($process_loss_cutting_per);
                                ?></td>
                        </tr>
                        <?
                        $tot_order_qty+=$tot_po_qnty;

                        $tot_fin_req_qty+=$finish_fabric_req_qnty;
                        $tot_fin_in_qty+=$finish_qnty_in;
                        $tot_fin_out_qty+=$finish_qnty_out;
                        $tot_fin_transIn_qty+=$transfer_in_qnty_finish;
                        $tot_fin_transOut_qty+=$transfer_out_qnty_finish;
                        $tot_fin_qty+=$total_finishing;
                        $tot_fin_prLoss_qty+=$process_loss_finishing;
                        $tot_fin_undOver_qty+=$under_over_finish_prod;
                        $tot_fin_issCut_qty+=$issuedToCutQnty;
                        $tot_fin_lftOver_qty+=$finish_left_over;

                        $tot_gmt_qty+=$tot_po_qnty;
                        $tot_printIssIn_qty+=$print_issue_qnty_in;
                        $tot_printIssOut_qty+=$print_issue_qnty_out;
                        $tot_printIssue_qty+=$total_print_issued;
                        $tot_printRcvIn_qty+=$print_recv_qnty_in;
                        $tot_printRcvOut_qty+=$print_recv_qnty_out;
                        $tot_printRcv_qty+=$total_print_recv;
                        $tot_printRjt_qty+=$print_reject_qnty;

                        $tot_sewInInput_qty+=$sew_input_qnty_in;
                        $tot_sewInOutput_qty+=$sew_input_qnty_out;
                        $tot_sewIn_qty+=$total_sew_issued;
                        $tot_sewInBal_qty+=$sew_balance_qnty;

                        $tot_sewRcvIn_qty+=$sew_recv_qnty_in;
                        $tot_sewRcvOut_qty+=$sew_recv_qnty_out;
                        $tot_sewRcv_qty+=$total_sew_recv;
                        $tot_sewRcvBal_qty+=$sew_balance_recv_qnty;
                        $tot_sewRcvRjt_qty+=$sew_reject_qnty;

                        $tot_washRcvIn_qty+=$wash_recv_qnty_in;
                        $tot_washRcvOut_qty+=$wash_recv_qnty_out;
                        $tot_washRcv_qty+=$total_wash_recv;
                        $tot_washRcvBal_qty+=$wash_balance_qnty;

                        $tot_gmtFinIn_qty+=$gmt_finish_in;
                        $tot_gmtFinOut_qty+=$gmt_finish_out;
                        $tot_gmtFin_qty+=$total_gmts_finish_qnty;
                        $tot_gmtFinBal_qty+=$finish_balance_qnty;
                        $tot_gmtFinRjt_qty+=$gmt_finish_reject_qnty;
                        $tot_gmtEx_qty+=$tot_exfactory_qnty;
                        $tot_gmtFinLeftOver_qty+=$left_over_finish_gmts;

                        $tot_leftOver_qty+=$left_over;
                        $tot_leftOverFin_qty+=$finish_left_over;
                        $tot_leftOverGmtFin_qty+=$left_over_finish_gmts;
                        $tot_leftOverTrm_qty+=$tot_trims_left_over_qnty;

                        $tot_rjtPrint_qty+=$print_reject_qnty;
                        $tot_rjtEmb_qty+=$emb_reject_qnty;
                        $tot_rjtSew_qty+=$sew_reject_qnty;
                        $tot_rjtFin_qty+=$finish_reject_qnty;

                        $tot_prLoss_qty+=$process_loss;
                        $tot_prLossFin_qty+=$process_loss_finishing;
                        $tot_prLossCut_qty+=$cutting_process_loss;
                        $total_reject_qnty+=$tot_reject_qnty;

                        $tot_process_loss_yern_per += $process_loss_yern_per;
                        $tot_process_loss_dyeing_per += $process_loss_dyeing_per;
                        $tot_process_loss_cutting_per += $process_loss_cutting_per;
                        $i++;
                    }
                }
                ?>
            </table>
        </div>

            <table width="<? echo $tbl_width;?>" class="tbl_bottom" cellpadding="0" cellspacing="0" border="1" rules="all">
                <tr style="font-size:13px">
                    <td width="62">&nbsp;</td>
                    <td width="175">&nbsp;</td>
                    <td width="80">&nbsp;</td>
                    <td width="80">&nbsp;</td>
                    <td width="160">Total :</td>
                    <?
                    if($cbo_search_type==1)
                    {
                        ?>
                        <td width="158">&nbsp;</td>
                        <?
                    }
                    ?>
                    <td width="128" align="right" id="td_order_qty" bgcolor="#FFFFCC"><? echo number_format($tot_order_qty); ?></td>
                    <?
                    if($cbo_search_type==1)
                    {
                        ?>
                        <td width="112">&nbsp;</td>
                        <td width="112">&nbsp;</td>
                        <?
                    }
                    ?>

                    <td width="128" align="right" id="td_fin_req_qty"><? echo number_format($tot_fin_req_qty,2); ?></td>


                    <td width="128" align="right" id="td_wovenRecQty"><? echo number_format($tot_wovenRecQty,2); ?></td>
                    <td width="128" align="right" id="td_fin_transIn_qty"><? echo number_format($tot_fin_transIn_qty,2); ?></td>
                    <td width="128" align="right" id="td_wovenRecBalQty"><? echo number_format($tot_wovenRecBalQty,2); ?></td>

                    <td width="128" align="right" id="td_wovenIssueQty"><? echo number_format($tot_wovenIssueQty,2); ?></td>
                    <td width="128" align="right" id="td_fin_transOut_qty"><? echo number_format($tot_fin_transOut_qty,2); ?></td>
                    <td width="128" align="right" id="td_wovenIssueBalQty"><? echo number_format($tot_wovenIssueBalQty,2); ?></td>

                    <td width="128" align="right" id="td_fin_lftOver_qty"><? echo number_format($tot_fin_lftOver_qty,2); ?></td>
                    <td width="128" align="right" id="td_gmt_qty" bgcolor="#FFFFCC"><? echo number_format($tot_gmt_qty); ?></td>
                    <td width="128" align="right" id="td_cutting_qty"><? echo number_format($tot_cutting_qty); ?></td>

                    <td width="128" align="right" id="td_printIssIn_qty"><? echo number_format($tot_printIssIn_qty); ?></td>
                    <td width="128" align="right" id="td_printIssOut_qty"><? echo number_format($tot_printIssOut_qty); ?></td>
                    <td width="128" align="right" id="td_printIssue_qty"><? echo number_format($tot_printIssue_qty); ?></td>

                    <td width="128" align="right" id="td_printRcvIn_qty"><? echo number_format($tot_printRcvIn_qty); ?></td>
                    <td width="128" align="right" id="td_printRcvOut_qty"><? echo number_format($tot_printRcvOut_qty); ?></td>
                    <td width="128" align="right" id="td_printRcv_qty"><? echo number_format($tot_printRcv_qty); ?></td>

                    <td width="128" align="right" id="td_printRjt_qty"><? echo number_format($tot_printRjt_qty); ?></td>

                    <td width="128" align="right" id="td_sewInInput_qty"><? echo number_format($tot_sewInInput_qty); ?></td>
                    <td width="128" align="right" id="td_sewInOutput_qty"><? echo number_format($tot_sewInOutput_qty); ?></td>
                    <td width="128" align="right" id="td_sewIn_qty"><? echo number_format($tot_sewIn_qty); ?></td>
                    <td width="128" align="right" id="td_sewInBal_qty"><? echo number_format($tot_sewInBal_qty); ?></td>

                    <td width="161">&nbsp;</td>

                    <td width="128" align="right" id="td_sewRcvIn_qty"><? echo number_format($tot_sewRcvIn_qty); ?></td>
                    <td width="128" align="right" id="td_sewRcvOut_qty"><? echo number_format($tot_sewRcvOut_qty); ?></td>
                    <td width="128" align="right" id="td_sewRcv_qty"><? echo number_format($tot_sewRcv_qty); ?></td>
                    <td width="128" align="right" id="td_sewRcvBal_qty"><? echo number_format($tot_sewRcvBal_qty); ?></td>
                    <td width="128" align="right" id="td_sewRcvRjt_qty"><? echo number_format($tot_sewRcvRjt_qty); ?></td>

                    <td width="128" align="right" id="td_washRcvIn_qty"><? echo number_format($tot_washRcvIn_qty); ?></td>
                    <td width="128" align="right" id="td_washRcvOut_qty"><? echo number_format($tot_washRcvOut_qty); ?></td>
                    <td width="128" align="right" id="td_washRcv_qty"><? echo number_format($tot_washRcv_qty); ?></td>
                    <td width="128" align="right" id="td_washRcvBal_qty"><? echo number_format($tot_washRcvBal_qty); ?></td>

                    <td width="128" align="right" id="td_gmtFinIn_qty"><? echo number_format($tot_gmtFinIn_qty); ?></td>
                    <td width="128" align="right" id="td_gmtFinOut_qty"><? echo number_format($tot_gmtFinOut_qty); ?></td>
                    <td width="128" align="right" id="td_gmtFin_qty"><? echo number_format($tot_gmtFin_qty); ?></td>
                    <td width="128" align="right" id="td_gmtFinBal_qty"><? echo number_format($tot_gmtFinBal_qty); ?></td>
                    <td width="128" align="right" id="td_gmtFinRjt_qty"><? echo number_format($tot_gmtFinRjt_qty); ?></td>

                    <td width="128" align="right" id="td_gmtrej_qty"><? echo number_format($total_reject_qnty); ?></td>
                    <td width="128" align="right" id="td_gmtEx_qty"><? echo number_format($tot_gmtEx_qty); ?></td>
                    <td width="128" align="right" id="td_gmtFinLeftOver_qty"><? echo number_format($tot_gmtFinLeftOver_qty); ?></td>
                    <td width="128" align="right" id="td_shortExcess_exFactory_qty"><? echo number_format($tot_shortExcess_exFactory_qty); ?></td>
                    <td width="128" align="right" id="td_prLossCut_qty"><? echo number_format($tot_prLossCut_qty); ?></td>
                    <td width="128" align="right" id="td_prLoss_qty"><? echo number_format($tot_process_loss_cutting_per); ?></td>


                    <!-- old -->
                    <!-- <td width="128" align="right" id="td_fin_in_qty"><? //echo number_format($tot_fin_in_qty,2); ?></td>
                    <td width="128" align="right" id="td_fin_out_qty"><? //echo number_format($tot_fin_out_qty,2); ?></td>


                    <td width="128" align="right" id="td_fin_qty"><? //echo number_format($tot_fin_qty,2); ?></td>
                    <td width="128" align="right" id="td_fin_prLoss_qty"><? //echo number_format($tot_fin_prLoss_qty,2); ?></td>
                    <td width="128" align="right" id="td_fin_undOver_qty"><? //echo number_format($tot_fin_undOver_qty,2); ?></td>
                    <td width="128" align="right" id="td_fin_issCut_qty"><? //echo number_format($tot_fin_issCut_qty,2); ?></td>


                    <td width="128" align="right" id="td_wovenReqQty"><? //echo number_format($tot_wovenReqQty,2); ?></td>

                    <td width="128" align="right" id="td_prLoss_qty"><? //echo number_format($tot_prLoss_qty); ?></td>
                    <td width="128" align="right" id="td_prLoss_qty"><? //echo number_format($tot_process_loss_yern_per); ?></td>
                    <td width="128" align="right" id="td_prLossDye_qty"><? //echo number_format($tot_prLossDye_qty); ?></td>
                    <td width="128" align="right" id="td_prLoss_qty"><? //echo number_format($tot_process_loss_dyeing_per); ?></td> -->
                </tr>
            </table>

    </div>

    <?
    $html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
        //if( @filemtime($filename) < (time()-$seconds_old) )
        @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$cbo_search_type";
    exit();
}

if($action=='trims_popup')
{
    echo load_html_head_contents("Trims Details info", "../../../../", 1, 1,$unicode,'','');
    extract($_REQUEST);
    //echo $po_break_down_id."*".$tot_po_qnty;die;

    //echo $ratio;die;

    ?>
    <script>

        function window_close()
        {
            parent.emailwindow.hide();
        }

    </script>
    <fieldset style="width:650px;" >
        <legend>Accessories Status pop up</legend>
        <div style="100%" id="report_container">
            <table width="650" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                <tr>
                    <th colspan="7">Accessories Status</th>
                </tr>
                <tr>
                    <th width="110">Item</th>
                    <th width="70">UOM</th>
                    <th width="90">Req. Qty.</th>
                    <th width="90">Received</th>
                    <th width="90">Recv. Balance</th>
                    <th width="90">Issued</th>
                    <th>Left Over</th>
                </tr>
                </thead>
                <?
                $item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
                $trims_array=array();
                $trimsDataArr=sql_select("select b.item_group_id,
									sum(CASE WHEN a.entry_form=24 THEN a.quantity ELSE 0 END) AS recv_qnty,
									sum(CASE WHEN a.entry_form=25 THEN a.quantity ELSE 0 END) AS issue_qnty
									from order_wise_pro_details a, product_details_master b where a.prod_id=b.id and a.po_breakdown_id in($po_break_down_id) and b.item_category_id=4 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 group by b.item_group_id");
                foreach($trimsDataArr as $row)
                {
                    $trims_array[$row[csf('item_group_id')]]['recv']=$row[csf('recv_qnty')];
                    $trims_array[$row[csf('item_group_id')]]['iss']=$row[csf('issue_qnty')];
                }


                //$costing_per_arr=return_library_array( "select job_no, costing_per from wo_pre_cost_mst", "job_no", "costing_per"  );
                $trimsDataArr=sql_select("select c.po_break_down_id, max(a.costing_per) as costing_per, b.trim_group, max(b.cons_uom) as cons_uom, sum(b.cons_dzn_gmts) cons_dzn_gmts from wo_pre_cost_mst a, wo_pre_cost_trim_cost_dtls b , wo_pre_cost_trim_co_cons_dtls c where a.job_no=b.job_no and b.id=c.wo_pre_cost_trim_cost_dtls_id and b.status_active=1 and b.is_deleted=0 and c.po_break_down_id=$po_break_down_id group by b.trim_group, c.po_break_down_id");
                $i=1; $tot_accss_req_qnty=0; $tot_recv_qnty=0; $tot_iss_qnty=0; $tot_recv_bl_qnty=0; $tot_trims_left_over_qnty=0;
                foreach($trimsDataArr as $row)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    $dzn_qnty='';
                    if($row[csf('costing_per')]==1) $dzn_qnty=12;
                    else if($row[csf('costing_per')]==3) $dzn_qnty=12*2;
                    else if($row[csf('costing_per')]==4) $dzn_qnty=12*3;
                    else if($row[csf('costing_per')]==5) $dzn_qnty=12*4;
                    else $dzn_qnty=1;

                    $dzn_qnty=$dzn_qnty*$ratio;
                    $accss_req_qnty=($row[csf('cons_dzn_gmts')]/$dzn_qnty)*$tot_po_qnty;

                    $trims_recv=$trims_array[$row[csf('trim_group')]]['recv'];
                    $trims_issue=$trims_array[$row[csf('trim_group')]]['iss'];
                    $recv_bl=$accss_req_qnty-$trims_recv;
                    $trims_left_over=$trims_recv-$trims_issue;
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td><p><? echo $item_library[$row[csf('trim_group')]]; ?>&nbsp;</p></td>
                        <td align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?>&nbsp;</td>
                        <td align="right"><? echo number_format($accss_req_qnty,2,'.',''); ?>&nbsp;</td>
                        <td align="right"><? echo number_format($trims_recv,2,'.',''); ?>&nbsp;</td>
                        <td align="right"><? echo number_format($recv_bl,2,'.',''); ?>&nbsp;</td>
                        <td align="right"><? echo number_format($trims_issue,2,'.',''); ?>&nbsp;</td>
                        <td align="right"><? echo number_format($trims_left_over,2,'.',''); ?>&nbsp;</td>
                    </tr>
                    <?
                    $tot_accss_req_qnty+=$accss_req_qnty;
                    $tot_recv_qnty+=$trims_recv;
                    $tot_recv_bl_qnty+=$recv_bl;
                    $tot_iss_qnty+=$trims_issue;
                    $tot_trims_left_over_qnty+=$trims_left_over;
                    $i++;
                }
                $tot_trims_left_over_qnty_perc=($tot_trims_left_over_qnty/$tot_recv_qnty)*100;
                ?>
                <tfoot>
                <tr>
                    <th align="right">&nbsp;</th>
                    <th align="right">Total</th>
                    <th align="right"><? echo number_format($tot_accss_req_qnty,0,'.',''); ?>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_recv_qnty,0,'.',''); ?>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_recv_bl_qnty,0,'.',''); ?>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_iss_qnty,0,'.',''); ?>&nbsp;</th>
                    <th align="right"><? echo number_format($tot_trims_left_over_qnty,0,'.',''); ?>&nbsp;</th>
                </tr>
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
    ?>
    <div style="width:100%" align="center">
        <fieldset style="width:500px">
            <div class="form_caption" align="center"><strong>Ex-Factory Details</strong></div><br />
            <div style="width:100%">
                <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                    <thead>
                    <tr>
                        <th width="35">SL</th>
                        <th width="90">Ex-fac. Date</th>
                        <th width="120">System /Challan no</th>
                        <th width="100">Ex-Fact. Del.Qty.</th>
                        <th width="">Ex-Fact.Return Qty.</th>

                    </tr>
                    </thead>
                </table>
            </div>
            <div style="width:100%; max-height:400px;">
                <table cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
                    <?
                    $i=1;

                    $exfac_sql=("select b.challan_no,a.sys_number,b.ex_factory_date,
				CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_qnty,
				CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_return_qnty
				from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b  where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.po_break_down_id in($id) ");
                    $sql_dtls=sql_select($exfac_sql);

                    foreach($sql_dtls as $row_real)
                    {
                        if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="35"><? echo $i; ?></td>
                            <td width="90"><? echo change_date_format($row_real[csf("ex_factory_date")]); ?></td>
                            <td width="120"><? echo $row_real[csf("sys_number")]; ?></td>
                            <td width="100" align="right"><? echo $row_real[csf("ex_factory_qnty")]; ?></td>
                            <td width="" align="right"><? echo $row_real[csf("ex_factory_return_qnty")]; ?></td>
                        </tr>
                        <?
                        $rec_qnty+=$row_real[csf("ex_factory_qnty")];
                        $rec_return_qnty+=$row_real[csf("ex_factory_return_qnty")];
                        $i++;
                    }
                    ?>
                    <tfoot>
                    <tr>
                        <th colspan="3">Total</th>
                        <th><? echo number_format($rec_qnty,2); ?></th>
                        <th><? echo number_format($rec_return_qnty,2); ?></th>
                    </tr>
                    <tr>
                        <th colspan="3">Total Balance</th>
                        <th colspan="2" align="right"><? echo number_format($rec_qnty-$rec_return_qnty,2); ?></th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </fieldset>
    </div>
    <?
    exit();
}

if ($action=="reject_qty")
{
    extract($_REQUEST);
    echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode,'','');

    //echo $po_id;
    ?>
    <div style="width:500px;" align="center">
        <table width="490" cellspacing="0" border="1" class="rpt_table" rules="all" >
            <thead>
            <tr>
                <th colspan="7">Reject Qty Details</th>
            </tr>
            <tr>
                <th width="30">SL</th>
                <th width="75">Cutting Reject Qty</th>
                <th width="75">Embellishment Reject Qty</th>
                <th width="75">Sewing Out Reject Qty</th>
                <th width="75">Iron Reject Qty</th>
                <th width="75">Finish Reject Qty.</th>
                <th >Total Reject Qty.</th>
            </tr>
            </thead>
            <tbody>
            <?
            $po_id=str_replace("'","",$po_id);
            $company=str_replace("'","",$company);
            $sql_qry="Select po_break_down_id, sum(CASE WHEN production_type ='1' THEN reject_qnty ELSE 0 END) AS cutting_rej_qnty, sum(CASE WHEN production_type ='3' THEN reject_qnty ELSE 0 END) AS emb_rej_qnty, sum(CASE WHEN production_type ='7' THEN reject_qnty ELSE 0 END) AS iron_rej_qnty, sum(CASE WHEN production_type ='8' THEN reject_qnty ELSE 0 END) AS finish_rej_qnty, sum(CASE WHEN production_type ='5' THEN reject_qnty ELSE 0 END) AS sewingout_rej_qnty from pro_garments_production_mst  where po_break_down_id in ($po_id)  and status_active=1 and is_deleted=0  group by po_break_down_id";
            $sql_result=sql_select($sql_qry);

            $i=1;
            foreach($sql_result as $row)
            {
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>
                    <td align="right"><? echo number_format($row[csf('cutting_rej_qnty')],0); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf('emb_rej_qnty')],0); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf('sewingout_rej_qnty')],0); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf('iron_rej_qnty')],0); ?>&nbsp;</td>
                    <td align="right"><? echo number_format($row[csf('finish_rej_qnty')],0); ?>&nbsp;</td>
                    <th align="right"><? $total_reject=$row[csf('cutting_rej_qnty')]+$row[csf('emb_rej_qnty')]+$row[csf('iron_rej_qnty')]+$row[csf('sewingout_rej_qnty')]+$row[csf('finish_rej_qnty')]; echo $total_reject; ?>&nbsp;</th>
                </tr>
                <?
                $i++;
            }
            ?>
            </tbody>

        </table>
    </div>
    <?
    exit();
}
?>

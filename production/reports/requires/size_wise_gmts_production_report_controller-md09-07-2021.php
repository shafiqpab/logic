<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data']; 
$action=$_REQUEST['action'];
$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_short_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );$color_Arr_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );	
$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  ); 
$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  );
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0 and a.party_type not in('2') order by a.buyer_name","id,buyer_name", 1, "-- Select buyer --", 0, "","" );//load_drop_down( 'requires/daily_knitting_production_report_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_machine', 'machine_td' );$location_cond
  exit();	 
}
if($db_type==0) $insert_year="SUBSTRING_INDEX(a.insert_date, '-', 1)";
if($db_type==2) $insert_year="extract( year from b.insert_date)";
//item style------------------------------//

if ($action=="load_drop_down_location")
{
    extract($_REQUEST);
    $choosenCompany = $choosenCompany;  
	echo create_drop_down( "cbo_location_name", 200, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id in( $choosenCompany) group by id,location_name  order by location_name","id,location_name", 0, "-- Select location --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_floor")
{
    extract($_REQUEST);
    $choosenLocation = $choosenLocation;  
	echo create_drop_down( "cbo_floor_name", 200, "SELECT id,floor_name from lib_prod_floor where location_id in( $choosenLocation ) and status_active =1 and is_deleted=0 group by id,floor_name order by floor_name","id,floor_name", 0, "-- Select Floor --", $selected, "" );
	exit();
}

if($action=="job_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $buyer;die;
	?>

<script>
function js_set_value(id) {
    //alert(id);
    document.getElementById('selected_id').value = id;
    parent.emailwindow.hide();
}
</script>
</head>

<body>
    <div align="center" style="width:820px;">
        <form name="styleRef_form" id="styleRef_form">
            <fieldset style="width:800px;">
                <table width="800" cellspacing="0" cellpadding="0" border="1" rules="all" align="center"
                    class="rpt_table" id="tbl_list">
                    <thead>
                        <th>Company</th>
                        <th>Buyer</th>
                        <th>Search By</th>
                        <th id="search_by_td_up" width="170">Please Enter Job No</th>
                        <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;"
                                onClick="reset_form('styleRef_form','search_div','','','','');"></th> <input
                            type="hidden" id="selected_id" name="selected_id" />
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
                                <input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
                                    id="txt_search_common" />
                            </td>
                            <td align="center">
                                <input type="button" name="button" class="formbutton" value="Show"
                                    onClick="show_list_view (document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>', 'job_popup_search_list_view', 'search_div', 'size_wise_gmts_production_report_controller', 'setFilterGrid(\'table_body2\',-1)');"
                                    style="width:100px;" />
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
	$sql= "SELECT a.id, a.job_no_prefix_num, a.job_no, a.company_name,a.buyer_name,a.style_ref_no,$year_field as year , $group_field
	from wo_po_details_master a,  wo_po_break_down b 
	where a.job_no=b.job_no_mst and b.status_active in(1,2,3) and a.company_name=$company_id $buyer_cond $year_cond $search_con 
	group by a.id, a.job_no_prefix_num, a.job_no, a.company_name,a.buyer_name,a.style_ref_no,a.insert_date
	order by a.id desc";
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
        <tr bgcolor="<? echo  $bgcolor;?>"
            onClick="js_set_value('<? echo $data[csf('id')]; ?>'+'_'+'<? echo $data[csf('job_no')]; ?>')"
            style="cursor:pointer;">
            <td width="30" align="center">
                <? echo $i; ?>
            </td>
            <td width="120">
                <p>
                    <? echo $company_arr[$data[csf('company_name')]]; ?>
                </p>
            </td>
            <td width="120">
                <p>
                    <? echo $buyer_short_library[$data[csf('buyer_name')]]; ?>
                </p>
            </td>
            <td align="center" width="50">
                <p>
                    <? echo $data[csf('year')]; ?>
                </p>
            </td>
            <td width="120">
                <p>
                    <? echo $data[csf('job_no')]; ?>
                </p>
            </td>
            <td width="120">
                <p>
                    <? echo $data[csf('style_ref_no')]; ?>
                </p>
            </td>
            <td>
                <p>
                    <? echo $po_num; ?>
                </p>
            </td>
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
{
	/*
		echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
		extract($_REQUEST);
		$data=explode('_',$data);
		// $report_type=$data[3];
		// print_r($data);
		//echo $batch_type."AAZZZ";
	?>
<script type="text/javascript">
function js_set_value(id) {
    //alert(id);
    document.getElementById('selected_id').value = id;
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
        <tr bgcolor="<? echo  $bgcolor;?>"
            onclick="js_set_value('<? echo $data[csf('id')]; ?>'+'_'+'<? echo $data[csf('job_no')]; ?>')"
            style="cursor:pointer;">
            <td width="30">
                <? echo $i; ?>
            </td>
            <td align="center" width="40">
                <p>
                    <? echo $data[csf('year')]; ?>
                </p>
            </td>
            <td align="center" width="50">
                <p>
                    <? echo $data[csf('job_prefix')]; ?>
                </p>
            </td>
            <td width="100">
                <p>
                    <? echo $data[csf('style_ref_no')]; ?>
                </p>
            </td>
            <td width="">
                <p>
                    <? echo $po_num; ?>
                </p>
            </td>

        </tr>
        <? $i++; } ?>
    </table>
</div>
<script>
setFilterGrid("table_body2", -1);
</script>
<?
		disconnect($con);
		exit();
	*/
}//JobNumberShow


//order wise browse------------------------------//
if($action=="order_wise_search")
{		  
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
<script>
var selected_id = new Array;
var selected_name = new Array;

function check_all_data() {
    var tbl_row_count = document.getElementById('list_view_po').rows.length;
    // var tbl_row_count =  $('#list_view_po tr:visible').length;
    // alert(tbl_row_count);return;
    tbl_row_count = tbl_row_count - 0;
    for (var i = 1; i <= tbl_row_count; i++) {
        if ($('#tr_' + i).is(":visible")) {
            var onclickString = $('#tr_' + i).attr('onclick');
            var paramArr = onclickString.split("'");
            var functionParam = paramArr[1];
            js_set_value(functionParam);
        }

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
    //$('#txt_individual_id' + str).val(splitSTR[1]);
    //$('#txt_individual' + str).val('"'+splitSTR[2]+'"');

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
	echo create_list_view("list_view_po", "Year,Job No,Style Ref,Order Number","50,100,120,150,","550","310",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "year,job_no_prefix_num,style_ref_no,po_number", "","setFilterGrid('list_view_po',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}

$colorname_arr=return_library_array( "select id, color_name from  lib_color", "id", "color_name"  );
$country_arr=return_library_array( "select id, country_name from   lib_country", "id", "country_name");
$floor_arr=return_library_array( "select id, floor_name from   lib_prod_floor", "id", "floor_name");


if($action=="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$hidden_order_id=str_replace("'","",$hidden_order_id);
	$date_to=str_replace("'","",$txt_date_to);
	$date_from=str_replace("'","",$txt_date_from);
	//$txt_production_date=str_replace("'","",$txt_production_date);
	$job_no=str_replace("'","",$txt_job_no);
	$hiden_order_id=str_replace("'","",$hiden_order_id);
	$cbo_work_company_name=str_replace("'","",$cbo_work_company_name);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_year=str_replace("'","",$cbo_year);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);

	
	$job_po_id="";
	
	
	if(str_replace("'","",$txt_job_no)!="")
	{
		if($db_type==0)
		{
			$job_po_id=return_field_value("group_concat(b.id) as po_id","wo_po_break_down b","b.job_no_mst in ('UHM-21-00192')","po_id");
			
		}
		else
		{
			$job_id=return_field_value("listagg(cast(job_no as varchar(4000)),',') within group(order by id) as job_no","wo_po_details_master","JOB_NO_PREFIX_NUM in ($txt_job_no) and to_char(insert_date,'YYYY')=$cbo_year and company_name=$cbo_work_company_name","job_no");
			$job_arr=explode(",",$job_id);
			foreach($job_arr as $val){
				$job_poid=return_field_value("listagg(cast(b.id as varchar(4000)),',') within group(order by id) as po_id","wo_po_break_down b","b.job_no_mst in ('$val')","po_id");
			}
			
		}
		
	}



//  echo $job_poid;die;

	if($cbo_buyer_name >0){
		$buyer_cond="and e.buyer_name='$cbo_buyer_name'";
		$buyer_cond_2="and d.buyer_name='$cbo_buyer_name'";
		$buyer_cond_3="and a.buyer_name='$cbo_buyer_name'";
		
	}else{
		$buyer_cond="";
		$buyer_cond_2="";
		$buyer_cond_3="";
	}



	if($job_no!=""){
		$job_cond="and d.job_no_prefix_num in ($job_no)";
		$job_cond_2="and e.job_no_prefix_num in ($job_no)";
		$job_cond_3="and a.job_no_prefix_num in ($job_no)";
		
	}else{
		$job_cond="";
		$job_cond_2="";
	}
	if($job_no!=""){
			if($cbo_year!=""){		
				$job_year_cond="and to_char(e.insert_date,'YYYY')=$cbo_year";	
				$job_year_cond_2="and to_char(d.insert_date,'YYYY')=$cbo_year";
				$job_year_cond_3="and to_char(a.insert_date,'YYYY')=$cbo_year";	
					
			}else{
				$job_year_cond="";
				$job_year_cond_2="";
				$job_year_cond_3="";			
			
			}
		}
	
	
	$order_cond_lay="";
	$order_cond_prod="";
	$order_d_cond="";

	if($order_cond_lay!=""){$order_cond_lay.=" and c.order_id in($job_poid)";}else{$order_cond_lay="";}
	if($order_cond!=""){$order_cond.=" and a.po_break_down_id in($job_poid)";}else{$order_cond="";}
	if($order_d_cond!=""){$order_d_cond .=" and g.po_break_down_id in($job_poid)";}else{$order_d_cond="";}



	//  echo $order_cond."<br>";
	//  echo $order_cond_lay."<br>";
	// die();
	
	

	if($cbo_company_name>0){ $company_cond=" and a.company_name=$cbo_company_name";$company_cond_2=" and a.company_id=$cbo_company_name";}

		
	 $current_date=change_date_format(date( 'd-m-Y' ),'dd-mm-yyyy','-',1);
	///$current_date="07-Jun-2021";

	if($date_from=="" && $date_to=="" ){ $date_cond="";}else{
		
		 $date_cond=" and a.production_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
		 $date_cond_2=" and m.delivery_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
		 $date_cond_3=" and a.entry_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
		 $date_cond_4=" and entry_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";

		 $date_cond_5=" and d.production_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
		 $date_cond_6=" and c.country_ship_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
		
	}
	$clientArr 	= return_library_array( "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id  and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (7))  order by buyer_name","id","buyer_name"); 
	$sizearr=return_library_array("SELECT id,size_name from lib_size ","id","size_name");  
	 if($type==1)
	 {
			
			// ============================== For Cut and Lay Entry Roll Wise entry form =============================================
			
		
			 //echo $production_sql;// die;			
		
		
			$sql_lay="SELECT d.size_id, d.size_qty,c.gmt_item_id,c.color_id,d.order_id,e.buyer_name,a.job_no,e.style_ref_no,e.client_id from ppl_cut_lay_mst a,ppl_cut_lay_dtls c,ppl_cut_lay_bundle d ,wo_po_details_master e, wo_po_break_down f where e.job_no=f.job_no_mst and e.job_no=a.job_no and d.order_id=f.id and  a.id=c.mst_id  and a.id=d.mst_id and a.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 and f.status_active=1  $date_cond_3 and a.WORKING_COMPANY_ID in ($cbo_work_company_name) $job_cond_2 $buyer_cond $job_year_cond order by d.size_id asc";

		//	echo $sql_lay;
			 
			$sql_lay_result=sql_select($sql_lay);
			$production_data=$porduction_ord_id=$lay_order_id=array();
			$garments_order_id_arr=array();
			$l=0;
			foreach($sql_lay_result as $row)
			{
				
				$order_color_size_data_2[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("order_id")]][$row[csf("gmt_item_id")]][$row[csf("color_id")]][$row[csf("size_id")]]["cut_lay"]+=$row[csf("size_qty")];
				if($l==0){
					$l_order_id .=$row[csf("order_id")];
					$l++;
				}else{
					$l_order_id .=",".$row[csf("order_id")];
				}
                $job_number_array_check[$row[csf("order_id")]]=$row[csf("order_id")];
                $po_number_array_check[$row[csf("job_no")]]=$row[csf("order_id")];
				
			}
			// print_r($order_color_size_data);

			$l_order=array_unique(explode(",",$l_order_id));
			$l_po_arr=implode(",",$l_order);		
			if($date_from !=="" && $date_to !=="" && $job_cond_2 ==""){
				$d_l_order_cond="and d.order_id in ($l_po_arr)";
			}else{
				$d_l_order_cond="";
			}
		
			$sql_lay_prev="SELECT d.size_id, d.size_qty,c.gmt_item_id,c.color_id,d.order_id,e.buyer_name,a.job_no,e.client_id as buyer_client from ppl_cut_lay_mst a,ppl_cut_lay_dtls c,ppl_cut_lay_bundle d ,wo_po_details_master e, wo_po_break_down f where e.job_no=f.job_no_mst and e.job_no=a.job_no and d.order_id=f.id and  a.id=c.mst_id  and a.id=d.mst_id and a.status_active=1  and c.status_active=1 and d.status_active=1 and a.WORKING_COMPANY_ID in ($cbo_work_company_name) $job_year_cond $d_l_order_cond $job_cond_2 $buyer_cond order by d.size_id  asc";
					// echo $sql_lay_prev;
				$sql_lay_prev_result=sql_select($sql_lay_prev);
				foreach($sql_lay_prev_result as $row)
				{
					
					$order_color_size_data_2[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("order_id")]][$row[csf("gmt_item_id")]][$row[csf("color_id")]][$row[csf("size_id")]]["lay_prev_qnty"]+=$row[csf("size_qty")];

                    $buyer_wise_data[$row[csf("job_no")]]['client_id']=$row[csf("buyer_client")];
                    $po_number_array_check[$row[csf("order_id")]]=$row[csf("order_id")];
				}
			

           
			$sewing_in_sql="SELECT a.production_date, c.po_break_down_id as order_id, c.item_number_id, d.buyer_name, c.color_number_id as color_id,c.size_number_id as size_id,c.color_order,c.size_order,
				sum(CASE WHEN b.production_type =4 and a.production_type =4 THEN b.production_qnty ELSE 0 END) AS sewing_in_qnty,d.job_no,d.style_ref_no,d.client_id,c.order_quantity
				from  pro_garments_production_dtls b,pro_garments_production_mst a,wo_po_color_size_breakdown c ,wo_po_details_master d, wo_po_break_down e
				where  d.job_no=e.job_no_mst and d.job_no=c.job_no_mst and c.po_break_down_id=e.id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active in(1,2,3) and c.is_deleted=0 and a.production_source=1 and b.production_type=4 and a.production_type=4  and a.serving_company in($cbo_work_company_name) $date_cond $job_cond $buyer_cond_2 $job_year_cond_2 group by a.production_date, c.po_break_down_id , c.item_number_id, d.buyer_name, c.color_number_id ,c.size_number_id,d.job_no,d.style_ref_no,d.client_id,c.order_quantity,c.color_order,c.size_order  order by c.color_order,c.size_order asc";

				// echo $sewing_in_sql;
	

			$sewing_in_sql_result=sql_select($sewing_in_sql);
			$si=0;
			foreach($sewing_in_sql_result as $row)
			{
				if($si==0){
					$si_order_id .=$row[csf("order_id")];
					$si++;
				}else{
					$si_order_id .=",".$row[csf("order_id")];
				}
	

				 $order_color_size_data_2[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]][$row[csf("size_id")]]["sewing_in"]+=$row[csf("sewing_in_qnty")];
			
			}
			$si_order=array_unique(explode(",",$si_order_id));
			$si_po_arr=implode(",",$si_order);			
			if($date_from !=="" && $date_to !=="" && $job_cond_2 ==""){
				$d_si_order_cond="and  c.po_break_down_id in ($si_po_arr)";
			}else{
				$d_si_order_cond="";
			}

			
			$sewing_in_tot_sql="SELECT a.production_date, c.po_break_down_id as order_id, c.item_number_id, d.buyer_name, c.color_number_id as color_id,c.size_number_id as size_id,c.color_order,c.size_order,
			sum(CASE WHEN b.production_type =4 and a.production_type =4 THEN b.production_qnty ELSE 0 END) AS sewing_in_prev_qnty,d.job_no,d.client_id as buyer_client
			from  pro_garments_production_dtls b,pro_garments_production_mst a,wo_po_color_size_breakdown c ,wo_po_details_master d, wo_po_break_down e
			where  d.job_no=e.job_no_mst and d.job_no=c.job_no_mst and c.po_break_down_id=e.id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active in(1,2,3) and c.is_deleted=0 and a.production_source=1 and b.production_type=4 and a.production_type=4  and a.serving_company in($cbo_work_company_name)  $d_si_order_cond 
			$job_cond $buyer_cond_2 $job_year_cond_2 group by a.production_date, c.po_break_down_id , c.item_number_id, d.buyer_name, c.color_number_id ,c.size_number_id,
			d.job_no,c.color_order,c.size_order,d.client_id order by c.color_order,c.size_order asc";
			$sewing_in_tot_sql_data=sql_select($sewing_in_tot_sql);
			foreach($sewing_in_tot_sql_data as $row)
			{
				
				
				 $order_color_size_data_2[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]][$row[csf("size_id")]]["sewing_in_prev_qnty"]+=$row[csf("sewing_in_prev_qnty")];
                 $po_number_array_check[$row[csf("order_id")]]=$row[csf("order_id")];
                 $buyer_wise_data[$row[csf("job_no")]]['client_id']=$row[csf("buyer_client")];
			}



			$sewing_out_sql="SELECT a.production_date, c.po_break_down_id as order_id, c.item_number_id, d.buyer_name, c.color_number_id as color_id,c.size_number_id as size_id,c.color_order,c.size_order,			
			sum(CASE WHEN b.production_type =5 and a.production_type =5 THEN b.production_qnty ELSE 0 END) AS sewing_out_qnty,d.job_no,d.style_ref_no,d.client_id,c.order_quantity
			from  pro_garments_production_dtls b,pro_garments_production_mst a,wo_po_color_size_breakdown c ,wo_po_details_master d, wo_po_break_down e  where  d.job_no=e.job_no_mst and d.job_no=c.job_no_mst and c.po_break_down_id=e.id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active in(1,2,3) and c.is_deleted=0 and a.production_source=1 and b.production_type=5 and a.production_type=5  and a.serving_company in($cbo_work_company_name) $date_cond  $job_cond $buyer_cond_2 $job_year_cond_2 group by a.production_date, c.po_break_down_id, c.item_number_id, c.color_number_id,c.size_number_id,d.job_no, d.buyer_name,d.style_ref_no,d.client_id,c.order_quantity,c.color_order,c.size_order order by c.color_order,c.size_order asc";

			$sewing_out_sql_result=sql_select($sewing_out_sql);
			$so=0;
			foreach($sewing_out_sql_result as $row)
			{
				if($so==0){
					$so_order_id .=$row[csf("order_id")];
					$so++;
				}else{
					$so_order_id .=",".$row[csf("order_id")];
				}

		
				 $order_color_size_data_2[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]][$row[csf("size_id")]]["sewing_out"]+=$row[csf("sewing_out_qnty")];
				
			}
			$so_order=array_unique(explode(",",$so_order_id));
			$so_po_arr=implode(",",$so_order);			
		
			if($date_from !=="" && $date_to !=="" && $job_cond_2 ==""){
				$d_so_order_cond="and  c.po_break_down_id in ($so_po_arr)";
			}else{
				$d_so_order_cond="";
			}


			$sewing_out_tot_sql="SELECT a.production_date, c.po_break_down_id as order_id, c.item_number_id, d.buyer_name, c.color_number_id as color_id,c.size_number_id as size_id,c.color_order,c.size_order,			
			sum(CASE WHEN b.production_type =5 and a.production_type =5 THEN b.production_qnty ELSE 0 END) AS sewing_out_prev_qnty,d.job_no,d.client_id as buyer_client from  pro_garments_production_dtls b,pro_garments_production_mst a,wo_po_color_size_breakdown c ,wo_po_details_master d, wo_po_break_down e  where  d.job_no=e.job_no_mst and d.job_no=c.job_no_mst and c.po_break_down_id=e.id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active in(1,2,3) and c.is_deleted=0 and a.production_source=1 and b.production_type=5 and a.production_type=5  and a.serving_company in($cbo_work_company_name)   $d_so_order_cond $job_cond $buyer_cond_2 $job_year_cond_2	group by a.production_date, c.po_break_down_id, c.item_number_id, c.color_number_id,c.size_number_id,d.job_no, d.buyer_name,c.color_order,c.size_order,d.client_id order by c.color_order,c.size_order asc";

			$sewing_out_tot_sql_data=sql_select($sewing_out_tot_sql);
			foreach($sewing_out_tot_sql_data as $row)
			{
				
				
			 $order_color_size_data_2[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]][$row[csf("size_id")]]["sewing_out_prev_qnty"]+=$row[csf("sewing_out_prev_qnty")];
             $buyer_wise_data[$row[csf("job_no")]]['client_id']=$row[csf("buyer_client")];
             $po_number_array_check[$row[csf("order_id")]]=$row[csf("order_id")];

			}

		
			$poly_sql="SELECT  c.po_break_down_id as order_id, c.item_number_id, c.color_number_id as color_id,c.size_number_id as size_id,c.color_order,c.size_order,	sum(CASE WHEN b.production_type =11 and a.production_type=11 THEN b.production_qnty ELSE 0 END) AS poly_qnty,d.job_no, d.buyer_name,d.style_ref_no,d.client_id,c.order_quantity from  pro_garments_production_dtls b,pro_garments_production_mst a,wo_po_color_size_breakdown c ,wo_po_details_master d, wo_po_break_down e  where  d.job_no=e.job_no_mst and d.job_no=c.job_no_mst and c.po_break_down_id=e.id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active in(1,2,3) and c.is_deleted=0 and a.production_source=1 and b.production_type=11 and a.production_type=11   and a.serving_company in($cbo_work_company_name) $date_cond $job_cond  $buyer_cond_2 $job_year_cond_2 group by  c.po_break_down_id, c.item_number_id, c.color_number_id,c.size_number_id,d.job_no, d.buyer_name,d.style_ref_no,d.client_id,c.order_quantity,c.color_order,c.size_order order by c.color_order,c.size_order asc";


			$poly_sql_result=sql_select($poly_sql);
			$p=0;
			foreach($poly_sql_result as $row)
			{
				if($p==0){
					$p_order_id .=$row[csf("order_id")];
					$p++;
				}else{
					$p_order_id .=",".$row[csf("order_id")];
				}
			
				 $order_color_size_data_2[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]][$row[csf("size_id")]]["poly_qnty"]+=$row[csf("poly_qnty")];

				
			}
			$p_order=array_unique(explode(",",$p_order_id));
			$p_po_arr=implode(",",$p_order);		
	
			if($date_from !=="" && $date_to !=="" && $job_cond_2 ==""){
				$d_p_order_cond="and  c.po_break_down_id in ($p_po_arr)";
			}else{
				$d_p_order_cond="";
			}

		
			$poly_tot_sql="SELECT  c.po_break_down_id as order_id, c.item_number_id, c.color_number_id as color_id,c.size_number_id as size_id,c.color_order,c.size_order,sum(CASE WHEN b.production_type =11 and a.production_type=11 THEN b.production_qnty ELSE 0 END) AS poly_prev_qnty,d.job_no, d.buyer_name,d.client_id as buyer_client	from  pro_garments_production_dtls b,pro_garments_production_mst a,wo_po_color_size_breakdown c ,wo_po_details_master d, wo_po_break_down e where  d.job_no=e.job_no_mst and d.job_no=c.job_no_mst and c.po_break_down_id=e.id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active in(1,2,3) and c.is_deleted=0 and a.production_source=1 and b.production_type=11 and a.production_type=11  and a.serving_company in($cbo_work_company_name)  $d_p_order_cond $job_cond $buyer_cond_2 $job_year_cond_2 group by  c.po_break_down_id, c.item_number_id, c.color_number_id,c.size_number_id,d.job_no, d.buyer_name,c.color_order,c.size_order,d.client_id order by  c.color_order,c.size_order asc";

			$poly_sql_data=sql_select($poly_tot_sql);
			foreach($poly_sql_data as $row)
			{
				
				
				 $order_color_size_data_2[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]][$row[csf("size_id")]]["poly_prev_qnty"]+=$row[csf("poly_prev_qnty")];
                 $buyer_wise_data[$row[csf("job_no")]]['client_id']=$row[csf("buyer_client")];
                 $po_number_array_check[$row[csf("order_id")]]=$row[csf("order_id")];
			}

			


			$paking_finish_sql="SELECT c.po_break_down_id as order_id, c.item_number_id, c.color_number_id as color_id,c.size_number_id as size_id,c.color_order,c.size_order,	sum(CASE WHEN b.production_type =8 and a.production_type =8 THEN b.production_qnty ELSE 0 END) AS paking_finish_qnty,d.job_no, d.buyer_name,d.style_ref_no,d.client_id,c.order_quantity
			from  pro_garments_production_dtls b,pro_garments_production_mst a,wo_po_color_size_breakdown c ,wo_po_details_master d, wo_po_break_down e where  d.job_no=e.job_no_mst and d.job_no=c.job_no_mst and c.po_break_down_id=e.id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active in(1,2,3) and c.is_deleted=0 and a.production_source=1 and b.production_type=8 and a.production_type=8   and a.serving_company in($cbo_work_company_name) $date_cond $job_cond $buyer_cond_2 $job_year_cond_2 group by  c.po_break_down_id, c.item_number_id, c.color_number_id,c.size_number_id,d.job_no, d.buyer_name,d.style_ref_no,d.client_id,c.order_quantity,c.color_order,c.size_order order by c.color_order,c.size_order asc";
	//	echo 	$paking_finish_sql;
            $paking_finish_sql_result=sql_select($paking_finish_sql);
            $pf=0;
            foreach($paking_finish_sql_result as $row)
            {
                if($pf==0){
                    $pf_order_id .=$row[csf("order_id")];
                    $pf++;
                }else{
                    $pf_order_id .=",".$row[csf("order_id")];
                }

            
                    $order_color_size_data_2[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]][$row[csf("size_id")]]["paking_finish_qnty"]+=$row[csf("paking_finish_qnty")];
                
            }

		$pf_order=array_unique(explode(",",$pf_order_id));
		$p_f_po_arr=implode(",",$pf_order);	

		if($date_from !=="" && $date_to !=="" && $job_cond_2 ==""){
			$d_pf_order_cond="and  c.po_break_down_id in ($p_f_po_arr)";
		}else{
			$d_pf_order_cond="";
		}

	
		$paking_finish_tot_sql="SELECT c.po_break_down_id as order_id, c.item_number_id, c.color_number_id as color_id,c.size_number_id as size_id,c.color_order,c.size_order,	sum(CASE WHEN b.production_type =8 and a.production_type =8 THEN b.production_qnty ELSE 0 END) AS paking_finish_prev_qnty,d.job_no, d.buyer_name,d.client_id as buyer_client from  pro_garments_production_dtls b,pro_garments_production_mst a,wo_po_color_size_breakdown c ,wo_po_details_master d, wo_po_break_down e where  d.job_no=e.job_no_mst and d.job_no=c.job_no_mst and c.po_break_down_id=e.id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active in(1,2,3) and c.is_deleted=0 and a.production_source=1 and b.production_type=8 and a.production_type=8   and a.serving_company in($cbo_work_company_name)   $d_pf_order_cond $job_cond $buyer_cond_2 $job_year_cond_2 group by  c.po_break_down_id, c.item_number_id, c.color_number_id,c.size_number_id,d.job_no, d.buyer_name,c.color_order,c.size_order,d.client_id order by c.color_order,c.size_order asc";
			// echo $paking_finish_tot_sql;
				
			$paking_finish_tot_sql_result=sql_select($paking_finish_tot_sql);
			foreach($paking_finish_tot_sql_result as $row)
			{
					 $order_color_size_data_2[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]][$row[csf("size_id")]]["paking_finish_prev_qnty"]+=$row[csf("paking_finish_prev_qnty")];
                    $buyer_wise_data[$row[csf("job_no")]]['client_id']=$row[csf("buyer_client")];
                    $po_number_array_check[$row[csf("order_id")]]=$row[csf("order_id")];
			}
			//	echo $p_f_po_arr;die; 

				$ex_factory_sql="SELECT  a.po_break_down_id as order_id, a.item_number_id, c.color_number_id as color_id,c.color_order,c.size_order, sum(b.production_qnty) as ex_fact_qnty ,c.size_number_id as size_id,d.job_no, d.buyer_name,d.style_ref_no,d.client_id, c.order_quantity from pro_ex_factory_delivery_mst m, pro_ex_factory_mst a, pro_ex_factory_dtls b, wo_po_color_size_breakdown c ,wo_po_details_master d, wo_po_break_down e  where  d.job_no=e.job_no_mst and a.po_break_down_id=e.id and m.id=a.delivery_mst_id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and m.status_active=1 and m.is_deleted=0 and m.entry_form!=85  and m.delivery_company_id in ($cbo_work_company_name) $date_cond_2 $buyer_cond_2  $job_cond $job_year_cond_2 
				group by  a.po_break_down_id, a.item_number_id, c.color_number_id,c.size_number_id ,d.job_no, d.buyer_name,d.style_ref_no,d.client_id, c.order_quantity,c.color_order,c.size_order order by c.color_order,c.size_order asc";
			//echo $ex_factory_sql;
		
			$ex_factory_sql_result=sql_select($ex_factory_sql);
			$ex=0;
			foreach($ex_factory_sql_result as $row)
			{
				if($ex==0){
					$ex_order_id .=$row[csf("order_id")];
					$ex++;
				}else{
					$ex_order_id .=",".$row[csf("order_id")];
				}
			
				$order_color_size_data_2[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]][$row[csf("size_id")]]["ex_fact_qnty"]+=$row[csf("ex_fact_qnty")];
				
			}

			$ex_order=array_unique(explode(",",$ex_order_id));
			$ex_po_arr=implode(",",$ex_order);

			if($date_from !=="" && $date_to !=="" && $job_cond_2 ==""){
				$d_ex_order_cond="and  c.po_break_down_id in ($ex_po_arr)";
			}else{
				$d_ex_order_cond="";
			}
			$all_po_list=implode(",",array_unique(array_merge($l_order,$si_order,$so_order,$p_order,$pf_order,$ex_order)));;
			
			$ex_factory_tot_sql="SELECT  c.po_break_down_id as order_id, c.item_number_id, c.color_number_id as color_id,c.color_order,c.size_order, sum(b.production_qnty) as ex_fact_prev_qnty ,c.size_number_id as size_id,d.job_no, d.buyer_name,d.style_ref_no,d.client_id as buyer_client
			from pro_ex_factory_delivery_mst m, pro_ex_factory_mst a, pro_ex_factory_dtls b, wo_po_color_size_breakdown c ,wo_po_details_master d, wo_po_break_down e where  d.job_no=e.job_no_mst and a.po_break_down_id=e.id and m.id=a.delivery_mst_id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and m.status_active=1 and m.is_deleted=0 and m.entry_form!=85  and m.delivery_company_id in ($cbo_work_company_name) $job_cond   $d_ex_order_cond $buyer_cond_2 $job_year_cond_2
			group by  c.po_break_down_id, c.item_number_id, c.color_number_id,c.size_number_id ,d.job_no, d.buyer_name,d.style_ref_no ,c.color_order,c.size_order,d.client_id order by c.color_order,c.size_order asc";
		
			// echo 	$ex_factory_tot_sql;
			$ex_factory_tot_sql_data=sql_select($ex_factory_tot_sql);
			foreach($ex_factory_tot_sql_data as $row)
			{

				
				$order_color_size_data_2[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]][$row[csf("size_id")]]["ex_fact_prev_qnty"]+=$row[csf("ex_fact_prev_qnty")];
                $buyer_wise_data[$row[csf("job_no")]]['client_id']=$row[csf("buyer_client")];
                $po_number_array_check[$row[csf("order_id")]]=$row[csf("order_id")];
			}

			//  echo "<pre>";
			// print_r($production_data);die;
			
			
			
			
		
			
				$sql_data=sql_select("SELECT  a.id,a.client_id as buyer_client,b.po_number,a.style_ref_no,c.order_quantity, c.color_number_id,a.buyer_name, c.item_number_id,c.size_number_id,a.job_no,c.po_break_down_id,c.color_order,c.size_order  FROM wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where
				a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 $job_cond_3 $buyer_cond_3 $job_year_cond_3 order by c.color_order,c.size_order asc");
				foreach($sql_data as $row) 
				{
                   
                    $po_arr[$row[csf("po_break_down_id")]]=$row[csf("po_number")];
                    if($po_number_array_check[$row[csf("po_break_down_id")]]){

                        $order_color_size_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['po_qty']+=$row[csf("order_quantity")];
                        $buyer_wise_data[$row[csf("job_no")]]['client_id']=$row[csf("buyer_client")];
                    }
					$order_qty_color_size_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]["order_quantity"]+=$row[csf("order_quantity")];
					// $buyer_wise_data[$row[csf("buyer_name")]]['style_ref_no']=$row[csf("style_ref_no")];
                    $style_ref_no_arr[$row[csf("job_no")]]['style_ref_no']=$row[csf("style_ref_no")];
					// $buyer_wise_data[$row[csf("buyer_name")]]['client_id']=$row[csf("client_id")];
                   

				}


		
  
			
			//echo $sql_color_size;die;
		 //  print_r($order_color_data);
		
			ob_start();
		 ?>
<fieldset style="width:2480px;">
    <div style="width:2480px;">
        <table width="2040" cellspacing="0">
            <tr class="form_caption" style="border:none;">
                <td colspan="31" align="center" style="border:none;font-size:14px; font-weight:bold">Size Wise GMTS Production Report </td>
            </tr>
            <tr style="border:none;">
                <td colspan="31" align="center" style="border:none; font-size:16px; font-weight:bold">
                    Working Company Name:
                    <? 
							$cbo_work_company_name_arr=explode(",",$cbo_work_company_name);
							$workingCompanyName="";
							foreach ($cbo_work_company_name_arr as $workig_cmp_name)
							{
								$workingCompanyName.= $company_arr[$workig_cmp_name]; 
							}
							echo chop($workingCompanyName);
							?>
                </td>
            </tr>

            <tr style="border:none;">
                <td colspan="31" align="center" style="border:none;font-size:12px; font-weight:bold">
                    Date:
                    <? echo $date_from." To ".$date_to ;?>
                </td>
            </tr>
        </table>
        <br/>

        <fieldset style="width:2480px; float:left;">
            <legend>Report Details Part</legend>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2460" class="rpt_table" align="left">
                <thead>
                    <tr>
                        <th width="40" rowspan="2">SL</th>
                        <th width="100" rowspan="2">Buyer</th>
                        <th width="100" rowspan="2">Client</th>
                        <th width="100" rowspan="2">Style Ref</th>
                        <th width="60" rowspan="2">Job No</th>
                        <th width="100" rowspan="2">Order No</th>
                        <th width="100" rowspan="2">Garment Item</th>
                        <th width="100" rowspan="2">Color</th>
                        <th width="100" rowspan="2">Size</th>
                        <th width="70" rowspan="2">Order Qty.</th>
                        <th width="140" colspan="3">Lay Quantity</th>
                        <th width="140" colspan="3">Sewing Input</th>
                        <th width="140" colspan="3">Sewing Output</th>
                        <th width="140" colspan="3">Poly Entry</th>
                        <th width="140" colspan="3">Packing & Finishing</th>
                        <th width="140" colspan="3">Ex-Factory</th>

                    </tr>
                    <tr>

                        <th width="70">Today </th>
                        <th width="70">Total </th>
                        <th width="70">Balance </th>

                        <th width="70">Today </th>
                        <th width="70">Total </th>
                        <th width="70">Balance </th>


                        <th width="70">Today </th>
                        <th width="70">Total </th>
                        <th width="70">Balance </th>


                        <th width="70">Today </th>
                        <th width="70">Total </th>
                        <th width="70">Balance </th>


                        <th width="70">Today </th>
                        <th width="70">Total </th>
                        <th width="70">Balance </th>


                        <th width="70">Today </th>
                        <th width="70">Total </th>
                        <th width="70">Balance </th>
                    </tr>
                </thead>
            </table>
            <div style="max-height:425px; overflow-y:scroll; width:2480px;" id="scroll_body">
                <table border="1" class="rpt_table" width="2460" rules="all" id="table_body" align="left">
                    <tbody>
                        <?
				//echo "<pre>";print_r($production_data);die;
				$i=1;
				foreach($order_color_size_data as $buyer_id=>$buyer_data)
				{
					foreach($buyer_data as $job_no=>$job_data)
					{
						foreach($job_data as $order_id=>$order_data)
						{
							foreach($order_data as $item_id=>$item_data)
							{
								foreach($item_data as $color_id=>$color_data)
								{
									foreach($color_data as $size_id=>$value)
									{
										
										
										
											$tot_lay_qnty=$tot_cutting_qnty=$tot_printing_qnty=$tot_printing_rcv_qnty=$tot_embroidery_qnty=$tot_embroidery_rcv_qnty=$tot_sewing_in_qnty=$tot_sewing_out_qnty=$tot_poly_qnty=$tot_paking_finish_qnty=$tot_ex_fact_qnty=0;
											$cut_qc_wip=$printing_wip=$emb_wip=$wash_wip=$sp_work_wip=$sewing_wip=$ex_fact_wip=$ex_fact_wip=0;
											$total_sp_work_reject=$total_sewing_reject=$total_poly_reject=$total_finish_reject=0;
											$po_id=$value['po_id'];
											
											if ($i%2==0)
											$bgcolor="#E9F3FF";
											else
											$bgcolor="#FFFFFF";

                                            $style_ref_no=$style_ref_no_arr[$job_no]['style_ref_no'];
											$client_id=$buyer_wise_data[$job_no]['client_id'];
                                            
											$order_qty=$order_qty_color_size_data[$buyer_id][$job_no][$order_id][$item_id][$color_id][$size_id]["order_quantity"];
											?>
                        <tr bgcolor="<? echo $bgcolor; ?>"
                            onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')"
                            id="tr_2nd<? echo $i; ?>">
                            <td width="40" align="center">
                                <? echo $i; ?>
                            </td>
                            <td width="100">
                                <p>
                                    <? echo $buyer_short_library[$buyer_id]; ?>&nbsp;
                                </p>
                            </td>
                            <td width="100">
                                <p>
                                    <? echo $clientArr[$client_id]; ?>&nbsp;
                                </p>
                            </td>
                            <td width="100">
                                <p>
                                    <? echo $style_ref_no; ?>&nbsp;
                                </p>
                            </td>
                            <td width="60" align="center">
                                <p>
                                    <? echo $job_no; ?>&nbsp;
                                </p>
                            </td>
                            <td width="100">
                                <p>
                                    <? echo $po_arr[$order_id]; ?>&nbsp;
                                </p>
                            </td>
                            <td width="100">
                                <p>
                                    <? echo $garments_item[$item_id]; ?>&nbsp;
                                </p>
                            </td>
                            <td width="100">
                                <p>
                                    <? echo $colorname_arr[$color_id]; ?>&nbsp;
                                </p>
                            </td>
                            <td width="100">
                                <p>
                                    <? echo $sizearr[$size_id]; ?>&nbsp;
                                </p>
                            </td>
                            <td width="70" align="right">
                                <?

												echo $order_qty;
												// echo number_format($value["order_quantity"],0);
												 $job_order_qnty+=$order_qty;
												 $color_order_qnty+=$order_qty;
												 $item_order_qnty+=$order_qty;
												 $po_order_qnty+=$order_qty;
												 $buyer_order_qnty+=$order_qty; 
												 $gt_order_qnty+=$order_qty; ?>
                            </td>
                            <td width="70" align="right">
                                <?
											
											if($date_from !=="" && $date_to !=="" && $job_cond_2 =="" || $date_from !=="" && $date_to !=="" && $job_cond_2 !==""){

                                                $cut_lay=$order_color_size_data_2[$buyer_id][$job_no][$order_id][$item_id][$color_id][$size_id]["cut_lay"];

														echo $cut_lay;
														
														$job_lay_qnty+=$cut_lay;
														$color_lay_qnty+=$cut_lay;
														$item_lay_qnty+=$cut_lay;
														$po_lay_qnty+=$cut_lay;
														$buyer_lay_qnty+=$cut_lay; 
														$gt_lay_qnty+=$cut_lay;

												}
													?>


                            </td>
                            <td width="70" align="right">
                                <? 
                                       $lay_prev_qnty=$order_color_size_data_2[$buyer_id][$job_no][$order_id][$item_id][$color_id][$size_id]["lay_prev_qnty"];

												echo $lay_prev_qnty;
												// $tot_lay_qnty=$production_data[$order_id][$item_id][$color_id][$size_id]["lay_prev_qnty"];
												//  echo number_format($tot_lay_qnty,0); 
												$tot_lay_qnty=$lay_prev_qnty;
												$job_tot_lay_qnty+=$tot_lay_qnty;
												 $color_tot_lay_qnty+=$tot_lay_qnty; 
												 $item_tot_lay_qnty+=$tot_lay_qnty; 
												 $po_tot_lay_qnty+=$tot_lay_qnty;
												$buyer_tot_lay_qnty+=$tot_lay_qnty; 
												$gt_tot_lay_qnty+=$tot_lay_qnty;?>
                            </td>
                            <td width="70" align="right">
                                <? $bal_lay_qnty=$order_qty-$tot_lay_qnty; echo number_format($bal_lay_qnty,0);
												$tot_bal_lay_qnty+=$bal_lay_qnty;
												$job_bal_lay_qnty+=$bal_lay_qnty;
												$color_bal_lay_qnty+=$bal_lay_qnty;
												$item_bal_lay_qnty+=$bal_lay_qnty;
												$po_bal_lay_qnty+=$bal_lay_qnty;
												$buyer_bal_lay_qnty+=$bal_lay_qnty;
												$gt_bal_lay_qnty+=$bal_lay_qnty;
												
												?>
                            </td>


                            <td width="70" align="right">
                                <?
														if($date_from !=="" && $date_to !=="" && $job_cond_2 =="" || $date_from !=="" && $date_to !=="" && $job_cond_2 !==""){

                                                            $sewing_in=$order_color_size_data_2[$buyer_id][$job_no][$order_id][$item_id][$color_id][$size_id]["sewing_in"];

														echo  $sewing_in;

													// number_format($production_data[$order_id][$item_id][$color_id][$size_id]["sewing_in_qnty"],0); 								
													
													
													$job_sewing_in_qnty+= $sewing_in;
													$color_sewing_in_qnty+= $sewing_in;
													$item_sewing_in_qnty+= $sewing_in;
													$po_sewing_in_qnty+= $sewing_in;
													$buyer_sewing_in_qnty+=$ $sewing_in; $gt_sewing_in_qnty+= $sewing_in;

														}
															?>
                            </td>
                            <td width="70" align="right">
                                <p>
                                    <? 

                                                    $sewing_in_prev_qnty=$order_color_size_data_2[$buyer_id][$job_no][$order_id][$item_id][$color_id][$size_id]["sewing_in_prev_qnty"];

                                                    echo  $sewing_in_prev_qnty;

													// echo $value["sewing_in_prev_qnty"];
											
												$tot_sewing_in_qnty=$sewing_in_prev_qnty;
												$job_tot_sewing_in_qnty+=$tot_sewing_in_qnty;
												$color_tot_sewing_in_qnty+=$tot_sewing_in_qnty;
												$item_tot_sewing_in_qnty+=$tot_sewing_in_qnty;
												$po_tot_sewing_in_qnty+=$tot_sewing_in_qnty;	
												$buyer_tot_sewing_in_qnty+=$tot_sewing_in_qnty; 
												$gt_tot_sewing_in_qnty+=$tot_sewing_in_qnty;?>
                                </p>
                            </td>

                            <td width="70" align="right">
                                <p>
                                    <? $bal_sewing_in_qnty=$tot_lay_qnty-$tot_sewing_in_qnty; echo number_format($bal_sewing_in_qnty,0);
												$job_bal_sewing_in_qnty+=$bal_sewing_in_qnty;
												$color_bal_sewing_in_qnty+=$bal_sewing_in_qnty;
												$item_bal_sewing_in_qnty+=$bal_sewing_in_qnty;
												$po_bal_sewing_in_qnty+=$bal_sewing_in_qnty;
												$gt_bal_sewing_in_qnty+=$bal_sewing_in_qnty;
												$buyer_bal_sewing_in_qnty+=$bal_sewing_in_qnty;
												
												
												?>
                                </p>
                            </td>

                            <td width="70" align="right">
                                <? 
															if($date_from !=="" && $date_to !=="" && $job_cond_2 =="" || $date_from !=="" && $date_to !=="" && $job_cond_2 !==""){
                                                                $sewing_out=$order_color_size_data_2[$buyer_id][$job_no][$order_id][$item_id][$color_id][$size_id]["sewing_out"];
													echo  $sewing_out;
											
													
														
												$job_sewing_out_qnty+=$sewing_out;
												 $color_sewing_out_qnty+=$sewing_out;
												 $item_sewing_out_qnty+=$sewing_out;
												 $po_sewing_out_qnty+=$sewing_out;
												$buyer_sewing_out_qnty+=$sewing_out; 
												$gt_sewing_out_qnty+=$sewing_out;
															}
												?>
                            </td>
                            <td width="70" align="right">
                                <? 
                                                $sewing_out_prev_qnty=$order_color_size_data_2[$buyer_id][$job_no][$order_id][$item_id][$color_id][$size_id]["sewing_out_prev_qnty"];
                                                echo  $sewing_out_prev_qnty;

												// echo $value["sewing_out_prev_qnty"];
											
												$tot_sewing_out_qnty=$sewing_out_prev_qnty;
												$job_tot_sewing_out_qnty+=$tot_sewing_out_qnty;
												$color_tot_sewing_out_qnty+=$tot_sewing_out_qnty;
												$item_tot_sewing_out_qnty+=$tot_sewing_out_qnty;
												$po_tot_sewing_out_qnty+=$tot_sewing_out_qnty;
												$buyer_tot_sewing_out_qnty+=$tot_sewing_out_qnty; 
												$gt_tot_sewing_out_qnty+=$tot_sewing_out_qnty;?>
                            </td>
                            <td width="70" align="right">
                                <? $bal_sewing_out_qnty=$tot_sewing_in_qnty-$tot_sewing_out_qnty; echo number_format($bal_sewing_out_qnty,0);
												$job_bal_sewing_out_qnty+=$bal_sewing_out_qnty;
												$color_bal_sewing_out_qnty+=$bal_sewing_out_qnty;
												$item_bal_sewing_out_qnty+=$bal_sewing_out_qnty;
												$po_bal_sewing_out_qnty+=$bal_sewing_out_qnty;
												$gt_bal_sewing_out_qnty+=$bal_sewing_out_qnty;
												$buyer_bal_sewing_out_qnty+=$bal_sewing_out_qnty;
												
												
												?>
                            </td>



                            <td width="70" align="right">
                                <? 
                                            if($date_from !=="" && $date_to !=="" && $job_cond_2 =="" || $date_from !=="" && $date_to !=="" && $job_cond_2 !==""){

                                                $poly_qnty=$order_color_size_data_2[$buyer_id][$job_no][$order_id][$item_id][$color_id][$size_id]["poly_qnty"];
													echo $poly_qnty;
											
												 $job_poly_qnty+=$poly_qnty;
												$color_poly_qnty+=$poly_qnty;
												$item_poly_qnty+=$poly_qnty;
												$po_poly_qnty+=$poly_qnty;									
												$buyer_poly_qnty+=$poly_qnty;
												 $gt_poly_qnty+=$poly_qnty;}?>
                            </td>
                            <td width="70" align="right">
                                <? 
                                                $poly_prev_qnty=$order_color_size_data_2[$buyer_id][$job_no][$order_id][$item_id][$color_id][$size_id]["poly_prev_qnty"];
                                                        echo $poly_prev_qnty;

												// echo $value["poly_prev_qnty"];
										
												$tot_poly_qnty=$poly_prev_qnty;
												$job_tot_poly_qnty+=$tot_poly_qnty;
												$color_tot_poly_qnty+=$tot_poly_qnty;
												$item_tot_poly_qnty+=$tot_poly_qnty;
												$po_tot_poly_qnty+=$tot_poly_qnty;
												$buyer_tot_poly_qnty+=$tot_poly_qnty; $gt_tot_poly_qnty+=$tot_poly_qnty;?>
                            </td>
                            <td width="70" align="right">
                                <? $bal_poly_qnty=$tot_sewing_in_qnty-$tot_poly_qnty; echo number_format($bal_poly_qnty,0);
												$job_bal_poly_qnty+=$bal_poly_qnty;
												$color_bal_poly_qnty+=$bal_poly_qnty;
												$item_bal_poly_qnty+=$bal_poly_qnty;
												$po_bal_poly_qnty+=$bal_poly_qnty;
												$gt_bal_poly_qnty+=$tot_poly_qnty;
												$buyer_bal_poly_qnty+=$tot_poly_qnty;
												
												?>
                            </td>


                            <td width="70" align="right">
                                <?
															if($date_from !=="" && $date_to !=="" && $job_cond_2 =="" || $date_from !=="" && $date_to !=="" && $job_cond_2 !==""){

                                                                $paking_finish_qnty=$order_color_size_data_2[$buyer_id][$job_no][$order_id][$item_id][$color_id][$size_id]["paking_finish_qnty"];
												echo $paking_finish_qnty;
											
											
												$job_paking_finish_qnty+=$paking_finish_qnty;
												$color_paking_finish_qnty+=$paking_finish_qnty;
												$item_paking_finish_qnty+=$paking_finish_qnty;
												$po_paking_finish_qnty+=$paking_finish_qnty;
												$buyer_paking_finish_qnty+=$paking_finish_qnty;
												$gt_paking_finish_qnty+=$paking_finish_qnty;}?>
                            </td>
                            <td width="70" align="right">
                                <? 
                                                $paking_finish_prev_qnty=$order_color_size_data_2[$buyer_id][$job_no][$order_id][$item_id][$color_id][$size_id]["paking_finish_prev_qnty"];
                                                echo $paking_finish_prev_qnty;
													// echo $value["paking_finish_prev_qnty"];
											
												$tot_paking_finish_qnty=$paking_finish_prev_qnty;
												$job_tot_paking_finish_qnty+=$tot_paking_finish_qnty; 
												$color_tot_paking_finish_qnty+=$tot_paking_finish_qnty;
												$item_tot_paking_finish_qnty+=$tot_paking_finish_qnty;
												$po_tot_paking_finish_qnty+=$tot_paking_finish_qnty;
												$buyer_tot_paking_finish_qnty+=$tot_paking_finish_qnty; $gt_tot_paking_finish_qnty+=$tot_paking_finish_qnty;?>
                            </td>
                            <td width="70" align="right">
                                <? $bal_paking_finish_qnty=$tot_poly_qnty-$tot_paking_finish_qnty; echo number_format($bal_paking_finish_qnty,0);
												$job_bal_paking_finish_qnty+=$bal_paking_finish_qnty;
												$color_bal_paking_finish_qnty+=$bal_paking_finish_qnty;
												$item_bal_paking_finish_qnty+=$bal_paking_finish_qnty;
												$po_bal_paking_finish_qnty+=$bal_paking_finish_qnty;
												$gt_bal_paking_finish_qnty+=$bal_paking_finish_qnty;
												$buyer_bal_paking_finish_qnty+=$bal_paking_finish_qnty;
											
												
												?>
                            </td>


                            <td width="70" align="right">
                                <? 
																if($date_from !=="" && $date_to !=="" && $job_cond_2 =="" || $date_from !=="" && $date_to !=="" && $job_cond_2 !==""){

                                                                    $ex_fact_qnty=$order_color_size_data_2[$buyer_id][$job_no][$order_id][$item_id][$color_id][$size_id]["ex_fact_qnty"];

													echo $ex_fact_qnty;
										
												
												$job_ex_fact_qnty+=$ex_fact_qnty;
												$color_ex_fact_qnty+=$ex_fact_qnty;
												$item_ex_fact_qnty+=$ex_fact_qnty;
												$po_ex_fact_qnty+=$ex_fact_qnty;
												 $buyer_ex_fact_qnty+=$ex_fact_qnty;
												 $gt_ex_fact_qnty+=$ex_fact_qnty;}?>
                            </td>
                            <td width="70" align="right">
                                <?
                                $ex_fact_prev_qnty=$order_color_size_data_2[$buyer_id][$job_no][$order_id][$item_id][$color_id][$size_id]["ex_fact_prev_qnty"];
													echo  $ex_fact_prev_qnty;
											
												$tot_ex_fact_qnty=$ex_fact_prev_qnty;
												 $job_tot_ex_fact_qnty+=$tot_ex_fact_qnty; 
												 $color_tot_ex_fact_qnty+=$tot_ex_fact_qnty;
												 $item_tot_ex_fact_qnty+=$tot_ex_fact_qnty;
												 $po_tot_ex_fact_qnty+=$tot_ex_fact_qnty;
												
												$buyer_tot_ex_fact_qnty+=$tot_ex_fact_qnty; $gt_tot_ex_fact_qnty+=$tot_ex_fact_qnty;?>
                            </td>
                            <td width="70" align="right">
                                <? $bal_ex_fact_qnty=$tot_paking_finish_qnty-$tot_ex_fact_qnty; echo number_format($bal_ex_fact_qnty,0); 
												$job_bal_ex_fact_qnty+=$bal_ex_fact_qnty;
												$color_bal_ex_fact_qnty+=$bal_ex_fact_qnty;
												$item_bal_ex_fact_qnty +=$bal_ex_fact_qnty;
												$po_bal_ex_fact_qnty+=$bal_ex_fact_qnty;
												$gt_bal_ex_fact_qnty+=$bal_ex_fact_qnty;
												$buyer_bal_ex_fact_qnty+=$bal_ex_fact_qnty;
												
												?>
                            </td>

                        </tr>
                        <?
		                                    $i++;
											
									
										
									}
									//  if(color_bal_lay_qnty>0 || $color_bal_sewing_in_qnty>0 || $color_bal_sewing_out_qnty>0 || $color_bal_poly_qnty>0 || $color_bal_paking_finish_qnty>0 || $color_bal_ex_fact_qnty>0)
									//  {
									?>
                        <tr bgcolor="#F4F3C4">
                            <td align="right" colspan="9" style="font-weight:bold;">Color Total:</td>
                            <td width="70" align="right">
                                <? echo number_format($color_order_qnty,0); $color_order_qnty=0; ?>
                            </td>

                            <td width="70" align="right">
                                <? echo number_format($color_lay_qnty,0); $color_lay_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($color_tot_lay_qnty,0); $color_tot_lay_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($color_bal_lay_qnty,0); $color_bal_lay_qnty=0;?>
                            </td>

                            <td width="70" align="right">
                                <? echo number_format($color_sewing_in_qnty,0); $color_sewing_in_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($color_tot_sewing_in_qnty,0); $color_tot_sewing_in_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($color_bal_sewing_in_qnty,0); $color_bal_sewing_in_qnty=0;?>
                            </td>

                            <td width="70" align="right">
                                <? echo number_format($color_sewing_out_qnty,0); $color_sewing_out_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($color_tot_sewing_out_qnty,0); $color_tot_sewing_out_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($color_bal_sewing_out_qnty,0); $color_bal_sewing_out_qnty=0;?>
                            </td>


                            <td width="70" align="right">
                                <? echo number_format($color_poly_qnty,0); $color_poly_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($color_tot_poly_qnty,0); $color_tot_poly_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($color_bal_poly_qnty,0); $color_bal_poly_qnty=0;?>
                            </td>


                            <td width="70" align="right">
                                <? echo number_format($color_paking_finish_qnty,0); $color_paking_finish_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($color_tot_paking_finish_qnty,0); $color_tot_paking_finish_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($color_bal_paking_finish_qnty,0); $color_bal_paking_finish_qnty=0;?>
                            </td>


                            <td width="70" align="right">
                                <? echo number_format($color_ex_fact_qnty,0); $color_ex_fact_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($color_tot_ex_fact_qnty,0); $color_tot_ex_fact_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($color_bal_ex_fact_qnty,0); $color_bal_ex_fact_qnty=0;?>
                            </td>

                        </tr>
                        <?
								//}
							}
								// if($item_tot_lay_qnty>0 || $item_tot_sewing_in_qnty>0 || $item_tot_sewing_out_qnty >0 || $item_tot_poly_qnty >0 || $item_tot_paking_finish_qnty>0 || $item_tot_ex_fact_qnty>0)
								// 	{
							
								?>
                        <tr bgcolor="#F4F3C4">
                            <td align="right" colspan="9" style="font-weight:bold;">Item Total:</td>
                            <td width="70" align="right">
                                <? echo number_format($item_order_qnty,0); $item_order_qnty=0; ?>
                            </td>

                            <td width="70" align="right">
                                <? echo number_format($item_lay_qnty,0); $item_lay_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($item_tot_lay_qnty,0); $item_tot_lay_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($item_bal_lay_qnty,0); $item_bal_lay_qnty=0;?>
                            </td>

                            <td width="70" align="right">
                                <? echo number_format($item_sewing_in_qnty,0); $item_sewing_in_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($item_tot_sewing_in_qnty,0); $item_tot_sewing_in_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($item_bal_sewing_in_qnty,0); $item_bal_sewing_in_qnty=0;?>
                            </td>

                            <td width="70" align="right">
                                <? echo number_format($item_sewing_out_qnty,0); $item_sewing_out_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($item_tot_sewing_out_qnty,0); $item_tot_sewing_out_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($item_bal_sewing_out_qnty,0); $item_bal_sewing_out_qnty=0;?>
                            </td>


                            <td width="70" align="right">
                                <? echo number_format($item_poly_qnty,0); $item_poly_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($item_tot_poly_qnty,0); $item_tot_poly_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($item_bal_poly_qnty,0); $item_bal_poly_qnty=0;?>
                            </td>


                            <td width="70" align="right">
                                <? echo number_format($item_paking_finish_qnty,0); $item_paking_finish_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($item_tot_paking_finish_qnty,0); $item_tot_paking_finish_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($item_bal_paking_finish_qnty,0); $item_bal_paking_finish_qnty=0;?>
                            </td>


                            <td width="70" align="right">
                                <? echo number_format($item_ex_fact_qnty,0); $item_ex_fact_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($item_tot_ex_fact_qnty,0); $item_tot_ex_fact_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($item_bal_ex_fact_qnty,0); $item_bal_ex_fact_qnty=0;?>
                            </td>

                        </tr>
                        <?
						//	}
						}

							// if($po_tot_lay_qnty>0 || $po_tot_sewing_in_qnty>0 || $po_tot_sewing_out_qnty >0 || $po_tot_poly_qnty >0 || $po_tot_paking_finish_qnty>0 || $po_tot_ex_fact_qnty>0)
							// 		{
							?>
                        <tr bgcolor="#F4F3C4">
                            <td align="right" colspan="9" style="font-weight:bold;">PO Total:</td>
                            <td width="70" align="right">
                                <? echo number_format($po_order_qnty,0); $po_order_qnty=0; ?>
                            </td>

                            <td width="70" align="right">
                                <? echo number_format($po_lay_qnty,0); $po_lay_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($po_tot_lay_qnty,0); $po_tot_lay_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($po_bal_lay_qnty,0); $po_bal_lay_qnty=0;?>
                            </td>

                            <td width="70" align="right">
                                <? echo number_format($po_sewing_in_qnty,0); $po_sewing_in_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($po_tot_sewing_in_qnty,0); $po_tot_sewing_in_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($po_bal_sewing_in_qnty,0); $color_bal_sewing_in_qnty=0;?>
                            </td>

                            <td width="70" align="right">
                                <? echo number_format($po_sewing_out_qnty,0); $po_sewing_out_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($po_tot_sewing_out_qnty,0); $po_tot_sewing_out_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($po_bal_sewing_out_qnty,0); $po_bal_sewing_out_qnty=0;?>
                            </td>


                            <td width="70" align="right">
                                <? echo number_format($po_poly_qnty,0); $po_poly_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($po_tot_poly_qnty,0); $po_tot_poly_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($po_bal_poly_qnty,0); $po_bal_poly_qnty=0;?>
                            </td>


                            <td width="70" align="right">
                                <? echo number_format($po_paking_finish_qnty,0); $po_paking_finish_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($po_tot_paking_finish_qnty,0); $po_tot_paking_finish_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($po_bal_paking_finish_qnty,0); $po_bal_paking_finish_qnty=0;?>
                            </td>


                            <td width="70" align="right">
                                <? echo number_format($po_ex_fact_qnty,0); $po_ex_fact_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($po_tot_ex_fact_qnty,0); $po_tot_ex_fact_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($po_bal_ex_fact_qnty,0); $po_bal_ex_fact_qnty=0;?>
                            </td>
                        </tr>
                        <?
					//	}
					}
						// if($job_tot_lay_qnty>0 || $job_tot_sewing_in_qnty>0 || $job_tot_sewing_out_qnty >0 || $job_tot_poly_qnty >0 || $job_tot_paking_finish_qnty>0 || $job_tot_ex_fact_qnty>0)
						// 			{
						?>
                        <tr bgcolor="#F4F3C4">
                            <td align="right" colspan="9" style="font-weight:bold;">Job Total:</td>
                            <td width="70" align="right">
                                <? echo number_format($job_order_qnty,0); $job_order_qnty=0; ?>
                            </td>

                            <td width="70" align="right">
                                <? echo number_format($job_lay_qnty,0); $job_lay_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($job_tot_lay_qnty,0); $job_tot_lay_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($job_bal_lay_qnty,0); $job_bal_lay_qnty=0;?>
                            </td>

                            <td width="70" align="right">
                                <? echo number_format($job_sewing_in_qnty,0); $job_sewing_in_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($job_tot_sewing_in_qnty,0); $job_tot_sewing_in_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($job_bal_sewing_in_qnty,0); $job_tot_sewing_in_qnty=0;?>
                            </td>

                            <td width="70" align="right">
                                <? echo number_format($job_sewing_out_qnty,0); $job_sewing_out_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($job_tot_sewing_out_qnty,0); $job_tot_sewing_out_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($job_bal_sewing_out_qnty,0); $job_bal_sewing_out_qnty=0;?>
                            </td>


                            <td width="70" align="right">
                                <? echo number_format($job_poly_qnty,0); $job_poly_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($job_tot_poly_qnty,0); $job_tot_poly_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($job_bal_poly_qnty,0); $job_bal_poly_qnty=0;?>
                            </td>


                            <td width="70" align="right">
                                <? echo number_format($job_paking_finish_qnty,0); $job_paking_finish_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($job_tot_paking_finish_qnty,0); $job_tot_paking_finish_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($job_bal_paking_finish_qnty,0); $job_bal_paking_finish_qnty=0;?>
                            </td>


                            <td width="70" align="right">
                                <? echo number_format($job_ex_fact_qnty,0); $job_ex_fact_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($job_tot_ex_fact_qnty,0); $job_tot_ex_fact_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($job_bal_ex_fact_qnty,0); $job_bal_ex_fact_qnty=0;?>
                            </td>

                        </tr>
                        <?
				//	}
				}
					// if($buyer_tot_lay_qnty>0 || $buyer_tot_sewing_in_qnty>0 || $buyer_tot_sewing_out_qnty >0 || $buyer_tot_poly_qnty >0 || $buyer_tot_paking_finish_qnty>0 || $buyer_tot_ex_fact_qnty>0)
					// 				{
					?>
                        <tr bgcolor="#CCCCCC">
                            <td align="right" colspan="9" style="font-weight:bold;">Buyer Total:</td>
                            <td width="70" align="right">
                                <? echo number_format($buyer_order_qnty,0); $buyer_order_qnty=0; ?>
                            </td>

                            <td width="70" align="right">
                                <? echo number_format($buyer_lay_qnty,0);  $buyer_lay_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($buyer_tot_lay_qnty,0); $buyer_tot_lay_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($buyer_bal_lay_qnty,0); $buyer_tot_lay_qnty=0;?>
                            </td>


                            <td width="70" align="right">
                                <? echo number_format($buyer_sewing_in_qnty,0);  $buyer_sewing_in_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($buyer_tot_sewing_in_qnty,0);  $buyer_tot_sewing_in_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($buyer_bal_sewing_in_qnty,0);  $buyer_tot_sewing_in_qnty=0;?>
                            </td>

                            <td width="70" align="right">
                                <? echo number_format($buyer_sewing_out_qnty,0);  $buyer_sewing_out_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($buyer_tot_sewing_out_qnty,0); $buyer_tot_sewing_out_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($buyer_bal_sewing_out_qnty,0); $buyer_bal_sewing_out_qnty=0;?>
                            </td>


                            <td width="70" align="right">
                                <? echo number_format($buyer_poly_qnty,0); $buyer_poly_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($buyer_tot_poly_qnty,0); $buyer_tot_poly_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($buyer_bal_poly_qnty,0); $buyer_bal_poly_qnty=0;?>
                            </td>


                            <td width="70" align="right">
                                <? echo number_format($buyer_paking_finish_qnty,0);  $buyer_paking_finish_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($buyer_tot_paking_finish_qnty,0); $buyer_tot_paking_finish_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($buyer_bal_paking_finish_qnty,0); $buyer_bal_paking_finish_qnty=0;?>
                            </td>


                            <td width="70" align="right">
                                <? echo number_format($buyer_ex_fact_qnty,0); $buyer_ex_fact_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($buyer_tot_ex_fact_qnty,0); $buyer_tot_ex_fact_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($buyer_bal_ex_fact_qnty,0); $buyer_bal_ex_fact_qnty=0;?>
                            </td>

                        </tr>
                        <?
				//}
			}
		        
		        ?>
                    </tbody>


                </table>
            </div>
            <table border="1" class="rpt_table" width="2460" rules="all" style="margin-left: 2px;" align="left" id="">
                <tfoot>
                    <tr>
                        <th style="word-break: break-all;word-wrap: break-word;" width="40" align="center">&nbsp;</th>
                        <th style="word-break: break-all;word-wrap: break-word;" width="100">
                            <p>&nbsp;</p>
                        </th>
                        <th style="word-break: break-all;word-wrap: break-word;" width="100">
                            <p>&nbsp;</p>
                        </th>
                        <th style="word-break: break-all;word-wrap: break-word;" width="100">
                            <p>&nbsp;</p>
                        </th>
                        <th style="word-break: break-all;word-wrap: break-word;" width="60">
                            <p>&nbsp;</p>
                        </th>
                        <th style="word-break: break-all;word-wrap: break-word;" width="100" align="center">
                            <p>&nbsp;</p>
                        </th>
                        <th style="word-break: break-all;word-wrap: break-word;" width="100" align="center">
                            <p>&nbsp;</p>
                        </th>
                        <th style="word-break: break-all;word-wrap: break-word;" width="100" align="center">
                            <p>&nbsp;</p>
                        </th>


                        <th width="100"
                            style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;">Grand
                            Total</th>
                        <th width="70" align="right"
                            style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;">
                            <? echo number_format($gt_order_qnty,0);?>
                        </th>

                        <th width="70" align="right"
                            style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;">
                            <? echo number_format($gt_lay_qnty,0);?>
                        </th>
                        <th width="70" align="right"
                            style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;">
                            <? echo number_format($gt_tot_lay_qnty,0);?>
                        </th>
                        <th width="70" align="right"
                            style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;">
                            <? echo number_format($gt_bal_lay_qnty,0);?>
                        </th>



                        <th width="70" align="right"
                            style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;">
                            <? echo number_format($gt_sewing_in_qnty,0);?>
                        </th>
                        <th width="70" align="right"
                            style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;">
                            <? echo number_format($gt_tot_sewing_in_qnty,0); ?>
                        </th>
                        <th width="70" align="right"
                            style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;">
                            <? echo number_format($gt_bal_sewing_in_qnty,0); ?>
                        </th>

                        <th width="70" align="right"
                            style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;">
                            <? echo number_format($gt_sewing_out_qnty,0); ?>
                        </th>
                        <th width="70" align="right"
                            style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;">
                            <? echo number_format($gt_tot_sewing_out_qnty,0); ?>
                        </th>
                        <th width="70" align="right"
                            style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;">
                            <? echo number_format($gt_bal_sewing_out_qnty,0); ?>
                        </th>

                        <th width="70" align="right"
                            style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;">
                            <? echo number_format($gt_poly_qnty,0);?>
                        </th>
                        <th width="70" align="right"
                            style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;">
                            <? echo number_format($gt_tot_poly_qnty,0); ?>
                        </th>
                        <th width="70" align="right"
                            style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;">
                            <? echo number_format($gt_bal_poly_qnty,0); ?>
                        </th>

                        <th width="70" align="right"
                            style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;">
                            <? echo number_format($gt_paking_finish_qnty,0);?>
                        </th>
                        <th width="70" align="right"
                            style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;">
                            <? echo number_format($gt_tot_paking_finish_qnty,0); ?>
                        </th>
                        <th width="70" align="right"
                            style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;">
                            <? echo number_format($gt_bal_paking_finish_qnty,0); ?>
                        </th>

                        <th width="70" align="right"
                            style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;">
                            <? echo number_format($gt_ex_fact_qnty,0);?>
                        </th>
                        <th width="70" align="right"
                            style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;">
                            <? echo number_format($gt_tot_ex_fact_qnty,0); ?>
                        </th>
                        <th width="70" align="right"
                            style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;">
                            <? echo number_format($gt_bal_ex_fact_qnty,0); ?>
                        </th>

                    </tr>
                </tfoot>
            </table>
        </fieldset>
    </div>
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
	$create_new_doc = fopen($filename, 'w') or die('can not open');
	$is_created = fwrite($create_new_doc,ob_get_contents()) or die('can not write');
	//$filename=$user_id."_".$name.".xls";
	// echo "$total_data####$filename";
	exit();
}







if($action=="openJobNoPopup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
<script>
var selected_id = new Array;
var selected_name = new Array;
var selected_no = new Array;

function check_all_data() {
    var tbl_row_count = document.getElementById('list_view').rows.length;
    tbl_row_count = tbl_row_count - 0;
    for (var i = 1; i <= tbl_row_count; i++) {
        if ($('#tr_' + i).is(':visible')) {
            var onclickString = $('#tr_' + i).attr('onclick');
            var paramArr = onclickString.split("'");
            var functionParam = paramArr[1];
            js_set_value(functionParam);
        }

    }
}

function toggle(x, origColor) {
    var newColor = 'yellow';
    if (x.style) {
        x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
    }
}

function js_set_value(strCon) {
    //alert(strCon);
    var splitSTR = strCon.split("_");
    var str_or = splitSTR[0];
    var selectID = splitSTR[1];
    var selectDESC = splitSTR[2];
    //$('#txt_individual_id' + str).val(splitSTR[1]);
    //$('#txt_individual' + str).val('"'+splitSTR[2]+'"');

    toggle(document.getElementById('tr_' + str_or), '#FFFFCC');

    if (jQuery.inArray(selectID, selected_id) == -1) {
        selected_id.push(selectID);
        selected_name.push(selectDESC);
        selected_no.push(str_or);
    } else {
        for (var i = 0; i < selected_id.length; i++) {
            if (selected_id[i] == selectID) break;
        }
        selected_id.splice(i, 1);
        selected_name.splice(i, 1);
        selected_no.splice(i, 1);
    }
    var id = '';
    var name = '';
    var job = '';
    var num = '';
    for (var i = 0; i < selected_id.length; i++) {
        id += selected_id[i] + ',';
        name += selected_name[i] + ',';
        num += selected_no[i] + ',';
    }
    id = id.substr(0, id.length - 1);
    name = name.substr(0, name.length - 1);
    num = num.substr(0, num.length - 1);
    //alert(num);
    $('#txt_selected_po').val(id);
    $('#txt_selected_job').val(name);
    $('#txt_selected_no').val(num);
}
</script>
<?
	$buyer=str_replace("'","",$buyer);
	$w_company=str_replace("'","",$w_company);
	//echo $w_company;
	$lc_company=str_replace("'","",$lc_company);
	$job_year=str_replace("'","",$job_year);

	
	if($w_company!=0) $w_company="company_name=$w_company"; else $w_company="";
	if($buyer!=0) $buyer_cond="and buyer_name=$buyer"; else $buyer_cond="";
	if($db_type==0)
	{
		if($job_year!=0) $job_year_cond=" and year(insert_date)=$job_year"; else $job_year_cond="";
		$select_date=" year(insert_date)";
	}
	else if($db_type==2)
	{
		if($job_year!=0) $job_year_cond=" and to_char(insert_date,'YYYY')=$job_year"; else $job_year_cond="";
		$select_date=" to_char(insert_date,'YYYY')";
	}
	if($txt_style_ref_id!="") $style_cond=" and b.id in($txt_style_ref_id)"; else $style_cond="";
	// $sql = "SELECT a.id,a.po_number,a.job_no_mst,b.style_ref_no,b.job_no_prefix_num,$select_date as year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no $lc_company_cond $  $job_year_cond  $style_cond and a.status_active in(1,2,3) and b.status_active=1"; 
    $buyer_arr=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
    $arr=array(2=>$buyer_arr);
	$sql ="SELECT id,job_no,style_ref_no,job_no_prefix_num, to_char(insert_date,'YYYY') as year,buyer_name from  wo_po_details_master 
	 where  status_active=1  $job_year_cond  $buyer_cond ";
//	echo $sql; die;
	echo create_list_view("list_view", "Year,Job No,Buyer Name,Style Ref No","70,50,80,150","420","360",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "0,0,buyer_name,0", $arr, "year,job_no_prefix_num,buyer_name,style_ref_no", "","setFilterGrid('list_view',-1)","0","",1) ;	

	echo "<input type='hidden' id='txt_selected_po' />";
	echo "<input type='hidden' id='txt_selected_job' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	exit();
}
?>
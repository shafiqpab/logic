<?
header('Content-type:text/html; charset=utf-8');
session_start();
//include('../../../includes/common.php');
include('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.yarns.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer ",'id','buyer_name');


$mrr_date_check="";
$select_insert_year="";
$date_ref="";
$group_concat="";
if($db_type==2 || $db_type==1)
{
  $mrr_date_check="and to_char(insert_date,'YYYY')=".date('Y',time())."";
  $select_insert_year="to_char";
  $date_ref=",'YYYY'";
  $group_concat="wm_concat";
  // LISTAGG(b.po_breakdown_id, ',') WITHIN GROUP (ORDER BY po_breakdown_id)
}
else if ($db_type==0)
{
  $mrr_date_check="and year(insert_date)=".date('Y',time())."";
  $select_insert_year="year";
  $date_ref="";
  $group_concat="group_concat";
}

if($action=="load_drop_down_buyer")
{
  echo create_drop_down( "cbo_buyer_name", 135, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$data $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", 0, "",0 );
  exit();

}

if($action=="load_drop_down_buyer1")
{
  $data=explode("__",$data);
  echo create_drop_down( "cbobuyername_".$data[1], 90, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$data[0] $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", 0, "",0 );
  exit();

}

//------------------------------------------------------------------------------------------------------
if ($action=="load_drop_down_supplier")
{
  echo create_drop_down( "cbo_supplier", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id and b.party_type in(2) and c.tag_company in($data) order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
  exit();
}

if($action=="print_button_variable_setting")
{
    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=5 and report_id=69 and is_deleted=0 and status_active=1");
    $printButton=explode(',',$print_report_format);
  foreach($printButton as $id){
    if($id==134)$buttonHtml.='<input id="Print1" class="formbutton_disabled printReport" type="button" style="width:80px" onclick="fnc_yarn_req_entry(4)" name="print" value="Print">';
    if($id==135)$buttonHtml.='<input type="button" style="width:80px;" id="print2"  onClick="fnc_yarn_req_entry(6)"   class="formbutton_disabled printReport" name="Print2" value="Print2" />';
    if($id==136)$buttonHtml.='<input type="button" style="width:80px;" id="print3"  onClick="fnc_yarn_req_entry(7)" class="formbutton_disabled printReport" name="Print3" value="Print3" />';
    if($id==137)$buttonHtml.='<input type="button" style="width:80px;" id="print4"  onClick="fnc_yarn_req_entry(8)" class="formbutton_disabled printReport" name="print4" value="Print4" />';
    if($id==64)$buttonHtml.='<input type="button" style="width:80px;" id="print5"  onClick="fnc_yarn_req_entry(9)" class="formbutton_disabled printReport" name="print5" value="Print5" />';

  }
   echo "document.getElementById('button_data_panel').innerHTML = '".$buttonHtml."';\n";
    exit();
}


$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');


if ($action=="order_search_popup")
{
    echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
  extract($_REQUEST);
  $permission=$_SESSION['page_permission'];
  //echo $company;
  ?>
    <script>
      function js_set_value(str)
      {
        $("#hidden_tbl_id").val(str); // wo/pi id
        parent.emailwindow.hide();
      }
      var permission='<? echo $permission; ?>';
    </script>

    <div align="center" style="width:100%;" >
    <form name="searchjob"  id="searchjob" autocomplete="off">
      <table width="600" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                <thead>
                    <th width="140">Company</th>
                    <th width="140">Buyer</th>
                    <th width="50">Year</th>
                    <th width="80">Job No</th>
                    <th width="80">Style No</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:85px" class="formbutton" onClick="reset_form('searchjob','search_div','','','')"  /></th>
                </thead>
                <tbody>
                    <tr>
                        <td>
              <?
                          echo create_drop_down( "cbo_company_name", 135, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", str_replace("'","",$company), "load_drop_down( 'yarn_requisition_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
                        ?>
                        </td>
                        <td align="center" id="buyer_td">
              <?
                          $blank_array="select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name";
                //echo create_drop_down( "cbo_buyer_name", 130, $blank_array, 1, "-- Select Buyer --", str_replace("'","",$cbo_buyer_name), "" );
                echo create_drop_down( "cbo_buyer_name", 135, $blank_array,"id,buyer_name", 1, "-- Select Buyer --",0);
                        ?>
                        </td>
                        <td>
                        <?
                            $year_current=date("Y");
                            echo create_drop_down( "cbo_job_year", 60, $year,"", 1, "All",$year_current);
                        ?>
                        </td>
                        <td align="center">
                            <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:75px" />
                         </td>
                         <td align="center">
                            <input type="text" name="txt_style_no" id="txt_style_no" class="text_boxes" style="width:75px" />
                         </td>
                         <td align="center">
                            <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_style_no').value+'_'+document.getElementById('cbo_job_year').value, 'create_job_search_list_view', 'search_div', 'yarn_requisition_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:85px;" />
                        </td>
                  </tr>
                </tbody>
            </table>
            <br>
            <div align="center" valign="top" id="search_div"> </div>
            </form>
       </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>

<?
}


if ($action=="create_job_search_list_view")
{
  $data=explode("_",$data);
  $cbo_company_name=str_replace("'","",$data[0]);
  $cbo_buyer_name=str_replace("'","",$data[1]);
  $txt_job_no=str_replace("'","",$data[2]);
  $txt_style_no=str_replace("'","",$data[3]);
  $cbo_job_year=str_replace("'","",$data[4]);

  //echo $cbo_company_name."**".$txt_style_no."**".$txt_job_no."<br>";die;
  $sql_cond="";
  if($cbo_company_name!=0) $sql_cond=" and a.company_name='$cbo_company_name'";
  if($cbo_buyer_name!=0) $sql_cond.=" and a.buyer_name='$cbo_buyer_name'";
  if($txt_job_no!="") $sql_cond.=" and a.job_no like '%$txt_job_no%'";
  if($txt_style_no!="") $sql_cond.=" and a.style_ref_no like '%$txt_style_no%'";


  if($db_type==0)
  {
    if($cbo_job_year!=0) $sql_cond.=" and year(a.insert_date)='$cbo_job_year'";

    $sql="select a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, year(a.insert_date) as year from  wo_po_details_master a,  wo_pre_cost_fab_yarn_cost_dtls b where a.job_no=b.job_no and a.status_active=1 $sql_cond group by  a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, year(a.insert_date) order by a.job_no";
  }
  else if($db_type==2)
  {

    if($cbo_job_year!=0) $sql_cond.=" and to_char(a.insert_date,'YYYY')='$cbo_job_year'";

    $sql="select a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, to_char(a.insert_date,'YYYY') as year from  wo_po_details_master a,  wo_pre_cost_fab_yarn_cost_dtls b where a.job_no=b.job_no and a.status_active=1 $sql_cond group by a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, to_char(a.insert_date,'YYYY') order by a.job_no";
  }
  //echo $sql;//die;

  ?>
  <div style="width:550px;">
    <input type="hidden" id="hidden_tbl_id">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="550" class="rpt_table" >
            <thead>
                <th width="50">SL</th>
                <th width="80">Year</th>
                <th width="120">Job No</th>
                <th width="130">Buyer</th>
                <th > Style Ref.NO</th>

            </thead>
        </table>
        <div style="width:550px; overflow-y:scroll; max-height:250px;" id="buyer_list_view" >
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="532" class="rpt_table" id="tbl_list_search">
            <?

        $i=1;
        $nameArray=sql_select( $sql );
        foreach ($nameArray as $selectResult)
        {
          $po_number=implode(",",array_unique(explode(",",$selectResult[csf("po_number")])));
          if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
        ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>+'_'+'<? echo $selectResult[csf('job_no')]; ?>'+'_'+'<? echo $selectResult[csf('buyer_name')]; ?>'); ">

                     <td width="50"><p> <? echo $i; ?></p></td>
                      <td width="80"  align="center"> <p><? echo $selectResult[csf('year')]; ?></p></td>
                      <td width="120"  align="center"> <p><? echo $selectResult[csf("job_no")]; ?></p></td>
                      <td width="130"><p><?  echo  $buyer_arr[$selectResult[csf('buyer_name')]]; ?></p></td>
                      <td> <p><?  echo $selectResult[csf('style_ref_no')]; ?></p></td>
                    </tr>
                <?
                  $i++;
        }
      ?>
            </table>
    </div>
    </div>
   <?

}

if($action=="order_search_with_wo_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$permission=$_SESSION['page_permission'];
	//echo $company;
	?>
    <script>
      function js_set_value_wo(str)
      {
        $("#hidden_tbl_id_wo").val(str); // wo/pi id

        parent.emailwindow.hide();
      }
      var permission='<? echo $permission; ?>';
    </script>

    <div style="width:770px;" >
    <form name="searchjob"  id="searchjob" autocomplete="off">
    	<table width="770" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                <thead>
                    <th width="140">Company</th>
                    <th width="140">Buyer</th>
                    <th width="100">Job No</th>
                    <th width="100">WO Type</th>
                    <th width="80">Is Short</th>
                    <th width="100">WO No</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('searchjob','search_div','','','')"  /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td align="center">
              			<?
                          echo create_drop_down( "cbo_company_name", 135, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", str_replace("'","",$company), "load_drop_down( 'yarn_requisition_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
                        ?>
                        </td>
                        <td align="center" id="buyer_td">
              			<?
                        $blank_array="select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name";
    					//echo create_drop_down( "cbo_buyer_name", 130, $blank_array, 1, "-- Select Buyer --", str_replace("'","",$cbo_buyer_name), "" );
    					echo create_drop_down( "cbo_buyer_name", 135, $blank_array,"id,buyer_name", 1, "-- Select Buyer --",0);
                        ?>
                        </td>
                        <td align="center">
                        <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:75px" />
                        </td>
                        <td align="center">
                        <?
                        echo create_drop_down( "cbo_booking_type", 95, $booking_type,"", 1, "--Select--","","","","","","","2,5,6,7");
                        ?>
                        </td>
                        <td>
                       <?
                            echo create_drop_down( "cbo_is_short", 70, $yes_no,"", 1, "--Select--");
                        ?>
                        </td>
                         <td align="center">
                            <input type="text" name="txt_wo" id="txt_wo" class="text_boxes" style="width:75px" />
                         </td>
                         <td align="center">
                            <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('cbo_booking_type').value+'_'+document.getElementById('cbo_is_short').value+'_'+document.getElementById('txt_wo').value+'_'+'<? echo str_replace("'","",$prev_wo_ids);?>', 'create_job_search_list_view_with_wo', 'search_div', 'yarn_requisition_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:70px;" />
                        </td>
                  </tr>
                </tbody>
        </table>
        <br>
        <div valign="top" id="search_div"> </div>
    </form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
	<?
}


if ($action=="create_job_search_list_view_with_wo")
{
	$data=explode("_",$data);
	$cbo_company_name=str_replace("'","",$data[0]);
	$cbo_buyer_name=str_replace("'","",$data[1]);
	$txt_job_no=str_replace("'","",$data[2]);
	$cbo_booking_type=str_replace("'","",$data[3]);
	$cbo_is_short=str_replace("'","",$data[4]);
	$txt_wo=str_replace("'","",$data[5]);
	$prev_wo_ids=str_replace("'","",$data[6]);

    $prev_wo_no="";
    if ($prev_wo_ids != "")
    {
    	$prev_wo_ids_arr=array_unique(explode(",",$prev_wo_ids));    	
    	foreach($prev_wo_ids_arr as $wo_no)
    	{
    		$prev_wo_no.="'".$wo_no."',";
    	}
    	$prev_wo_no=chop($prev_wo_no,",");
    }
	//echo $prev_wo_no."<br>";//die;
    //$prev_wo_no = '123-fgh';

	//echo $cbo_company_name."**".$txt_style_no."**".$cbo_booking_type."<br>";die;
	$sql_cond=$sql_cond2="";
	if($cbo_company_name!=0) $sql_cond=" and a.company_id='$cbo_company_name'";
	if($cbo_buyer_name!=0) $sql_cond.=" and a.buyer_id='$cbo_buyer_name'";
	if($cbo_booking_type!=0) $sql_cond.=" and a.booking_type like '%$cbo_booking_type%'";

	if($cbo_is_short!=0) $sql_cond.=" and a.is_short like '%$cbo_is_short%'";
	if($txt_job_no!="") $sql_cond.=" and b.job_no like '%$txt_job_no%'";
	if($txt_wo!="") $sql_cond.=" and a.booking_no like '%$txt_wo%'";

	if($cbo_company_name!=0) $sql_cond2=" and a.company_id='$cbo_company_name'";
	if($cbo_buyer_name!=0) $sql_cond2.=" and a.buyer_id='$cbo_buyer_name'";
	if($cbo_booking_type!=0) $sql_cond2.=" and a.booking_type like '%$cbo_booking_type%'";

	if($cbo_is_short!=0) $sql_cond2.=" and a.is_short like '%$cbo_is_short%'";
	if($txt_job_no!="") $sql_cond2.=" and a.id=0";
	if($txt_wo!="") $sql_cond2.=" and a.booking_no like '%$txt_wo%'";


	/*if($db_type==0)
	{
		$sql="select a.id, a.job_no, a.company_id, a.buyer_id,a.booking_type,a.is_short,a.booking_no
		from  wo_booking_mst a,  wo_pre_cost_fab_yarn_cost_dtls b
		where a.booking_type in (1,4,7) and  a.job_no=b.job_no and a.status_active=1 $sql_cond
		group by  a.id, a.job_no, a.company_id, a.buyer_id, a.booking_type,a.is_short,a.booking_no order by a.job_no";
	}
	else if($db_type==2)
	{
		$sql="select a.id, a.job_no, a.company_id, a.buyer_id,a.booking_type,a.is_short,a.booking_no
		from  wo_booking_mst a,  wo_pre_cost_fab_yarn_cost_dtls b
		where a.booking_type in (1,4,7) and a.job_no=b.job_no and a.status_active=1 $sql_cond
		group by a.id, a.job_no,a.company_id, a.buyer_id, a.booking_type,a.is_short,a.booking_no order by a.job_no";
	}*/
	$prev_wo_cond="";
	if($prev_wo_no!="") $prev_wo_cond=" and a.booking_no not in($prev_wo_no)";
    //echo $prev_wo_cond;

	if($db_type==0)
	{
		$sql="select a.id, b.job_no, a.company_id, a.buyer_id,a.booking_type, a.is_short, a.booking_no, 1 as type, 0 as entry_form
		from  wo_booking_mst a, wo_booking_dtls b,  wo_pre_cost_fab_yarn_cost_dtls c
		where a.booking_no=b.booking_no and b.job_no=c.job_no and c.fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.booking_type in (1,4,7) and a.status_active=1 $sql_cond $prev_wo_cond
		group by  a.id, b.job_no, a.company_id, a.buyer_id, a.booking_type, a.is_short, a.booking_no
		union all
		select a.id, '' as job_no, a.company_id, a.buyer_id, a.booking_type, a.is_short, a.booking_no, 2 as type, a.entry_form_id as entry_form
		from  wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b
		where a.booking_no=b.booking_no and a.booking_type in (4) and a.status_active=1 $sql_cond2 $prev_wo_cond
		group by  a.id, a.company_id, a.buyer_id, a.booking_type,a.is_short,a.booking_no,a.entry_form_id";
	}
	else if($db_type==2)
	{
		$sql="select a.id, b.job_no, a.company_id, a.buyer_id,a.booking_type,a.is_short,a.booking_no, 1 as type, 0 as entry_form
		from  wo_booking_mst a, wo_booking_dtls b,  wo_pre_cost_fab_yarn_cost_dtls c
		where a.booking_no=b.booking_no and b.job_no=c.job_no and c.fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.booking_type in (1,4,7) and  a.status_active=1 $sql_cond $prev_wo_cond
		group by a.id, b.job_no,a.company_id, a.buyer_id, a.booking_type, a.is_short, a.booking_no
		union all
		select a.id, null as job_no, a.company_id, a.buyer_id, a.booking_type, a.is_short, a.booking_no, 2 as type, a.entry_form_id as entry_form 
		from  wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b
		where a.booking_no=b.booking_no and a.booking_type in (4) and a.status_active=1 $sql_cond2 $prev_wo_cond
		group by  a.id, a.company_id, a.buyer_id, a.booking_type,a.is_short,a.booking_no,a.entry_form_id";
	}
	//echo $sql;//die;

	?>
	<div style="width:770px;">
	<input type="hidden" id="hidden_tbl_id_wo">
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table">
        <thead>
            <th width="50">SL</th>
            <th width="120">Company</th>
            <th width="120">Buyer</th>
            <th width="120">Job No</th>
            <th width="120" >WO Type</th>
            <th width="70">Is Short</th>
            <th>WO No</th>
        </thead>
	</table>
	<div style="width:770px; overflow-y:scroll; max-height:250px;" id="buyer_list_view" >
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table" id="tbl_list_search">
	<?

	$i=1;
	$nameArray=sql_select($sql);

	foreach ($nameArray as $selectResult)
	{

		$po_number=implode(",",array_unique(explode(",",$selectResult[csf("po_number")])));
		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		?>
		<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value_wo('<? echo $selectResult[csf('booking_no')]; ?>'+','+'<? echo $selectResult[csf('job_no')]; ?>'+','+'<? echo $selectResult[csf('type')]; ?>'+','+'<? echo $selectResult[csf('entry_form')]; ?>'); ">
            <td width="50" align="center"><p> <? echo $i; ?></p></td>
            <td width="120"> <p><? echo $company_arr[$selectResult[csf('company_id')]]; ?></p></td>
            <td width="120"> <p><? echo $buyer_arr[$selectResult[csf("buyer_id")]]; ?></p></td>
            <td width="120"><p><?  echo  $selectResult[csf('job_no')]; ?></p></td>
            <td width="120"> <p><? echo $booking_type[$selectResult[csf('booking_type')]];?></p></td>
            <td width="70"> <p><?
            if($selectResult[csf('is_short')]==1) echo 'Yes';
            if($selectResult[csf('is_short')]==2) echo 'No';
            ?></p></td>
            <td> <p><?  echo $selectResult[csf('booking_no')]; ?></p></td>
		</tr>
		<?
		$i++;
	}
	?>
	</table>
	</div>
	</div>
	<?

}



if($action=="dtls_part_html_row_with_wo")
{
	$data_ex=explode("_",$data);
	$company_id=$data_ex[3];
	$job_no=$data_ex[1];
	$type=$data_ex[4];
	$entry_form=$data_ex[5];

	/*if($job_no!="")
	{

	  $costing_per_id_library=return_library_array( "select job_no, costing_per from wo_pre_cost_mst where job_no ='".$data_ex[1]."'", "job_no", "costing_per");
	  $plan_qty_arr=array();
	  //$po_sql=sql_select("select job_no_mst, sum(plan_cut) as plan_cut from wo_po_break_down where status_active=1 and is_deleted=0 group by job_no_mst");
	  $po_sql=sql_select("select b.job_no_mst, sum(b.plan_cut) as plan_cut from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.job_no_mst ='".$data_ex[1]."' group by b.job_no_mst");
	  foreach($po_sql as $row)
	  {
		$plan_qty_arr[$row[csf('job_no_mst')]]+=$row[csf('plan_cut')];
	  }
	  unset($po_sql);
	  $condition= new condition();
	  $condition->company_name("=$company_id");
	  $condition->job_no("='$job_no'");
	  $condition->init();
	  $yarn= new yarn($condition);
	  $yarn_require_arr=$yarn->get_By_Precostdtlsid_YarnQtyArray();
	}*/

	//$variable_page_setting=return_field_value("bom_page_setting","variable_order_tracking","company_name=$company_id and variable_list=50","bom_page_setting");


	//$sql="select a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, a.total_set_qnty as ratio, b.plan_cut, c.count_id, c.copm_one_id, c.percent_one, c.type_id, sum(c.cons_qnty) as cons_qnty, (sum(rate)/count(rate)) as rate_ratio from  wo_po_details_master a, wo_po_break_down b, wo_pre_cost_fab_yarn_cost_dtls c where a.job_no=b.job_no_mst and a.job_no=c.job_no and a.id=$data_ex[0] and a.status_active=1 group by a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,a.total_set_qnty,b.plan_cut,c.count_id,c.copm_one_id,c.percent_one,c.type_id";


	/*  $sql="select a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, a.total_set_qnty as ratio,a.job_quantity, c.count_id, c.copm_one_id, c.percent_one, c.type_id, sum(c.cons_qnty) as cons_qnty, (sum(rate)/count(rate)) as rate_ratio from  wo_booking_dtls a, wo_pre_cost_fab_yarn_cost_dtls c where a.job_no=c.job_no and a.id=$data_ex[0] and a.status_active=1 group by a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,a.total_set_qnty,a.job_quantity,c.count_id,c.copm_one_id,c.percent_one,c.type_id";*/

	  /*$sql="select a.id,a.booking_no, a.job_no,a.grey_fab_qnty, c.count_id, c.copm_one_id, c.percent_one,c.percent_two, c.type_id, sum(a.grey_fab_qnty) as grey_fab_qnty, (sum(a.rate)/count(a.rate)) as rate_ratio,p.company_name, p.buyer_name, p.style_ref_no, p.total_set_qnty as ratio,p.job_quantity
	  from wo_po_details_master p, wo_booking_dtls a, wo_pre_cost_fab_yarn_cost_dtls c
	  where p.job_no=a.job_no and a.job_no=c.job_no and c.id=pre_cost_fabric_cost_dtls_id and a.booking_no='$data_ex[0]' and a.status_active=1
	  group by a.id,a.booking_no,a.grey_fab_qnty, a.job_no, c.count_id, c.copm_one_id, c.percent_one,c.percent_two, c.type_id,p.company_name, p.buyer_name, p.style_ref_no, p.total_set_qnty,p.job_quantity,'grey_fab_qnty'";*/

	  /*$sql_prev="select b.booking_no,b.yarn_type_id,sum(b.quantity) as quantity from  inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b where a.id=b.mst_id and b.booking_no='".$data_ex[0]."'  and a.entry_form=70 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.booking_no,b.yarn_type_id";

	  $sql_result_recv=sql_select($sql_prev);
	  foreach($sql_result_recv as $row)
	  {
		 $yarn_prev_recv_arr[$row[csf('booking_no')]][$row[csf('yarn_type_id')]]['qty']=$row[csf('quantity')];
	  }*/

	$prev_req_qnty=sql_select("select b.booking_no, b.count_id, b.composition_id, b.com_percent, b.yarn_type_id, b.quantity
	from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b
	where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.entry_form=70 and b.booking_no ='".$data_ex[0]."'");
	$prev_req_data=array();
	foreach($prev_req_qnty as $row)
	{
		$yarn_prev_recv_arr[$row[csf("booking_no")]][$row[csf("count_id")]][$row[csf("composition_id")]][$row[csf("com_percent")]][$row[csf("yarn_type_id")]]+=$row[csf("quantity")];
	}
	//echo $type;
	if($type==1) //### with order
	{
		$sql="select a.booking_no, a.job_no, c.count_id, c.rate, p.company_name, c.copm_one_id, c.percent_one, c.type_id,  p.buyer_name, p.style_ref_no, p.style_ref_no, p.total_set_qnty as ratio, p.job_quantity, sum(((a.grey_fab_qnty*c.cons_ratio)/100)) as grey_fab_qnty
		from wo_po_details_master p, wo_booking_dtls a, wo_pre_cost_fab_yarn_cost_dtls c
		where p.job_no=a.job_no and a.job_no=c.job_no and c.fabric_cost_dtls_id=a.pre_cost_fabric_cost_dtls_id and a.booking_no='$data_ex[0]' and a.status_active=1
		group by p.company_name,p.buyer_name,c.rate, a.booking_no,p.style_ref_no, a.job_no, c.count_id, c.copm_one_id, c.percent_one, c.type_id,  p.buyer_name, p.style_ref_no, p.total_set_qnty,p.job_quantity";
	}
	else  //### without order
	{
		//$sql="select a.booking_no, c.count_id as count_id, a.rate, p.company_id as company_name, p.buyer_id as buyer_name, c.copmposition_id as copm_one_id, c.percent as percent_one, c.type_id as type_id, a.grey_fabric as grey_fab_qnty		from wo_non_ord_samp_booking_mst p, wo_non_ord_samp_booking_dtls a, lib_yarn_count_determina_mst b, lib_yarn_count_determina_dtls c		where p.booking_no=a.booking_no and a.lib_yarn_count_deter_id=b.id and b.id=c.mst_id and a.booking_no='$data_ex[0]' and a.status_active=1";
		if($entry_form==140)
		{
			$sql="select a.booking_no, max(a.id) as booking_dtls_id, avg(a.rate) as rate, p.company_id as company_name, p.buyer_id as buyer_name, b.id as yarn_dtls_id, b.count_id as count_id, b.copm_one_id, b.cons_ratio as percent_one, b.type_id as type_id, b.cons_qnty as grey_fab_qnty
			from wo_non_ord_samp_booking_mst p, wo_non_ord_samp_booking_dtls a, sample_development_yarn_dtls b
			where p.booking_no=a.booking_no and a.booking_no=b.booking_no and a.lib_yarn_count_deter_id=b.determin_id and a.booking_no='$data_ex[0]' and a.status_active=1 and b.status_active=1
			group by a.booking_no, p.company_id, p.buyer_id, b.id, b.count_id, b.copm_one_id, b.cons_ratio, b.type_id, b.cons_qnty";
		}
		else
		{
			$sql="select a.booking_no, a.id as booking_dtls_id, a.rate, p.company_id as company_name, p.buyer_id as buyer_name, b.id as yarn_dtls_id, b.count_id as count_id, b.copm_one_id, b.cons_ratio as percent_one, b.type_id as type_id, b.cons_qnty as grey_fab_qnty
			from wo_non_ord_samp_booking_mst p, wo_non_ord_samp_booking_dtls a, wo_non_ord_samp_yarn_dtls b
			where p.booking_no=a.booking_no and a.id=b.wo_non_ord_samp_book_dtls_id and a.booking_no='$data_ex[0]' and a.status_active=1 and b.status_active=1";
		}
		
	}

	//echo $sql;die;
	$sql_result=sql_select($sql);
	$i=$data_ex[2];
	$k=1;
    //print_r($sql_result);
    /* wo_pre_cost_fabric_cost_dtls*/
        //die;
	foreach($sql_result as $row)
	{

		/*if($variable_page_setting==2)
		{
		$cons_qnty=$yarn_require_arr[$row[csf('dtls_id')]];
		}
		else
		{
		$dzn_qnty=0; $cons_qnty=0; $cons_balance_qnty=0;
		if($costing_per_id_library[$row[csf('job_no')]]==1) $dzn_qnty=12;
		else i
		else if($costing_per_id_library[$row[csf('job_no')]]==4) $dzn_qnty=12*3;
		else if($costing_per_id_library[$row[csf('job_no')]]==5) $dzn_qnty=12*4;
		else $dzn_qnty=1;

		$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
		$plan_cut_qnty=$plan_qty_arr[$row[csf('job_no')]]*$row[csf('ratio')];
		$cons_qnty=$plan_cut_qnty*($row[csf('grey_fab_qnty')]/$dzn_qnty);
		}*/

		//$prev_recv_qty=$yarn_prev_recv_arr[$row[csf('booking_no')]][$row[csf('type_id')]]['qty'];

		$prev_recv_qty=$yarn_prev_recv_arr[$row[csf("booking_no")]][$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]];
		$balance_qty=$row[csf("grey_fab_qnty")]-$prev_recv_qty;
		?>
		<tr class="general" id="tr_<? echo $i; ?>" >
            <td align="center" >
            <input type="text" name="txtjobno_<? echo $i; ?>" id="txtjobno_<? echo $i; ?>" class="text_boxes" style="width:75px;"  value="<? echo $row[csf("job_no")]; ?>" readonly disabled />
            <input type="hidden" id="txtjobid_<? echo $i; ?>" name="txtjobid_<? echo $i; ?>" style="width:100px;" value="<? echo $row[csf("id")]; ?>">
            <input type="hidden" name="txtrowid_<? echo $i; ?>" id="txtrowid_<? echo $i; ?>" class="text_boxes" value="" style="width:70px;" />
            <input type="hidden" name="hiderow_<? echo $i; ?>" id="hiderow_<? echo $i; ?>" class="text_boxes" value="0" style="width:70px;" />
            </td>
            <td>
            <input type="text" name="txtwono_<? echo $i; ?>" id="txtwono_<? echo $i; ?>" class="text_boxes"  style="width:75px;" value="<? echo $row[csf("booking_no")]?>"  readonly />
            <input type="hidden" id="txtwoid_<? echo $i; ?>" name="txtwoid_<? echo $i; ?>" value="<? echo $row[csf("id")]?>" style="width:100px;">
            </td>
            <td>
            <?
            //select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$data[0] $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name
            echo create_drop_down( "cbobuyername_".$i, 90, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='".$row[csf("company_name")]."' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select --", $row[csf("buyer_name")], "",1 );
            ?>
            </td>
            <td align="center"><input type="text" name="txtstyleno_<? echo $i; ?>" id="txtstyleno_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf("style_ref_no")]; ?>" style="width:75px;" readonly disabled /></td>
            <td align="center"><input type="text" name="txtyarncolor[]" id="txtyarncolor_<? echo $i; ?>" class="text_boxes" onFocus="add_auto_complete( <? echo $i; ?> )" value="GREY" style="width:75px;" /></td>
            <td align="center">
            <?
            echo create_drop_down( "cbocount_".$i, 70, "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count",1,"-- Select --", $row[csf("count_id")], "",1,"" );
            ?>
            </td>
            <td align="center">
            <?
            echo create_drop_down( "cbocompone_".$i, 100, $composition,"", 1, "-- Select --", $row[csf("copm_one_id")], "",1,"","","",$ommitComposition );
            ?>
            </td>
            <td><input type="text" name="txtpacent_<? echo $i; ?>" id="txtpacent_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf("percent_one")]; ?>" style="width:40px;" disabled/></td>
            <td>
            <?
            echo create_drop_down( "cbotype_".$i, 100, $yarn_type,"", 1, "-- Select --", $row[csf("type_id")], "",1,"","","",$ommitYarnType );
            ?>
            </td>
            <td>
            <?
            echo create_drop_down( "cbouom_".$i, 60, $unit_of_measurement,"", 1, "-- Select--", 12, "",1 );
            ?>
            </td>
            <td title="<? echo number_format($row[csf("grey_fab_qnty")],4,'.',''); ?>">
            <input type="text" id="reqqnty_<? echo $i; ?>" name="reqqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:55px" value="<? echo number_format($balance_qty,4,'.',''); ?>" onKeyUp="calculate_amount(<? echo $i; ?>)" />
            <input type="hidden" id="hiddenreqqnty_<? echo $i; ?>" name="hiddenreqqnty_<? echo $i; ?>" value="<? echo number_format($balance_qty,4,'.',''); ?>" />
            </td>
            <td>
            <input type="text" name="txtrate_<? echo $i; ?>" id="txtrate_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo number_format($row[csf("rate")],4,'.',''); ?>"  style="width:40px;" onKeyUp="calculate_amount(<? echo $i; ?>)" />
            <input type="hidden" name="hiddentxtrate_<? echo $i; ?>" id="hiddentxtrate_<? echo $i; ?>" value="<? echo number_format($row[csf("rate")],4,'.',''); ?>" />
            </td>
            <td><input type="text" name="txtamount_<? echo $i; ?>" id="txtamount_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo number_format($row[csf("grey_fab_qnty")]*1*($row[csf("rate")]*1),4,'.',''); ?>" style="width:50px;" readonly /></td>
            <td><input class="datepicker" type="text" style="width:65px;" name="txtyarndate_<? echo $i; ?>" id="txtyarndate_<? echo $i; ?>" placeholder="Select Date" /></td>
            <td><input type="text" name="txtremarks_<? echo $i; ?>" id="txtremarks_<? echo $i; ?>" class="text_boxes" value="" style="width:110px;" /></td>
            <td><input type="button" id="decrease_<? echo $i; ?>" name="decrease_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deletebreak_down_tr(<? echo $i; ?>);" /></td>
		</tr>
		<?
		$i++;
	}
	?>

    <tr class="general" id="tr_<? echo $i; ?>">
        <td align="center">
        <input type="text" name="txtjobno_<? echo $i; ?>" id="txtjobno_<? echo $i; ?>" class="text_boxes" value="" style="width:75px;" readonly  disabled />
        <input type="hidden" id="txtjobid_<? echo $i; ?>" name="txtjobid_<? echo $i; ?>" style="width:100px;">
        <input type="hidden" name="txtrowid_<? echo $i; ?>" id="txtrowid_<? echo $i; ?>" class="text_boxes" value="" style="width:70px;" />
        <input type="hidden" name="hiderow_<? echo $i; ?>" id="hiderow_<? echo $i; ?>" class="text_boxes" value="1" style="width:70px;" />
        </td>
        <td>
        <input type="text" name="txtwono_<? echo $i; ?>" id="txtwono_<? echo $i; ?>" class="text_boxes" value="" style="width:75px;" onDblClick="openmypage_wo(<? echo $i; ?>)" placeholder="Double Click For WO" readonly />
        <input type="hidden" id="txtwoid_<? echo $i; ?>" name="txtwoid_<? echo $i; ?>" style="width:100px;">
        </td>
        <td>
        <?
            echo create_drop_down( "cbobuyername_".$i, 90, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='".$company_name."' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select --", 0, "" );
        ?>
        </td>
        <td align="center"><input type="text" name="txtstyleno_<? echo $i; ?>" id="txtstyleno_<? echo $i; ?>" class="text_boxes" value="" style="width:75px;" /></td>
        <td align="center"><input type="text" name="txtyarncolor[]" id="txtyarncolor_<? echo $i; ?>" class="text_boxes" value="GREY" onFocus="add_auto_complete( <? echo $i; ?> )" style="width:75px;"/></td>
        <td align="center">
        <?
            echo create_drop_down( "cbocount_".$i, 70, "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count",1,"-- Select --", 0, "",0,"" );
        ?>
        </td>
        <td align="center">
        <?
            echo create_drop_down( "cbocompone_".$i, 100, $composition,"", 1, "-- Select --", 0, "",0,"","","",$ommitComposition );
        ?>
        </td>
        <td><input type="text" name="txtpacent_<? echo $i; ?>" id="txtpacent_<? echo $i; ?>" class="text_boxes" value="" style="width:40px;" /></td>
        <td>
        <?
            echo create_drop_down( "cbotype_".$i, 100, $yarn_type,"", 1, "-- Select --", $row[csf("type_id")], "",$disabled,"","","",$ommitYarnType );
        ?>
        </td>
        <td>
        <?
            echo create_drop_down( "cbouom_".$i, 60, $unit_of_measurement,"", 1, "-- Select--", 12, "",1 );
        ?>
        </td>
        <td title="<? echo 'Previous Recv-'.$prev_recv_qty;?>">
            <input type="text" id="reqqnty_<? echo $i; ?>" name="reqqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:55px" onKeyUp="calculate_amount(<? echo $i; ?>)" />
            <input type="hidden" id="hiddenreqqnty_<? echo $i; ?>" name="hiddenreqqnty_<? echo $i; ?>" value=""  />
        </td>
        <td>
            <input type="text" name="txtrate_<? echo $i; ?>" id="txtrate_<? echo $i; ?>" class="text_boxes_numeric" value="" style="width:40px;" onKeyUp="calculate_amount(<? echo $i; ?>)" />
            <input type="hidden" name="hiddentxtrate_<? echo $i; ?>" id="hiddentxtrate_<? echo $i; ?>" value="" />
        </td>
        <td><input type="text" name="txtamount_<? echo $i; ?>" id="txtamount_<? echo $i; ?>" class="text_boxes_numeric" value="" style="width:50px;" readonly /></td>
        <td><input class="datepicker" type="text" style="width:65px;" name="txtyarndate_<? echo $i; ?>" id="txtyarndate_<? echo $i; ?>" placeholder="Select Date" /></td>
        <td><input type="text" name="txtremarks_<? echo $i; ?>" id="txtremarks_<? echo $i; ?>" class="text_boxes" value="" style="width:110px;" /></td>
        <td><input type="button" id="decrease_<? echo $i; ?>" name="decrease_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deletebreak_down_tr(<? echo $i; ?>);" /></td>
    </tr>
   <?

}

if($action=="dtls_part_html_row")
{

	$data_ex=explode("_",$data);
	$job_no=$data_ex[1];

	$variable_page_setting=return_field_value("bom_page_setting","variable_order_tracking","company_name=$data_ex[3] and variable_list=50","bom_page_setting");

	if($variable_page_setting==2)
	{
		$condition= new condition();
		$condition->company_name("=$data_ex[3]");
		$condition->job_no("='$job_no'");
		// echo "jahid";die;
		$condition->init();
		$yarn= new yarn($condition);
		//echo $yarn->getQuery();
		//die;
		$yarn_require_arr=$yarn->getJobCountCompositionPercentAndTypeWiseYarnQtyArray();
	}
	else
	{
		$costing_per_id_library=return_library_array( "select job_no, costing_per from wo_pre_cost_mst where job_no ='".$data_ex[1]."'", "job_no", "costing_per");
		$plan_qty_arr=array();
		$po_sql=sql_select("select job_no_mst, sum(plan_cut) as plan_cut from wo_po_break_down where status_active=1 and is_deleted=0 and job_no_mst='$job_no' group by job_no_mst");
		foreach($po_sql as $row)
		{
			$plan_qty_arr[$row[csf('job_no_mst')]]+=$row[csf('plan_cut')];
		}
		unset($po_sql);
	}

	//echo "<pre>";
	//print_r($yarn_require_arr);
	//die;
	//$sql="select a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, a.total_set_qnty as ratio, b.plan_cut, c.count_id, c.copm_one_id, c.percent_one, c.type_id, sum(c.cons_qnty) as cons_qnty, (sum(rate)/count(rate)) as rate_ratio from  wo_po_details_master a, wo_po_break_down b, wo_pre_cost_fab_yarn_cost_dtls c where a.job_no=b.job_no_mst and a.job_no=c.job_no and a.id=$data_ex[0] and a.status_active=1 group by a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,a.total_set_qnty,b.plan_cut,c.count_id,c.copm_one_id,c.percent_one,c.type_id";
	$prev_req_qnty=sql_select("select b.job_no, b.count_id, b.composition_id, b.com_percent, b.yarn_type_id, b.quantity
	from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b
	where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.entry_form=70 and b.job_no='$job_no'");
	$prev_req_data=array();
	foreach($prev_req_qnty as $row)
	{
		$prev_req_data[$row[csf("job_no")]][$row[csf("count_id")]][$row[csf("composition_id")]][$row[csf("com_percent")]][$row[csf("yarn_type_id")]]+=$row[csf("quantity")];
	}


	$sql="select a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, a.total_set_qnty as ratio,a.job_quantity, c.count_id, c.copm_one_id, c.percent_one, c.type_id, sum(c.cons_qnty) as cons_qnty, (sum(rate)/count(rate)) as rate_ratio
	from  wo_po_details_master a, wo_pre_cost_fab_yarn_cost_dtls c
	where a.job_no=c.job_no and a.id=$data_ex[0] and a.status_active=1
	group by a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, a.total_set_qnty,a.job_quantity, c.count_id, c.copm_one_id, c.percent_one, c.type_id";
	//  echo $sql;die;
	$sql_result=sql_select($sql);
	$i=$data_ex[2];
	$k=1;

	foreach($sql_result as $row)
	{
		if($variable_page_setting==2)
		{
			$cons_qnty=$yarn_require_arr[$job_no][$row[csf('count_id')]][$row[csf('copm_one_id')]][$row[csf('percent_one')]][$row[csf('type_id')]]-$prev_req_data[$job_no][$row[csf('count_id')]][$row[csf('copm_one_id')]][$row[csf('percent_one')]][$row[csf('type_id')]];
		}
		else
		{
			$dzn_qnty=0; $cons_qnty=0; $cons_balance_qnty=0;
			if($costing_per_id_library[$row[csf('job_no')]]==1) $dzn_qnty=12;
			else if($costing_per_id_library[$row[csf('job_no')]]==3) $dzn_qnty=12*2;
			else if($costing_per_id_library[$row[csf('job_no')]]==4) $dzn_qnty=12*3;
			else if($costing_per_id_library[$row[csf('job_no')]]==5) $dzn_qnty=12*4;
			else $dzn_qnty=1;
			$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
			$plan_cut_qnty=$plan_qty_arr[$row[csf('job_no')]]*$row[csf('ratio')];
			$cons_qnty=$plan_cut_qnty*($row[csf('cons_qnty')]/$dzn_qnty);
			$prev_req_qnty=$prev_req_data[$job_no][$row[csf('count_id')]][$row[csf('copm_one_id')]][$row[csf('percent_one')]][$row[csf('type_id')]];
			$cons_qnty=$cons_qnty-$prev_req_qnty;
		}

		//echo $dzn_qnty.jahid;die;
		$company_name=$row[csf("company_name")];
		?>
		<tr class="general" id="tr_<? echo $i; ?>" >
            <td align="center">
            <input type="text" name="txtjobno_<? echo $i; ?>" id="txtjobno_<? echo $i; ?>" class="text_boxes" style="width:75px;"  value="<? echo $row[csf("job_no")]; ?>" readonly />
            <input type="hidden" id="txtjobid_<? echo $i; ?>" name="txtjobid_<? echo $i; ?>" style="width:100px;" value="<? echo $row[csf("id")]; ?>">
            <input type="hidden" name="txtrowid_<? echo $i; ?>" id="txtrowid_<? echo $i; ?>" class="text_boxes" value="" style="width:70px;" />
            <input type="hidden" name="hiderow_<? echo $i; ?>" id="hiderow_<? echo $i; ?>" class="text_boxes" value="0" style="width:70px;" />
            </td>
            <td>
            <input type="text" name="txtwono_<? echo $i; ?>" id="txtwono_<? echo $i; ?>" class="text_boxes" value="" style="width:75px;"  readonly disabled />
            <input type="hidden" id="txtwoid_<? echo $i; ?>" name="txtwoid_<? echo $i; ?>" style="width:100px;">

            <input type="hidden" name="hiderow_<? echo $i; ?>" id="hiderow_<? echo $i; ?>" class="text_boxes" value="0" style="width:70px;" />
            </td>
            <td>
            <?
            //select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$data[0] $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name
            echo create_drop_down( "cbobuyername_".$i, 90, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='".$row[csf("company_name")]."' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select --", $row[csf("buyer_name")], "",1 );
            ?>
            </td>
            <td align="center"><input type="text" name="txtstyleno_<? echo $i; ?>" id="txtstyleno_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf("style_ref_no")]; ?>" style="width:75px;" readonly disabled /></td>
            <td align="center"><input type="text" name="txtyarncolor[]" id="txtyarncolor_<? echo $i; ?>" class="text_boxes" onFocus="add_auto_complete( <? echo $i; ?> )" value="GREY" style="width:75px;" /></td>
            <td align="center">
            <?
            echo create_drop_down( "cbocount_".$i, 70, "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count",1,"-- Select --", $row[csf("count_id")], "",0,"" );
            ?>
            </td>
            <td align="center">
            <?
            echo create_drop_down( "cbocompone_".$i, 100, $composition,"", 1, "-- Select --", $row[csf("copm_one_id")], "",0,"","","",$ommitComposition );
            ?>
            </td>
            <td>
            <? $percent_one = ($row[csf("percent_one")])? $row[csf("percent_one")]: "100";?>
            <input type="text" name="txtpacent_<? echo $i; ?>" id="txtpacent_<? echo $i; ?>" class="text_boxes" value="<? echo $percent_one; ?>" style="width:40px;"/>
            </td>
            <td>
            <?
            echo create_drop_down( "cbotype_".$i, 100, $yarn_type,"", 1, "-- Select --", $row[csf("type_id")], "",0,"","","",$ommitYarnType );
            ?>
            </td>
            <td>
            <?
            echo create_drop_down( "cbouom_".$i, 60, $unit_of_measurement,"", 1, "-- Select--", 12, "",1 );
            ?>
            </td>
            <td>
            <input type="text" id="reqqnty_<? echo $i; ?>" name="reqqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:55px" value="<? echo number_format($cons_qnty,4,'.',''); ?>" onKeyUp="calculate_amount(<? echo $i; ?>)" />
            <input type="hidden" id="hiddenreqqnty_<? echo $i; ?>" name="hiddenreqqnty_<? echo $i; ?>" value="<? echo number_format($cons_qnty,4,'.',''); ?>"  />
            </td>
            <td><input type="text" name="txtrate_<? echo $i; ?>" id="txtrate_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo number_format($row[csf("rate_ratio")],4,'.',''); ?>"  style="width:40px;" onKeyUp="calculate_amount(<? echo $i; ?>)" />
            <input type="hidden" name="hiddentxtrate_<? echo $i; ?>" id="hiddentxtrate_<? echo $i; ?>" value="<? echo number_format($row[csf("rate_ratio")],4,'.',''); ?>" /></td>
            <td><input type="text" name="txtamount_<? echo $i; ?>" id="txtamount_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo number_format(($cons_qnty*($row[csf("rate_ratio")]*1)),4,'.',''); ?>" style="width:50px;" readonly /></td>
            <td><input class="datepicker" type="text" style="width:65px;" name="txtyarndate_<? echo $i; ?>" id="txtyarndate_<? echo $i; ?>" placeholder="Select Date" /></td>
            <td><input type="text" name="txtremarks_<? echo $i; ?>" id="txtremarks_<? echo $i; ?>" class="text_boxes" value="" style="width:110px;" /></td>
            <td><input type="button" id="decrease_<? echo $i; ?>" name="decrease_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deletebreak_down_tr(<? echo $i; ?>);" /></td>
		</tr>
		<?
		$i++;
	}
	?>
	<tr class="general" id="tr_<? echo $i; ?>">
	<td align="center">
	<input type="text" name="txtjobno_<? echo $i; ?>" id="txtjobno_<? echo $i; ?>" class="text_boxes" value="" style="width:75px;" onDblClick="openmypage_job(<? echo $i; ?>)" placeholder="Doble Click For Job" readonly />
	<input type="hidden" id="txtjobid_<? echo $i; ?>" name="txtjobid_<? echo $i; ?>" style="width:100px;">
	<input type="hidden" name="txtrowid_<? echo $i; ?>" id="txtrowid_<? echo $i; ?>" class="text_boxes" value="" style="width:70px;" />
	<input type="hidden" name="hiderow_<? echo $i; ?>" id="hiderow_<? echo $i; ?>" class="text_boxes" value="0" style="width:70px;" />
	</td>
	<td>
	<input type="text" name="txtwono_<? echo $i; ?>" id="txtwono_<? echo $i; ?>" class="text_boxes" value="" style="width:75px;" onDblClick="openmypage_wo(1)" readonly disabled />
	<input type="hidden" id="txtwoid_<? echo $i; ?>" name="txtwoid_<? echo $i; ?>" style="width:100px;">
	<input type="hidden" name="txtwoid_<? echo $i; ?>" id="txtwoid_<? echo $i; ?>" class="text_boxes" value="" style="width:70px;" />
	<input type="hidden" name="hiderow_<? echo $i; ?>" id="hiderow_<? echo $i; ?>" class="text_boxes" value="0" style="width:70px;" />
	</td>
	<td>
	<?
	echo create_drop_down( "cbobuyername_".$i, 90, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='".$company_name."' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select --", 0, "" );
	?>
	</td>
	<td align="center"><input type="text" name="txtstyleno_<? echo $i; ?>" id="txtstyleno_<? echo $i; ?>" class="text_boxes" value="" style="width:75px;" /></td>
	<td align="center"><input type="text" name="txtyarncolor[]" id="txtyarncolor_<? echo $i; ?>" class="text_boxes" value="GREY" onFocus="add_auto_complete( <? echo $i; ?> )" style="width:75px;"/></td>
	<td align="center">
	<?
	echo create_drop_down( "cbocount_".$i, 70, "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count",1,"-- Select --", 0, "",0,"" );
	?>
	</td>
	<td align="center">
	<?
	echo create_drop_down( "cbocompone_".$i, 100, $composition,"", 1, "-- Select --", 0, "",0,"","","",$ommitComposition );
	?>
	</td>
	<td><input type="text" name="txtpacent_<? echo $i; ?>" id="txtpacent_<? echo $i; ?>" class="text_boxes" value="100" style="width:40px;" /></td>
	<td>
	<?
	echo create_drop_down( "cbotype_".$i, 100, $yarn_type,"", 1, "-- Select --", $row[csf("type_id")], "",$disabled,"","","",$ommitYarnType );
	?>
	</td>
	<td>
	<?
	echo create_drop_down( "cbouom_".$i, 60, $unit_of_measurement,"", 1, "-- Select--", 12, "",1 );
	?>
	</td>
	<td>
	<input type="text" id="reqqnty_<? echo $i; ?>" name="reqqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:55px" onKeyUp="calculate_amount(<? echo $i; ?>)" />
	<input type="hidden" id="hiddenreqqnty_<? echo $i; ?>" name="hiddenreqqnty_<? echo $i; ?>" value=""  />
	</td>
	<td><input type="text" name="txtrate_<? echo $i; ?>" id="txtrate_<? echo $i; ?>" class="text_boxes_numeric" value="" style="width:40px;" onKeyUp="calculate_amount(<? echo $i; ?>)" /></td>
	<td><input type="text" name="txtamount_<? echo $i; ?>" id="txtamount_<? echo $i; ?>" class="text_boxes_numeric" value="" style="width:50px;" readonly /></td>
	<td><input class="datepicker" type="text" style="width:65px;" name="txtyarndate_<? echo $i; ?>" id="txtyarndate_<? echo $i; ?>" placeholder="Select Date" /></td>
	<td><input type="text" name="txtremarks_<? echo $i; ?>" id="txtremarks_<? echo $i; ?>" class="text_boxes" value="" style="width:110px;" /></td>
	<td><input type="button" id="decrease_<? echo $i; ?>" name="decrease_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deletebreak_down_tr(<? echo $i; ?>);" /></td>
	</tr>
	<?
}

if($action=="save_update_delete")
{
	$process = array( &$_POST );
	//print_r($process);die;
	extract(check_magic_quote_gpc( $process ));
	$update_id=str_replace("'","",$update_id);
	$tot_row=str_replace("'","",$tot_row);
	
	//echo "10**".$update_id;die;
	if ($operation==0)  // Insert Here=======================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; die;}
		
		if($db_type==0) $select_year="YEAR(insert_date)"; else if($db_type==2) $select_year="TO_CHAR(insert_date,'YYYY')";
		if($update_id=="")
		{
			$new_requ_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '','RQSN', date("Y",time()), 5, "select requ_no_prefix,requ_prefix_num from inv_purchase_requisition_mst where company_id=$cbo_company_name and $select_year=".date('Y',time())." and entry_form=70 order by id desc ", "requ_no_prefix", "requ_prefix_num" ));
			
			$id=return_next_id("id","inv_purchase_requisition_mst",1);
			$field_array="id,entry_form,requ_no,requ_no_prefix,requ_prefix_num,company_id,item_category_id,supplier_id,delivery_date,pay_mode,requisition_date,cbo_currency,source,do_no,dealing_marchant,attention,remarks,ready_to_approve,basis,inserted_by,insert_date,status_active,is_deleted";
			$data_array="(".$id.",70,'".$new_requ_no[0]."','".$new_requ_no[1]."',".$new_requ_no[2].",".$cbo_company_name.",".$cbo_item_category.",".$cbo_supplier.",".$txt_delivery_date.",".$cbo_pay_mode.",".$txt_wo_date.",".$cbo_currency.",".$cbo_source.",".$txt_do_no.",".$txt_deal_march.",".$txt_attention.",".$txt_remarks.",".$cbo_ready_to_approved.",".$cbo_basis.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		}
		else
		{
			$id=$update_id;
			$field_array="supplier_id*delivery_date*pay_mode*requisition_date*cbo_currency*source*do_no*dealing_marchant*attention*remarks*ready_to_approve*updated_by*update_date";
			$data_array="".$cbo_supplier."*".$txt_delivery_date."*".$cbo_pay_mode."*".$txt_wo_date."*".$cbo_currency."*".$cbo_source."*".$txt_do_no."*".$txt_deal_march."*".$txt_attention."*".$txt_remarks."*".$cbo_ready_to_approved."*".$user_id."*'".$pc_date_time."'";
		}
		
		$dtls_id=return_next_id( "id", "inv_purchase_requisition_dtls",1);
		$field_array_dlls ="id,mst_id,item_category,job_id,job_no,booking_no,booking_id,buyer_id,style_ref_no,color_id,count_id,composition_id,com_percent,yarn_type_id,cons_uom,quantity,rate,amount,yarn_inhouse_date,remarks,inserted_by,insert_date,status_active,is_deleted";
		//$i=1;
		
		
		$test_data=0;
		for($i=1; $i<=$total_row; $i++)
		{
			$txtjobid="txtjobid_".$i;
			$txtjobno="txtjobno_".$i;
			$txtbookingno="txtwono_".$i;
			$txtwoid="txtwoid_".$i;
			$cbobuyername="cbobuyername_".$i;
			$txtstyleno="txtstyleno_".$i;
			$txtyarncolor="txtyarncolor_".$i;
			$cbocount="cbocount_".$i;
			$cbocompone="cbocompone_".$i;
			$txtpacent="txtpacent_".$i;
			$cbotype="cbotype_".$i;
			$cbouom="cbouom_".$i;
			$reqqnty="reqqnty_".$i;
			$txtrate="txtrate_".$i;
			$txtamount="txtamount_".$i;
			$txtyarndate="txtyarndate_".$i;
			$txtremarks="txtremarks_".$i;
			$txtrowid="txtrowid_".$i;
			$hiderow="hiderow_".$i;
			
			//$color_id=return_id( $$txtyarncolor, $color_arr, "lib_color", "id,color_name");
			
			if(str_replace("'","",$$txtyarncolor)!="")
			{
				if (!in_array(str_replace("'","",$$txtyarncolor),$new_array_color))
				{
					$color_id = return_id( str_replace("'","",$$txtyarncolor), $color_arr, "lib_color", "id,color_name","70");
					$new_array_color[$color_id]=str_replace("'","",$$txtyarncolor);
				}
				else $color_id =  array_search(str_replace("'","",$$txtyarncolor), $new_array_color);
			}
			else
			{
				$color_id=0;
			}
			
			if(str_replace("'","",$$hiderow)!=1)
			{
				if(str_replace("'","",$$reqqnty)>0)
				{
					if ($data_array_dtls!="") $data_array_dtls .=",";
					$data_array_dtls .="(".$dtls_id.",".$id.",".$cbo_item_category.",".$$txtjobid.",".$$txtjobno.",".$$txtbookingno.",".$$txtwoid.",".$$cbobuyername.",".$$txtstyleno.",".$color_id.",".$$cbocount.",".$$cbocompone.",".$$txtpacent.",".$$cbotype.",".$$cbouom.",".$$reqqnty.",".$$txtrate.",".$$txtamount.",".$$txtyarndate.",".$$txtremarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$dtls_id=$dtls_id+1;
				}
			}
		}
		
		//echo "10**".($data_array_dtls);die;
		//  die;
		//check_table_status( $_SESSION['menu_id'],0);
		if($update_id=="")
		{
			$rID=sql_insert("inv_purchase_requisition_mst",$field_array,$data_array,0);
		}
		else
		{
			$rID=sql_update("inv_purchase_requisition_mst",$field_array,$data_array,"id",$id,0);
		}
		//print_r($data_array_dtls);
		//die;
		$dtls_rId=sql_insert("inv_purchase_requisition_dtls",$field_array_dlls,$data_array_dtls,0);
		
		//echo "10** insert into inv_purchase_requisition_dtls ($field_array_dlls) values $data_array_dtls";die;
		//echo "10**".$rID."**".$dtls_rId;die;
		
		// echo print_r($dtls_rId);die;
		if($db_type==0)
		{
			if($rID && $dtls_rId)
			{
				mysql_query("COMMIT");
				echo "0**".str_replace("'", '', $new_requ_no[0])."**".$id;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".$id;
			}
		}
		else if($db_type==2)
		{
			if($rID && $dtls_rId)
			{
				oci_commit($con);
				echo "0**".str_replace("'", '', $new_requ_no[0])."**".$id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$id;
			}
		}
		disconnect($con);
		die;
	}
	
	else if ($operation==1)
	{
		$con = connect();
		$update_id=str_replace("'","",$update_id);
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if($update_id=="")
		{
			echo "10";die;
		}
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; die;}
		
		//$update_id
		$approved_status=return_field_value("is_approved","inv_purchase_requisition_mst","id=$update_id","is_approved");
		if($approved_status>0)
		{
			echo "20**This Requisition is Approved. So Change Not Allowed";die;
		}
		
		$id=$update_id;
		$req_mst_no=str_replace("'","",$txt_wo_number);
		$field_array="supplier_id*delivery_date*pay_mode*requisition_date*cbo_currency*source*do_no*dealing_marchant*attention*remarks*ready_to_approve*updated_by*update_date";
		$data_array="".$cbo_supplier."*".$txt_delivery_date."*".$cbo_pay_mode."*".$txt_wo_date."*".$cbo_currency."*".$cbo_source."*".$txt_do_no."*".$txt_deal_march."*".$txt_attention."*".$txt_remarks."*".$cbo_ready_to_approved."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		
		
		$booking_id_sql = "select id, mst_id from inv_purchase_requisition_dtls where mst_id=$id and status_active=1 and is_deleted=0";
		$non_order_item_result = sql_select($booking_id_sql);
		foreach($non_order_item_result as $row)
		{
			$req_dtls_id.=$row[csf("id")].",";
		}
		$req_dtls_id=chop($req_dtls_id, ",");
		
		$req_item_result = sql_select("select b.id, b.requisition_dtls_id, b.supplier_order_quantity, a.wo_number from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and b.requisition_dtls_id in($req_dtls_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		$wo_data=array();$req_check_arr=array();
		foreach($req_item_result as $row)
		{
			$wo_data[$row[csf("requisition_dtls_id")]]["supplier_order_quantity"]= $row[csf("supplier_order_quantity")];
			$wo_data[$row[csf("requisition_dtls_id")]]["wo_number"]= $row[csf("wo_number")];
			$req_check_arr[$row[csf("requisition_dtls_id")]][]=$row[csf("requisition_dtls_id")];
		}
		//echo "10**";print_r($req_check_arr);die;
		for($i=1; $i<=$id; $i++)
		{
			$reqqnty="reqqnty_".$i;
			$txtrowid="txtrowid_".$i;
			$hiderow="hiderow_".$i;
			$row_dtls_id=str_replace("'","",$$txtrowid);
			$row_qnty=str_replace("'","",$$reqqnty);
			unset($req_check_arr[$row_dtls_id]);
			if($row_qnty<$wo_data[$row_dtls_id]["supplier_order_quantity"])
			{
				echo "34**WO found === ".$wo_data[$row_dtls_id]["wo_number"]." \n Requisition Quantity Not Allow Less Then WO Quantity";die;
			}
		}
		//echo "10**";print_r($req_check_arr);die;
		if(count($req_check_arr)>0)
		{
			echo "34**WO Available === ".$wo_data[$row_dtls_id]["wo_number"]." \n Requisition Delete Not Allow";die;
		}
		
		
		
		$dtls_id=return_next_id( "id", "inv_purchase_requisition_dtls",1);
		$field_array_dlls_insert ="id,mst_id,item_category,job_id,job_no,booking_no,booking_id,buyer_id,style_ref_no,color_id,count_id,composition_id,com_percent,yarn_type_id,cons_uom,quantity,rate,amount,yarn_inhouse_date,remarks,inserted_by,insert_date,status_active,is_deleted";
		$field_array_dtls_update="job_id*job_no*booking_no*booking_id*buyer_id*style_ref_no*color_id*count_id*composition_id*com_percent*yarn_type_id*cons_uom*quantity*rate*amount*yarn_inhouse_date*remarks*updated_by*update_date";
		//$field_array_dtls_delete="status_active*is_deleted";
		$field_array_dtls_delete="updated_by*update_date*status_active*is_deleted";
		
		$wo_qnty_arr = return_library_array("select sum(b.supplier_order_quantity) as wo_qnty, b.requisition_dtls_id from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id = b.mst_id and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and a.wo_basis_id = 1 and b.requisition_no = $txt_wo_number group by b.requisition_dtls_id","requisition_dtls_id","wo_qnty");
		$add_comma=0;
		//$i=1;
		for($i=1; $i<=$total_row; $i++)
		{
			$txtjobid="txtjobid_".$i;
			$txtjobno="txtjobno_".$i;
			$txtbookingno="txtwono_".$i;
			$txtwoid="txtwoid_".$i;
			$cbobuyername="cbobuyername_".$i;
			$txtstyleno="txtstyleno_".$i;
			$txtyarncolor="txtyarncolor_".$i;
			$cbocount="cbocount_".$i;
			$cbocompone="cbocompone_".$i;
			$txtpacent="txtpacent_".$i;
			$cbotype="cbotype_".$i;
			$cbouom="cbouom_".$i;
			$reqqnty="reqqnty_".$i;
			$txtrate="txtrate_".$i;
			$txtamount="txtamount_".$i;
			$txtyarndate="txtyarndate_".$i;
			$txtremarks="txtremarks_".$i;
			$txtrowid="txtrowid_".$i;
			$hiderow="hiderow_".$i;
			
			$row_dtls_id=str_replace("'","",$$txtrowid);
			
			//$color_id=return_id( $$txtyarncolor, $color_arr, "lib_color", "id,color_name");
			
			if(str_replace("'","",$$txtyarncolor)!="")
			{
				if (!in_array(str_replace("'","",$$txtyarncolor),$new_array_color))
				{
					$color_id = return_id( str_replace("'","",$$txtyarncolor), $color_arr, "lib_color", "id,color_name","70");
					$new_array_color[$color_id]=str_replace("'","",$$txtyarncolor);
				}
				else $color_id =  array_search(str_replace("'","",$$txtyarncolor), $new_array_color);
			}
			else
			{
				$color_id=0;
			}
			
			if(str_replace("'","",$$hiderow)!=1)
			{
				if(str_replace("'","",$$reqqnty)>0)
				{
					if($row_dtls_id>0)// update here
					{
						$wo_qnty = $wo_qnty_arr[$row_dtls_id];
						if($wo_qnty > str_replace("'","",$$reqqnty))
						{
							echo "20**Requisition Qnty can not less than WO Qnty. \n WO qnty = $wo_qnty \n Requ. Qnty = ".str_replace("'","",$$reqqnty);
							die;
						}
						
						$update_id_arr[]=$row_dtls_id;
						$data_array_dtls_update[$row_dtls_id]=explode("*",("".$$txtjobid."*".$$txtjobno."*".$$txtbookingno."*".$$txtwoid."*".$$cbobuyername."*".$$txtstyleno."*".$color_id."*".$$cbocount."*".$$cbocompone."*".$$txtpacent."*".$$cbotype."*".$$cbouom."*".$$reqqnty."*".$$txtrate."*".$$txtamount."*".$$txtyarndate."*".$$txtremarks."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					
					}
					else // insert here
					{
						if ($add_comma!=0) $data_array_dtls_insert .=",";
						$data_array_dtls_insert .="(".$dtls_id.",".$id.",".$cbo_item_category.",".$$txtjobid.",".$$txtjobno.",".$$txtbookingno.",".$$txtwoid.",".$$cbobuyername.",".$$txtstyleno.",".$color_id.",".$$cbocount.",".$$cbocompone.",".$$txtpacent.",".$$cbotype.",".$$cbouom.",".$$reqqnty.",".$$txtrate.",".$$txtamount.",".$$txtyarndate.",".$$txtremarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
						$add_comma++;
						$dtls_id=$dtls_id+1;
					}
				}
			}
			else
			{
				if($row_dtls_id>0)
				{
					$delete_id_arr[]=$row_dtls_id;
					$data_array_dtls_delete[$row_dtls_id]=explode("*",("".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'"));
				}
			}
			
			$updateID_array[] = $row_dtls_id;
		}
		//echo "20**On test";die;
		//==================For row Delete >>>>>>>>=====
		
		$mstUpdate_id_array=array();
		$sql_dtls = "select  b.id from inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b where
		a.id=b.mst_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and a.id= $update_id";
		
		$nameArray=sql_select( $sql_dtls );
		foreach($nameArray as $row)
		{
			$mstUpdate_id_array[]=$row[csf('id')];
		}
		
		if(implode(',',$updateID_array)!="")
		{
			$distance_delete_id=array_diff($mstUpdate_id_array,$updateID_array);
		}
		else
		{
			$distance_delete_id=$mstUpdate_id_array;
		}
		
		
		$data_array_dtls_delete= $_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		
		if(implode(',',$distance_delete_id)!="")
		{
			foreach($distance_delete_id as $id_val)
			{
				$rID=sql_update("inv_purchase_requisition_dtls",$field_array_dtls_delete,$data_array_dtls_delete,"id","".$id_val."",1);
			}
		}
		//==================<<<<<<<<<<<< row Delete End========
		
		//echo '0**'.$xyz=bulk_update_sql_statement("inv_purchase_requisition_dtls","id",$field_array_dtls_update,$data_array_dtls_update,$update_id_arr);die;
		check_table_status( $_SESSION['menu_id'],0);
		$rID=$dtlsrID=$dtlsrIDI=$dtlsrIdDel=true;
		$rID=sql_update("inv_purchase_requisition_mst",$field_array,$data_array,"id",$id,0);
		
		if(count($update_id_arr)>0)
		{
			$dtlsrID=execute_query(bulk_update_sql_statement("inv_purchase_requisition_dtls","id",$field_array_dtls_update,$data_array_dtls_update,$update_id_arr),0);
		}
		//echo print_r($dtlsrID);die;
		if(count($delete_id_arr)>0)
		{
			$dtlsrIdDel=execute_query(bulk_update_sql_statement("inv_purchase_requisition_dtls","id",$field_array_dtls_delete,$data_array_dtls_delete,$delete_id_arr),0);
		}
		//echo print_r($dtlsrIdDel);die;
		if($data_array_dtls_insert!="")
		{
			$dtlsrIDI=sql_insert("inv_purchase_requisition_dtls",$field_array_dlls_insert,$data_array_dtls_insert,1);
		}
		
		//echo print_r($dtlsrIDI);die;
		
		
		if($db_type==0)
		{
			if($rID && $dtlsrID && $dtlsrIDI && $dtlsrIdDel)
			{
				mysql_query("COMMIT");
				echo "1**".$req_mst_no."**".$id;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".$id;
			}
		}
		else if($db_type==2)
		{
			if($rID && $dtlsrID && $dtlsrIDI && $dtlsrIdDel)
			{
				oci_commit($con);
				echo "1**".$req_mst_no."**".$id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$id;
			}
		}
		disconnect($con);
		die;
	}
	
	else if ($operation==2)
	{
		//echo "10**here";die;
		$con = connect();
		if($db_type==0) { mysql_query("BEGIN"); }
		
		$mst_id = str_replace("'","",$update_id);
		if($mst_id=="" || $mst_id==0){ echo "15**0"; die;}
		
		//$wo_dtls_id = return_field_value("a.id","wo_non_order_info_dtls a, inv_purchase_requisition_dtls b, inv_purchase_requisition_mst c","a.requisition_dtls_id = b.id and b.mst_id = c.id and c.entry_form = 70 and c.requ_no = $txt_wo_number and a.requisition_no = $txt_wo_number and a.status_active = 1 and b.status_active=1");
		$wo_dtls_id = sql_select("select d.wo_number  from wo_non_order_info_dtls a, inv_purchase_requisition_dtls b, inv_purchase_requisition_mst c, wo_non_order_info_mst d
		where a.requisition_dtls_id = b.id and b.mst_id = c.id and a.mst_id = d.id and c.entry_form = 70 and c.requ_no = $txt_wo_number and a.requisition_no = $txt_wo_number and a.status_active = 1 and b.status_active=1 and c.status_active = 1 and d.status_active=1");
		
		foreach ($wo_dtls_id as $val) {
			$wo_number .= "'".$val[csf("wo_number")]."',";
		}
		
		$wo_number = implode(",",array_filter(array_unique(explode(",", chop($wo_number,",")))));
		
		if($wo_number)
		{
			echo "20**Requisition Found in Work Order.\n Work Order No. = $wo_number"; die;
		}
		$rID = sql_update("inv_purchase_requisition_mst",'status_active*is_deleted','0*1',"id",$mst_id,1);
		$dtlsrID = sql_update("inv_purchase_requisition_dtls",'status_active*is_deleted','0*1',"mst_id",$mst_id,1);
		if($db_type==0 )
		{
			if($rID && $dtlsrID)
			{
				mysql_query("COMMIT");
				echo "2**";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $dtlsrID)
			{
				oci_commit($con);
				echo "2**";
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
}



if($action=="show_dtls_listview_update")
{
	//echo $data;die;    
	$booking_id_sql = "select b.id, b.mst_id, b.job_no, a.basis, b.booking_no from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id and a.entry_form=70 and b.mst_id=$data and b.status_active=1 and b.is_deleted=0";
    //echo $booking_id_sql;//die;
	$non_order_item_result = sql_select($booking_id_sql);
	$req_basis=$non_order_item_result[0][csf("basis")];
    //echo $req_basis;
	foreach($non_order_item_result as $row)
	{
		$req_dtls_id.=$row[csf("id")].",";
		$all_job.="'".$row[csf("job_no")]."',";
		$all_booking_no.="'".$row[csf("booking_no")]."',";
	}
	$req_dtls_id=chop($req_dtls_id, ",");
	$all_job=chop($all_job, ",");
    $all_booking_no=chop($all_booking_no, ",");

    //echo $all_booking_no;die;

	if($req_basis==1 || $req_basis==7 || $req_basis==10)
	{
		if($all_booking_no!="")
		{
			$sql="select a.booking_no, c.count_id as count_id, c.copm_one_id as copm_one_id, c.percent_one as percent_one, c.type_id as type_id, sum(((a.grey_fab_qnty*c.cons_ratio)/100)) as grey_fab_qnty, 0 as yarn_dtls_id, 1 as type
			from wo_booking_dtls a, wo_pre_cost_fab_yarn_cost_dtls c
			where a.job_no=c.job_no and c.fabric_cost_dtls_id=a.pre_cost_fabric_cost_dtls_id and a.booking_no in($all_booking_no) and a.status_active=1
			group by a.booking_no, c.count_id, c.copm_one_id, c.percent_one, c.type_id
			union all
			select a.booking_no, b.count_id as count_id, b.copm_one_id as copm_one_id, b.cons_ratio as percent_one, b.type_id as type_id, b.cons_qnty as grey_fab_qnty, b.id as yarn_dtls_id, 2 as type
            from wo_non_ord_samp_booking_mst p, wo_non_ord_samp_booking_dtls a, sample_development_yarn_dtls b
            where p.booking_no=a.booking_no and a.booking_no=b.booking_no and a.lib_yarn_count_deter_id=b.determin_id and a.booking_no in($all_booking_no) and a.status_active=1 and b.status_active=1";
			//echo $sql;//die;
			$sql_result=sql_select($sql);
			$yarn_require_arr=array();
			foreach($sql_result as $row)
			{
				if($row[csf("type")]==2)
				{
					if($yarn_dtls_check[$row[csf("yarn_dtls_id")]]=="")
					{
						$yarn_dtls_check[$row[csf("yarn_dtls_id")]]=$row[csf("yarn_dtls_id")];
						$yarn_require_arr[$row[csf("booking_no")]][$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]]+=$row[csf("grey_fab_qnty")];
					}
				}
				else
				{
					$yarn_require_arr[$row[csf("booking_no")]][$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]]+=$row[csf("grey_fab_qnty")];
				}
			} 

			$prev_req_qnty=sql_select("select b.booking_no, b.count_id, b.composition_id, b.com_percent, b.yarn_type_id, b.quantity
			from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b
			where a.id=b.mst_id and a.id<>$data and a.status_active=1 and b.status_active=1 and a.entry_form=70 and b.booking_no in($all_booking_no)");
			$prev_req_data=array();
			foreach($prev_req_qnty as $row)
			{
				$prev_req_data[$row[csf("booking_no")]][$row[csf("count_id")]][$row[csf("composition_id")]][$row[csf("com_percent")]][$row[csf("yarn_type_id")]]+=$row[csf("quantity")];
			}
            //echo '<pre>';print_r($prev_req_data);
		}
	}
	else if($req_basis==5)
	{
		if($all_job!="")
		{
			$condition= new condition();
			$condition->job_no(" in($all_job)");
			//echo $job_no;die;
			$condition->init();
			$yarn= new yarn($condition);
			//echo $yarn->getQuery();die;
			$yarn_require_arr=$yarn->getJobCountCompositionPercentAndTypeWiseYarnQtyArray();

			$prev_req_qnty=sql_select("select b.job_no, b.count_id, b.composition_id, b.com_percent, b.yarn_type_id, b.quantity
			from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b
			where a.id=b.mst_id and a.id<>$data and a.status_active=1 and b.status_active=1 and a.entry_form=70 and b.job_no in($all_job)");
			$prev_req_data=array();
			foreach($prev_req_qnty as $row)
			{
				$prev_req_data[$row[csf("job_no")]][$row[csf("count_id")]][$row[csf("composition_id")]][$row[csf("com_percent")]][$row[csf("yarn_type_id")]]+=$row[csf("quantity")];
			}
		}
    }
    else if($req_basis==4)
	{
		if($all_booking_no!="")
		{
			$sql="select a.sales_booking_no as booking_no, b.yarn_count_id as count_id, b.composition_id as copm_one_id, b.composition_perc as percent_one, b.yarn_type as type_id, b.cons_qty
			from fabric_sales_order_mst a, fabric_sales_order_yarn_dtls b
			where a.id=b.mst_id and a.sales_booking_no in($all_booking_no) and a.status_active=1 and b.status_active = 1";
			//echo $sql;die;
			$sql_result=sql_select($sql);
			$yarn_require_arr=array();
			foreach($sql_result as $row)
			{
				$yarn_require_arr[$row[csf("booking_no")]][$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]]+=$row[csf("cons_qty")];
            }

			$prev_req_qnty=sql_select("select b.booking_no, b.count_id, b.composition_id, b.com_percent, b.yarn_type_id, b.quantity
			from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b
			where a.id=b.mst_id and a.id<>$data and a.status_active=1 and b.status_active=1 and a.entry_form=70 and b.booking_no in($all_booking_no)");
			$prev_req_data=array();
			foreach($prev_req_qnty as $row)
			{
				$prev_req_data[$row[csf("booking_no")]][$row[csf("count_id")]][$row[csf("composition_id")]][$row[csf("com_percent")]]+=$row[csf("quantity")];
			}
        }
    } 


	$i=1;$add_remove_fnc="";
	if($req_dtls_id!="")
	{
		$req_item_result = sql_select("select id, requisition_dtls_id from wo_non_order_info_dtls where requisition_dtls_id in($req_dtls_id) and status_active=1 and is_deleted=0");
		foreach($req_item_result as $row)
		{
			$is_booked[$row[csf("requisition_dtls_id")]]= $row[csf("requisition_dtls_id")];
		}
		//print_r($is_booked);  die;

		$sql = "select a.id as mst_id, a.company_id,a.basis,b.booking_no,b.booking_id, b.id, b.mst_id, b.job_id, b.job_no, b.buyer_id, b.style_ref_no, b.color_id, b.count_id, b.composition_id, b.com_percent, b.yarn_type_id, b.cons_uom, b.quantity, b.rate, b.amount, b.yarn_inhouse_date, b.remarks
		  from
			  inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b
		  where
			a.id=b.mst_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and a.id=$data order by b.id asc";
		  //echo $sql;die;

		$result = sql_select($sql);
		foreach($result as $row)
		{
		   $job_numbers .= "'".$row[csf("job_no")]."',";
		   $booking_numbers .= "'".$row[csf("booking_no")]."',";
		}

		$job_numbers = chop( $job_numbers,"," );
		$booking_numbers = chop( $booking_numbers, "," );

		$sales_sql_result = sql_select("SELECT sales_booking_no,job_no,within_group,buyer_id,po_buyer FROM fabric_sales_order_mst WHERE status_active=1 and is_deleted=0 and job_no in($job_numbers) and sales_booking_no in($booking_numbers)");

		$salesData = array();
		foreach($sales_sql_result as $row)
		{
			if($row[csf("within_group")]==1)
			{
				$salesData[$row[csf("sales_booking_no")]][$row[csf("job_no")]]['buyer_id'] = $row[csf("po_buyer")];
			}else {
				$salesData[$row[csf("sales_booking_no")]][$row[csf("job_no")]]['buyer_id'] = $row[csf("buyer_id")];
			}
		}

		$job_independ_check=$result[0][csf("job_id")];

		foreach($result as $row)
		{
			$company_name=$row[csf("company_id")];
			//echo $is_booked; die;
			if($row[csf("yarn_inhouse_date")]!="" && $row[csf("yarn_inhouse_date")]!="0000-00-00") $yarn_iss_date=change_date_format($row[csf("yarn_inhouse_date")]); else $yarn_iss_date="";
			if($req_basis==1 || $req_basis==7 || $req_basis==10)
			{
        		$cons_qnty=$yarn_require_arr[$row[csf('booking_no')]][$row[csf('count_id')]][$row[csf('composition_id')]][$row[csf('com_percent')]][$row[csf('yarn_type_id')]]-$prev_req_data[$row[csf('booking_no')]][$row[csf('count_id')]][$row[csf('composition_id')]][$row[csf('com_percent')]][$row[csf('yarn_type_id')]];
			}
			else if($req_basis==5)
			{
				$cons_qnty=$yarn_require_arr[$row[csf('job_no')]][$row[csf('count_id')]][$row[csf('composition_id')]][$row[csf('com_percent')]][$row[csf('yarn_type_id')]]-$prev_req_data[$row[csf('job_no')]][$row[csf('count_id')]][$row[csf('composition_id')]][$row[csf('com_percent')]][$row[csf('yarn_type_id')]];
			}
			else if( $req_basis==4)
			{
				$cons_qnty=$yarn_require_arr[$row[csf('booking_no')]][$row[csf('count_id')]][$row[csf('composition_id')]][$row[csf('com_percent')]]-$prev_req_data[$row[csf('booking_no')]][$row[csf('count_id')]][$row[csf('composition_id')]][$row[csf('com_percent')]];
			}

			if($job_independ_check>0)
			{
			  $readonly_disable=" readonly disabled ";
			  $buyer_disable=1;
			  $place_holder='placeholder="Doble Click For Job"';
			  $place_holder_wo='placeholder="Doble Click For WO"';
			}
			else
			{
			  $readonly_disable=" readonly disabled ";
			  $is_disable=1;
			  $buyer_disable=1;
			  $place_holder='';
			  $place_holder_wo='placeholder="Doble Click For WO"';

			}
			if($row[csf("basis")] == 2 || $row[csf("basis")] ==4)
			{
			  $b_w_disable = 0;
			  $b_w_disable_text = "";
			}
			else
			{
			  $b_w_disable = 1 ;
			  $b_w_disable_text = "disabled";
			}

			if($is_booked[$row[csf("id")]]=="")
			{
				$booking_loc = "";
			}
			else
			{
				$booking_loc = "disabled";
			}

			// b_w_disable  is basis wise disable

			if($salesData[$row[csf("booking_no")]][$row[csf("job_no")]]['buyer_id']!="")
			{
				$buyerId = $salesData[$row[csf("booking_no")]][$row[csf("job_no")]]['buyer_id'];
			}else {
				$buyerId = $row[csf("buyer_id")];
			}

			?>
			<tr class="general" id="tr_<? echo $i; ?>">
				<td align="center">
				<input type="text" name="txtjobno_<? echo $i; ?>" id="txtjobno_<? echo $i; ?>" class="text_boxes" style="width:75px;"  value="<? echo $row[csf("job_no")]; ?>" readonly />
				<input type="hidden" id="txtjobid_<? echo $i; ?>" name="txtjobid_<? echo $i; ?>" style="width:100px;" value="<? echo $row[csf("job_id")]; ?>">
				<input type="hidden" name="txtrowid_<? echo $i; ?>" id="txtrowid_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf("id")]; ?>" style="width:70px;" />
				<input type="hidden" name="hiderow_<? echo $i; ?>" id="hiderow_<? echo $i; ?>" class="text_boxes" value="0" style="width:70px;" />
				</td>

				<td>
				<input type="text" name="txtwono_<? echo $i; ?>" id="txtwono_<? echo $i; ?>" class="text_boxes"  style="width:75px;" value="<?php echo $row[csf("booking_no")] ?>"  readonly />
				<input type="hidden" id="txtwoid_<? echo $i; ?>" name="txtwoid_<? echo $i; ?>"  value="<? echo $row[csf("booking_id")]; ?>" >
				</td>
				<td>
				<?
				echo create_drop_down( "cbobuyername_".$i, 90, "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0 order by buyer_name","id,buyer_name", 1, "-- Select --", $buyerId, "", $buyer_disable );
				?>
				</td>
				<td align="center"><input type="text" name="txtstyleno_<? echo $i; ?>" id="txtstyleno_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf("style_ref_no")]; ?>" style="width:75px;" <? echo $readonly_disable; ?> /></td>
				<td align="center"><input type="text" name="txtyarncolor[]" id="txtyarncolor_<? echo $i; ?>" class="text_boxes" value="<? echo $color_arr[$row[csf("color_id")]]; ?>" style="width:75px;" onFocus="add_auto_complete( <? echo $i; ?> )" /></td>
				<td align="center">
				<?
				echo create_drop_down( "cbocount_".$i, 70, "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count",1,"-- Select --", $row[csf("count_id")], "",$b_w_disable,""  );
				?>
				</td>
				<td align="center">
				<?
				echo create_drop_down( "cbocompone_".$i, 100, $composition,"", 1, "-- Select --", $row[csf("composition_id")], "",$b_w_disable,"","","",$ommitComposition );
				?>
				</td>
				<td><input type="text" name="txtpacent_<? echo $i; ?>" id="txtpacent_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf("com_percent")]; ?>" style="width:40px;" <? echo $b_w_disable_text; ?> /></td>
				<td>
				<?
				echo create_drop_down( "cbotype_".$i, 100, $yarn_type,"", 1, "-- Select --", $row[csf("yarn_type_id")], "",$b_w_disable,"","","",$ommitYarnType );
				?>
				</td>
				<td>
				<?
				echo create_drop_down( "cbouom_".$i, 60, $unit_of_measurement,"", 1, "-- Select--", $row[csf("cons_uom")], "",1 );
				?>
				</td>
				<td>
				<input type="text" id="reqqnty_<? echo $i; ?>" name="reqqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:55px" value="<? echo number_format($row[csf("quantity")],2,'.',''); ?>" onKeyUp="calculate_amount(<? echo $i; ?>)" />
				<input type="hidden" id="hiddenreqqnty_<? echo $i; ?>" name="hiddenreqqnty_<? echo $i; ?>" value="<? echo number_format($cons_qnty,2,'.',''); ?>"/>
				</td>
				<td>
				<input type="text" name="txtrate_<? echo $i; ?>" id="txtrate_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo number_format($row[csf("rate")],4,'.',''); ?>"  style="width:40px;" onKeyUp="calculate_amount(<? echo $i; ?>)" />
				<input type="hidden" name="hiddentxtrate_<? echo $i; ?>" id="hiddentxtrate_<? echo $i; ?>" value="<? echo number_format($row[csf("rate")],4,'.',''); ?>" />
				</td>
				<td><input type="text" name="txtamount_<? echo $i; ?>" id="txtamount_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo number_format($row[csf("amount")],4,'.',''); ?>" style="width:50px;" readonly /></td>
				<td><input class="datepicker" type="text" style="width:65px;" name="txtyarndate_<? echo $i; ?>" id="txtyarndate_<? echo $i; ?>" placeholder="Select Date" value="<? echo $yarn_iss_date; ?>" /></td>
				<td><input type="text" name="txtremarks_<? echo $i; ?>" id="txtremarks_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf("remarks")]; ?>" style="width:110px;" <? echo $add_remove_fnc; ?>  /></td>
				<td><input type="button" id="decrease_<? echo $i; ?>" name="decrease_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deletebreak_down_tr(<? echo $i; ?>);" <? echo $booking_loc;?>/></td>
			</tr>
			<?
			$i++;
			if($job_independ_check>0)
			{
			  $add_remove_fnc="onDblClick='openmypage_job(". $i . ")'";
			}
			else
			{
			  //$add_remove_fnc="onClick='add_break_down_tr(". $i . ")'";
			  $add_remove_fnc="onDblClick='openmypage_wo(". $i . ")'";
			}
		}
	}

	?>
  	<tr class="general" id="tr_<? echo $i; ?>">
        <td align="center">
        <input type="text" name="txtjobno_<? echo $i; ?>" id="txtjobno_<? echo $i; ?>" class="text_boxes" value="" style="width:75px;"  <? echo $place_holder; ?> readonly <? if($add_remove_fnc!="") echo $add_remove_fnc; else echo "onDblClick='openmypage_job(1)'".'&nbsp; placeholder="Doble Click For Job"'; ?>  />
        <input type="hidden" id="txtjobid_<? echo $i; ?>" name="txtjobid_<? echo $i; ?>" style="width:100px;">
        <input type="hidden" name="txtrowid_<? echo $i; ?>" id="txtrowid_<? echo $i; ?>" class="text_boxes" value="" style="width:70px;" />
        <input type="hidden" name="hiderow_<? echo $i; ?>" id="hiderow_<? echo $i; ?>" class="text_boxes" value="0" style="width:70px;" />
        </td>
        <td>
          <input type="text" name="txtwono_<? echo $i; ?>" id="txtwono_<? echo $i; ?>" class="text_boxes"  style="width:75px;" value=""  <? echo $place_holder_wo; ?> readonly <? if($add_remove_fnc!="") echo $add_remove_fnc; else echo "onDblClick='openmypage_wo(1)'".'&nbsp; placeholder="Doble Click For WO"'; ?> readonly />
          <input type="hidden" id="txtwoid_<? echo $i; ?>" name="txtwoid_<? echo $i; ?>"  value="<? echo $row[csf("job_id")]; ?>" >
          <input type="hidden" name="txtworowid_<? echo $i; ?>" id="txtworowid_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf("id")]; ?>"  />
          <input type="hidden" name="hiderow_<? echo $i; ?>" id="hiderow_<? echo $i; ?>" class="text_boxes" value="0" />
        </td>
        <td>
        <?
            echo create_drop_down( "cbobuyername_".$i, 90, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='".$company_name."' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select --", 0, "" );
        ?>
        </td>
        <td align="center"><input type="text" name="txtstyleno_<? echo $i; ?>" id="txtstyleno_<? echo $i; ?>" class="text_boxes" value="" style="width:75px;" /></td>
        <td align="center"><input type="text" name="txtyarncolor[]" id="txtyarncolor_<? echo $i; ?>" class="text_boxes" value="GREY" onFocus="add_auto_complete( <? echo $i; ?> )" style="width:75px;"/></td>
        <td align="center">
        <?
            echo create_drop_down( "cbocount_".$i, 70, "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count",1,"-- Select --", 0, "",0,"" );
        ?>
        </td>
        <td align="center">
        <?
            echo create_drop_down( "cbocompone_".$i, 100, $composition,"", 1, "-- Select --", 0, "",0,"","","",$ommitComposition );
        ?>
        </td>
        <td><input type="text" name="txtpacent_<? echo $i; ?>" id="txtpacent_<? echo $i; ?>" class="text_boxes" value="100" style="width:40px;" /></td>
        <td>
        <?
            echo create_drop_down( "cbotype_".$i, 100, $yarn_type,"", 1, "-- Select --", $row[csf("type_id")], "",$disabled,"","","",$ommitYarnType );
        ?>
        </td>
        <td>
        <?
            echo create_drop_down( "cbouom_".$i, 60, $unit_of_measurement,"", 1, "-- Select--", 12, "",1 );
        ?>
        </td>
        <td>
            <input type="text" id="reqqnty_<? echo $i; ?>" name="reqqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:55px" onKeyUp="calculate_amount(<? echo $i; ?>)" />
            <input type="hidden" id="hiddenreqqnty_<? echo $i; ?>" name="hiddenreqqnty_<? echo $i; ?>" value=""  />
        </td>
        <td>
            <input type="text" name="txtrate_<? echo $i; ?>" id="txtrate_<? echo $i; ?>" class="text_boxes_numeric" value="" style="width:40px;" onKeyUp="calculate_amount(<? echo $i; ?>)" />
            <input type="hidden" id="hiddentxtrate_<? echo $i; ?>" name="hiddentxtrate_<? echo $i; ?>" value=""  />
        </td>
        <td><input type="text" name="txtamount_<? echo $i; ?>" id="txtamount_<? echo $i; ?>" class="text_boxes_numeric" value="" style="width:50px;" readonly /></td>
        <td><input class="datepicker" type="text" style="width:65px;" name="txtyarndate_<? echo $i; ?>" id="txtyarndate_<? echo $i; ?>" placeholder="Select Date" /></td>
        <td><input type="text" name="txtremarks_<? echo $i; ?>" id="txtremarks_<? echo $i; ?>" class="text_boxes" value="" style="width:110px;" <? if($add_remove_fnc!="") echo $add_remove_fnc; else echo "onClick='add_break_down_tr(1)'"; ?>  /></td>
        <td><input type="button" id="decrease_<? echo $i; ?>" name="decrease_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deletebreak_down_tr(<? echo $i; ?>);" <? echo $booking_loc;?>/></td>
    </tr>
    <?
  exit();
}


if($action=="req_popup")
{
  extract($_REQUEST);
  echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
  ?>
  <script>
    /* $(document).ready(function(e) {
            $("#txt_search_common").focus();
        });

    function search_populate(str)
    {
      //alert(str);
      if(str==1) // wo number
      {
        document.getElementById('search_by_th_up').innerHTML="Enter WO Number";
        document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:140px " class="text_boxes" id="txt_search_common" value=""  />';
      }
      else if(str==2) // supplier
      {
        var supplier_name = '<option value="0">--- Select ---</option>';
        <?php
//$supplier_arr=return_library_array( "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=2 order by supplier_name",'id','supplier_name');
//foreach($supplier_arr as $key=>$val)
//{
//echo "supplier_name += '<option value=\"$key\">".($val)."</option>';";
//}
?>
        document.getElementById('search_by_th_up').innerHTML="Select Supplier Name";
        document.getElementById('search_by_td').innerHTML='<select  name="txt_search_common" style="width:150px " class="combo_boxes" id="txt_search_common">'+ supplier_name +'</select>';
      }
    }*/

    function js_set_value(wo_number)
    {
      $("#hidden_wo_number").val(wo_number);
      parent.emailwindow.hide();
    }

    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" width="800" rules="all">
                 <thead>
                    <th width="100">Item Category</th>
                    <th width="120">Supplier</th>
                    <th width="80">Requisition Number</th>
                     <th width="80">Job No</th>
                     <th width="80">Fab Booking</th>
                    <th width="200">WO Date Range</th>
                    <th ><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:50px;" /></th>
                </thead>
                <tr>
                    <td align="center">
                    <?
                        echo create_drop_down( "cboitem_category", 100, $item_category,"", 1, "-- Select --", $itemCategory, "",1);
                    ?>
                    </td>
                    <td align="center">
                    <?
                        echo create_drop_down( "cbo_supplier", 120, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id and b.party_type in(2) and c.tag_company in($company) order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
                    ?>
                    </td>
                    <td align="center">
                        <input type="text" style="width:80px" class="text_boxes"  name="txt_req_no" id="txt_req_no" />
                    </td>
                    <td align="center">
                        <input type="text" style="width:80px" class="text_boxes"  name="txt_job_no" id="txt_job_no" />
                    </td>
                    <td align="center">
                        <input type="text" style="width:80px" class="text_boxes"  name="txt_booking_no" id="txt_booking_no" />
                    </td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"> To
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                    </td>
                    <td align="center">
                        <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('cboitem_category').value+'_'+document.getElementById('cbo_supplier').value+'_'+document.getElementById('txt_req_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_booking_no').value, 'create_wo_search_list_view', 'search_div', 'yarn_requisition_entry_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:50px;" />
                    </td>
                </tr>
                <tr>
                    <td  align="center" height="40" valign="middle" colspan="7">
                        <? echo load_month_buttons(1);  ?>
                        <input type="hidden" id="hidden_wo_number" name="hidden_wo_number" value="" />
                    </td>
                </tr>
            </table>
            <br />
            <div id="search_div" ></div>
    </form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
  exit();
}

if($action=="create_wo_search_list_view")
{
  extract($_REQUEST);
  $ex_data = explode("_",$data);
  $itemCategory = $ex_data[0];
  $supplier_ref = $ex_data[1];
  $txt_req_no = $ex_data[2];
  $txt_date_from = $ex_data[3];
  $txt_date_to = $ex_data[4];
  $company = $ex_data[5];
  $job_no = $ex_data[6];
  $booking_no = $ex_data[7];

  $sql_cond="";
  if(trim($itemCategory)!=0) $sql_cond .= " and a.item_category_id='$itemCategory'";
  if(trim($supplier_ref)!=0) $sql_cond .= " and a.supplier_id='$supplier_ref'";
  if(trim($txt_req_no)!="") $sql_cond .= " and a.requ_no like '%$txt_req_no%'";
  if(trim($job_no)!="") $sql_cond .= " and b.job_no like '%$job_no%'";
  if(trim($booking_no)!="") $sql_cond .= " and b.booking_no like '%$booking_no%'";

  //print $company; $txt_pay_date=date("j-M-Y",strtotime($txt_pay_date));
  if($txt_date_from!="" && $txt_date_to!="")
  {
    if($db_type==2)
    {
      $sql_cond .= " and a.requisition_date between '".change_date_format(trim($txt_date_from),'','',1)."' and '".change_date_format(trim($txt_date_to),'','',1)."'";
    }
    else
    {
      $sql_cond.= " and a.requisition_date between '".change_date_format(trim($txt_date_from),"yyyy-mm-dd","-")."' and '".change_date_format(trim($txt_date_to),"yyyy-mm-dd","-")."'";
    }
  }
  if(trim($company)!="") $sql_cond .= " and a.company_id='$company'";

  $sql = "select a.id, a.requ_prefix_num, a.requ_no, a.company_id, a.item_category_id, a.supplier_id, a.requisition_date, a.delivery_date, a.pay_mode, sum(case when b.status_active=1 then b.quantity else 0 end) as qnty
      from inv_purchase_requisition_mst a,  inv_purchase_requisition_dtls b
      where a.id=b.mst_id and  a.status_active=1 and a.is_deleted=0 and a.entry_form=70 $sql_cond
      group by a.id, a.requ_prefix_num, a.requ_no, a.company_id, a.item_category_id, a.supplier_id, a.requisition_date, a.delivery_date, a.pay_mode
      order by id"; //and garments_nature=$garments_nature
 // echo $sql;die;
  $result = sql_select($sql);

  $arr=array(0=>$company_arr,1=>$supplier_arr,5=>$pay_mode);
  echo  create_list_view("list_view", "Company,Supplier,Req. Number,Req. Date,Delivery Date, Pay Mode, Quantity", "130,150,120,80,80,80","800","250",0, $sql, "js_set_value", "requ_no,id", "", 1, "company_id,supplier_id,0,0,0,pay_mode,0", $arr , "company_id,supplier_id,requ_no,requisition_date,delivery_date,pay_mode,qnty", "","",'0,0,0,3,3,0,2');
  exit();
}

if($action=="populate_data_from_search_popup")
{
  $sql = "select a.id, a.requ_prefix_num, a.requ_no, a.company_id, a.item_category_id, a.supplier_id, a.requisition_date, a.delivery_date, a.pay_mode, a.cbo_currency, a.source, a.do_no, a.attention, a.remarks, a.ready_to_approve,a.basis,a.is_approved
      from inv_purchase_requisition_mst a
      where a.status_active=1 and a.is_deleted=0 and a.id=$data
      order by id";
  //echo $sql;die;
  $result = sql_select($sql);
  foreach($result as $resultRow)
  {
    echo "$('#cbo_company_name').val('".$resultRow[csf("company_id")]."');\n";
    echo "$('#is_approved').val('".$resultRow[csf("is_approved")]."');\n";
    echo "$('#cbo_company_name').attr('disabled',true);\n";
    echo "$('#update_id').val('".$resultRow[csf("id")]."');\n";
    echo "$('#cbo_item_category').val('".$resultRow[csf("item_category_id")]."');\n";
    echo "$('#cbo_item_category').attr('disabled',true);\n";

    echo "$('#cbo_supplier').val('".$resultRow[csf("supplier_id")]."');\n";
    echo "$('#txt_delivery_date').val('".change_date_format($resultRow[csf("delivery_date")])."');\n";
    echo "$('#cbo_pay_mode').val('".$resultRow[csf("pay_mode")]."');\n";
    echo "$('#txt_wo_date').val('".change_date_format($resultRow[csf("requisition_date")])."');\n";

    echo "$('#cbo_currency').val('".$resultRow[csf("cbo_currency")]."');\n";
    echo "$('#cbo_ready_to_approved').val(".$resultRow[csf("ready_to_approve")].");\n";
    echo "$('#cbo_basis').val(".$resultRow[csf("basis")].");\n";
    echo "$('#cbo_basis').attr('disabled','true')".";\n";
    echo "$('#cbo_source').val('".$resultRow[csf("source")]."');\n";
    echo "$('#txt_do_no').val('".$resultRow[csf("do_no")]."');\n";
    echo "$('#txt_attention').val('".$resultRow[csf("attention")]."');\n";
    echo "$('#txt_remarks').val('".$resultRow[csf("remarks")]."');\n";

    if($resultRow[csf("is_approved")] == 1){
        echo "$('#approved').html('Approved');\n";
    }elseif($resultRow[csf("is_approved")] == 3){
        echo "$('#approved').html('Partial Approved');\n";
    }else{
        echo "$('#approved').html('');\n";
    }


    if($resultRow[csf("basis")] == 4){
      echo "$('#booking_td').html('Sales Order No');\n";
    }else{
      echo "$('#booking_td').html('Fab Booking');\n";
    }

  }
  exit();
}


if($action=="terms_condition_popup")
{
  echo load_html_head_contents("Terms Condition Search","../../../", 1, 1, $unicode,1);
  extract($_REQUEST);
  $terms_sql = sql_select("select id,terms from lib_terms_condition order by id");
  $terms_name = "";
  foreach( $terms_sql as $result )
  {
    $terms_name.= '{value:"'.$result[csf('terms')].'",id:'.$result[csf('id')]."},";
  }
  ?>
  <script>

    function termsName(rowID)
    {
      $("#termsconditionID_"+rowID).val('');

      $(function() {
        var terms_name = [<? echo substr($terms_name, 0, -1); ?>];
        $("#termscondition_"+rowID).autocomplete({
          source: terms_name,
          select: function (event, ui) {
            $("#termscondition_"+rowID).val(ui.item.value); // display the selected text
            $("#termsconditionID_"+rowID).val(ui.item.id); // save selected id to hidden input
          }
        });
      });
    }

    function add_break_down_tr(i)
    {
      var row_num=$('#tbl_termcondi_details tr').length-1;
      if (row_num!=i)
      {
        return false;
      }
      else
      {
        i++;

        $("#tbl_termcondi_details tr:last").clone().find("input,select").each(function() {
          $(this).attr({
            'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
            'name': function(_, name) { return name + i },
            'value': function(_, value) { return value }
          });
          }).end().appendTo("#tbl_termcondi_details");
        $("#tbl_termcondi_details tr:last td:first").html(i);
        $('#termscondition_'+i).removeAttr("onKeyPress").attr("onKeyPress","termsName("+i+");");
        $('#termscondition_'+i).removeAttr("onKeyUp").attr("onKeyUp","termsName("+i+");");
        $('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
        $('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
        $('#termscondition_'+i).val("");
        $('#termsconditionID_'+i).val("");
        set_all_onclick();
      }
    }

    function fn_deletebreak_down_tr(rowNo)
    {
      var numRow = $('table#tbl_termcondi_details tbody tr').length;
      if(numRow==rowNo && rowNo!=1)
      {
        $('#tbl_termcondi_details tbody tr:last').remove();
      }

    }

    function fnc_requisition_terms_condition( operation )
    {
      var row_num=$('#tbl_termcondi_details tr').length-1;
      var data_all="";
      for (var i=1; i<=row_num; i++)
      {
        if (form_validation('termscondition_'+i,'Term Condition')==false)
        {
          return;
        }
        if(data_all=="") data_all='txt_req_id*termscondition_'+i+'*termsconditionID_'+i; else data_all=data_all+ '*'+'txt_req_id*termscondition_'+i+'*termsconditionID_'+i;
        //data_all=data_all+get_submitted_data_string('txt_req_id*termscondition_'+i+'*termsconditionID_'+i,"../../../");
      }

      var data="action=save_update_delete_terms_condition&operation="+operation+'&total_row='+row_num+get_submitted_data_string(data_all,"../../../");
      //alert (data);return;
      //freeze_window(operation);
      http.open("POST","yarn_requisition_entry_controller.php",true);
      http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
      http.send(data);
      http.onreadystatechange = fnc_requisition_terms_condition_reponse;
    }

    function fnc_requisition_terms_condition_reponse()
    {
      if(http.readyState == 4)
      {
        //alert(http.responseText);
        //release_freezing();
        var reponse=trim(http.responseText).split('**');
        if (reponse[0].length>2) reponse[0]=10;
        if(reponse[0]==0 || reponse[0]==1)
        {
          parent.emailwindow.hide();
        }
      }
    }

  </script>
  </head>
  <body>
  <div align="center" style="width:100%;" >
    <? echo load_freeze_divs ("../../../",$permission,1); ?>
  <fieldset>
       <form id="termscondi_1" autocomplete="off">
        <input type="hidden" id="txt_req_id" name="txt_req_id" value="<? echo str_replace("'","",$update_id) ?>"/>
        <table width="650" cellspacing="0" class="rpt_table" border="0" id="tbl_termcondi_details" rules="all">
                    <thead>
                        <tr>
                            <th width="50">Sl</th><th width="530">Terms</th><th ></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    $terms_and_conditionID = return_field_value("terms_and_condition","inv_purchase_requisition_mst","id = $update_id");
                    $flag=0;
                    if($terms_and_conditionID=="")
                        $condd = " is_default=1";
                    else
                    {
                        $condd = " id in ($terms_and_conditionID)";
                        $flag=1;
                    }
                    $data_array=sql_select("select id, terms from lib_terms_condition where $condd order by id");
                    if( count($data_array)>0 )
                    {
                        $i=0;
                        foreach( $data_array as $row )
                        {
                            $i++;
                            ?>
                            <tr id="settr_1" align="center">
                                <td>
                  <? echo $i;?>
                                </td>
                                <td>
                                    <input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>" onKeyPress="termsName(<? echo $i;?>)" onKeyUp="termsName(<? echo $i;?>)" />
                                    <input type="hidden" id="termsconditionID_<? echo $i;?>"  name="termsconditionID_<? echo $i;?>" value="<? echo $row[csf('id')]; ?>"  readonly />
                                    </td>
                                    <td>
                                    <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
                                    <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>);" />
                                </td>
                            </tr>
                            <?
                        }
                    }
                    ?>
                </tbody>
            </table>
            <table width="650" cellspacing="0" class="" border="0">
                <tr>
                    <td align="center" height="15" width="100%"> </td>
                </tr>
                <tr>
                    <td align="center" width="100%" class="button_container">
            <?
                            echo load_submit_buttons( $permission, "fnc_requisition_terms_condition", 0,0 ,"reset_form('termscondi_1','','','','')",1) ;
                        ?>
                    </td>
                </tr>
            </table>
        </form>
    </fieldset>
  </div>
  </body>
  <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
  </html>
  <?
  exit();
}

if($action=="save_update_delete_terms_condition")
{
  $process = array( &$_POST );
  extract(check_magic_quote_gpc( $process ));
  if ($operation==0)  // Insert Here
  {
    $con = connect();
    if($db_type==0) mysql_query("BEGIN");


    $terms_sql = sql_select("select id,terms from lib_terms_condition order by id");
    $terms_name = array();
    foreach( $terms_sql as $result )
    {
      $terms_name[$result[csf('terms')]] = $result[csf('id')];
    }

    $id=return_next_id( "id", "lib_terms_condition", 1 );
    $field_array = "id,terms"; $data_array = "";
    $idsArr = "";$j=0;
    for ($i=1;$i<=$total_row;$i++)
    {
       $termscondition = "termscondition_".$i;
       $termscondition = $$termscondition;
       $termsconditionID = "termsconditionID_".$i;
       $termsconditionID = $$termsconditionID;
       if(str_replace("'","",$termsconditionID) == "")
       {
         $j++;
         if ($j!=1){ $data_array .=",";}
         $data_array .="(".$id.",".$termscondition.")";
         $idsArr[]=$id;
         $id=$id+1;
       }
       else
       {
         $idsArr[]=str_replace("'","",$termsconditionID);
       }
     }

    //echo "insert into lib_terms_condition (".$field_array.") values ".$data_array."";die;
    if($data_array!="")
    {
      $CondrID=sql_insert("lib_terms_condition",$field_array,$data_array,0);
    }


    foreach($idsArr as $value)
    {
       $value = str_replace("'","",$value);
    }
    $idsArr = implode(",", $idsArr);
    $rID=true;
    $rID = sql_update("inv_purchase_requisition_mst","terms_and_condition","'$idsArr'","id",str_replace("'","",$txt_req_id),1);
    if($db_type==0)
    {
      if( $rID && $data_array!="" && $CondrID){
        mysql_query("COMMIT");
        echo "0**";
      }
      else if($rID && $data_array==""){
        mysql_query("COMMIT");
        echo "0**";
      }
      else{
        mysql_query("ROLLBACK");
        echo "10**";
      }
    }
    //oci_commit($con); oci_rollback($con);
    if($db_type==2 || $db_type==1 )
    {
      if( $rID && $data_array!="" && $CondrID){
        oci_commit($con);
        echo "0**";
      }
      else if($rID && $data_array==""){
        oci_commit($con);
        echo "0**";
      }
      else{
        oci_rollback($con);
        echo "10**";
      }
    }
    disconnect($con);
    die;
  }
  else if ($operation==1)  // Update Here
  {
    $con = connect();
    if($db_type==0) mysql_query("BEGIN");

  //  if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}

    $terms_sql = sql_select("select id,terms from lib_terms_condition order by id");
    $terms_name = array();
    foreach( $terms_sql as $result )
    {
      $terms_name[$result[csf('terms')]] = $result[csf('id')];
    }

    $id=return_next_id( "id", "lib_terms_condition", 0 );
    $field_array = "id,terms"; $data_array = "";
    $idsArr = "";$j=0;
    for ($i=1;$i<=$total_row;$i++)
    {
       $termscondition = "termscondition_".$i;
       $termscondition = $$termscondition;
       $termsconditionID = "termsconditionID_".$i;
       $termsconditionID = $$termsconditionID;
       if(str_replace("'","",$termsconditionID) == "")
       {
         $j++;
         if ($j!=1){ $data_array .=",";}
         $data_array .="(".$id.",".$termscondition.")";
         $idsArr[]=$id;
         $id=$id+1;
       }
       else
       {
         $idsArr[]=$termsconditionID;
       }
     }

    if($data_array!="")
    {
      $CondrID=sql_insert("lib_terms_condition",$field_array,$data_array,1);
    }

    foreach($idsArr as &$value)
    {
       $value = str_replace("'","",$value);
    }
    $idsArr = implode(",", $idsArr);
    $rID = sql_update("inv_purchase_requisition_mst","terms_and_condition","'$idsArr'","wo_number",$txt_req_id,1);
    //echo $rID;die;
    //oci_commit($con); oci_rollback($con);
    //check_table_status( $_SESSION['menu_id'],0);
    if($db_type==0)
    {
      if( $rID && $data_array!="" && $CondrID){
        oci_commit($con);
        echo "0**";
      }
      else if($rID && $data_array==""){
        oci_commit($con);
        echo "0**";
      }
      else{
        oci_rollback($con);
        echo "10**";
      }
    }

    if($db_type==2 || $db_type==1 )
    {
      if( $rID && $data_array!="" && $CondrID){
        mysql_query("COMMIT");
        echo "0**";
      }
      else if($rID && $data_array==""){
        mysql_query("COMMIT");
        echo "0**";
      }
      else{
        mysql_query("ROLLBACK");
        echo "10**";
      }
    }
    disconnect($con);
    die;
  }
}



if($action=="yarn_requisition_print")
{
  $data=explode('*',$data);
  // print_r($data);die;
  if($data[4]==2){
      echo load_html_head_contents($data[2],"../../../", 1, 1, $unicode,'','');
  }else{
      echo load_html_head_contents($data[2],"../../", 1, 1, $unicode,'','');
  }


  //echo "jahid";die;

  $com_sql=sql_select("select a.id, a.company_name, a.city from lib_company a where a.id=$data[0]");

  $company_name=$com_sql[0][csf("company_name")];
  $location_name=$com_sql[0][csf("city")];
  //$address=$com_sql[0][csf("address")];
  $count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');

  /*$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
  $location=return_field_value("location_name","lib_location","id=$data[0]" );
  $address=return_field_value("address","lib_location","id=$data[0]");
  $lib_country_arr=return_library_array( "select id,country_name from lib_country","id", "country_name"  );
  $item_name_arr=return_library_array("select id,item_name from lib_item_group", "id","item_name");
  $supplier_name_library = return_library_array('SELECT id,supplier_name FROM lib_supplier','id','supplier_name');
  $lib_terms_condition=return_library_array( "select id, terms from lib_terms_condition",'id','terms');
  $color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');

  $buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');*/


  if($db_type==0)
  {
    $sql_data = sql_select("SELECT a.id, a.requ_prefix_num, a.requ_no, a.item_category_id, a.supplier_id, a.delivery_date, a.pay_mode, a.requisition_date, a.cbo_currency, a.source, a.attention, a.do_no, a.remarks, group_concat(b.job_id) as job_id FROM  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b WHERE a.id=b.mst_id and a.id = $data[1] group by a.id, a.requ_prefix_num, a.requ_no, a.item_category_id, a.supplier_id, a.delivery_date, a.pay_mode, a.requisition_date, a.cbo_currency, a.source, a.attention, a.do_no, a.remarks");
  }
  else if($db_type==2)
  {
    $sql_data = sql_select("SELECT a.id, a.requ_prefix_num, a.requ_no, a.item_category_id, a.supplier_id, a.delivery_date, a.pay_mode, a.requisition_date, a.cbo_currency, a.source, a.attention, a.do_no, a.remarks, LISTAGG(CAST(b.job_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.job_id) as job_id FROM  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b WHERE a.id=b.mst_id and a.id = $data[1] group by a.id, a.requ_prefix_num, a.requ_no, a.item_category_id, a.supplier_id, a.delivery_date, a.pay_mode, a.requisition_date, a.cbo_currency, a.source, a.attention, a.do_no, a.remarks");
  }



  $job_all_id="";
  foreach($sql_data as $row)
  {
    $requ_prefix_num=$row[csf("requ_prefix_num")];
    $requ_no=$row[csf("requ_no")];
    $item_category_id=$row[csf("item_category_id")];
    $supplier_id=$row[csf("supplier_id")];
    $delivery_date=$row[csf("delivery_date")];
    $requisition_date=$row[csf("requisition_date")];
    $cbo_currency=$row[csf("cbo_currency")];
    $pay_mode_id=$row[csf("pay_mode")];
    $source_id=$row[csf("source")];
    $attention=$row[csf("attention")];
    $do_no=$row[csf("do_no")];
    $remarks=$row[csf("remarks")];
    $job_id_all=array_unique(explode(",",$row[csf("job_id")]));
    foreach($job_id_all as $job_id)
    {
      if($job_all_id=="") $job_all_id=$job_id; else $job_all_id.=",".$job_id;
    }

  }


  if($job_all_id!="")
  {
    $sql_job=sql_select("select a.id, min(b.po_received_date) as po_received_date, min(b.pub_shipment_date) as pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id in($job_all_id) group by a.id");
    foreach($sql_job as $row)
    {
      $buyer_job_arr[$row[csf("id")]]["po_received_date"]=$row[csf("po_received_date")];
      $buyer_job_arr[$row[csf("id")]]["pub_shipment_date"]=$row[csf("pub_shipment_date")];
    }
  }




  $sql_supplier = sql_select("SELECT id,supplier_name,contact_no,country_id,web_site,email,address_1,address_2,address_3,address_4 FROM  lib_supplier WHERE id = $supplier_id");

   foreach($sql_supplier as $supplier_data)
  {//contact_no
    $row_mst[csf('supplier_id')];

    if($supplier_data[csf('address_1')]!='')$address_1 = $supplier_data[csf('address_1')].','.' ';else $address_1='';
    if($supplier_data[csf('address_2')]!='')$address_2 = $supplier_data[csf('address_2')].','.' ';else $address_2='';
    if($supplier_data[csf('address_3')]!='')$address_3 = $supplier_data[csf('address_3')].','.' ';else $address_3='';
    if($supplier_data[csf('address_4')]!='')$address_4 = $supplier_data[csf('address_4')].','.' ';else $address_4='';
    if($supplier_data[csf('contact_no')]!='')$contact_no = $supplier_data[csf('contact_no')].','.' ';else $contact_no='';
    if($supplier_data[csf('web_site')]!='')$web_site = $supplier_data[csf('web_site')].','.' ';else $web_site='';
    if($supplier_data[csf('email')]!='')$email = $supplier_data[csf('email')].','.' ';else $email='';
    //if($supplier_data[csf('country_id')]!=0)$country = $supplier_data[csf('country_id')].','.' ';else $country='';
    $country = $supplier_data['country_id'];

    $supplier_address = $address_1;
    $supplier_country =$country;
    $supplier_phone =$contact_no;
    $supplier_email = $email;
  }
  $image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
  $varcode_booking_no=$requ_no;
  ?>
  <div style="width:1030px;">
    <table width="1000" cellspacing="0" align="center">
        <tr>
          <td rowspan="3" width="70">
              <? if($data[4] == 2){ ?>
              <img src="../../../<? echo $image_location; ?>" height="70" width="200"></td>
          <? }else{  ?>
              <img src="../../<? echo $image_location; ?>" height="70" width="200"></td>
          <? }?>
            <td colspan="2" style="font-size:xx-large;" align="center"><strong><? echo $company_name; ?></strong></td>
            <td rowspan="3" colspan="2" width="250" id="barcode_img_id"> </td>
        </tr>
        <tr class="form_caption">
          <td colspan="2" align="center" style="font-size:14px"><? echo $location_name; //.",".$address; ?></td>
        </tr>
        <tr>
            <td colspan="2" align="center" style="font-size:x-large"><strong><? echo $data[2]; ?></strong></td>
        </tr>
        <tr>
            <td colspan="2" align="center" style="font-size:x-large"><strong>&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:#F00"><? if($data[3]==1){ echo "(Approved)";}else{echo "";}; ?> </font></strong></td>
        </tr>
    </table>
    <table width="1000" cellspacing="0" align="center">
         <tr>
            <td width="300" ><strong>To</strong>,&nbsp;<? echo $attention; ?></td>
            <td width="175"><strong>Req. No:</strong> &nbsp;<? echo $requ_no; ?></td>
            <td width="175" ><strong>Req. Date:</strong>&nbsp;<? if($delivery_date!="" && $delivery_date!="0000-00-00") echo change_date_format($requisition_date); ?></td>
            <td width="175"><strong>Delivery. Date:</strong>&nbsp;<? if($delivery_date!="" && $delivery_date!="0000-00-00") echo change_date_format($delivery_date); ?></td>
            <td align="left"><strong>Pay Mode :</strong>&nbsp;<? echo $pay_mode[$pay_mode_id]; ?></td>
        </tr>
        <tr>
            <td ><? echo $supplier_arr[$supplier_id];  echo $supplier_address;  echo  $lib_country_arr[$country];  echo "<br> Cell :".$supplier_phone;  echo "Email :".$supplier_email; ?></td>
            <td ><strong>Currency :</strong>&nbsp;<? echo $currency[$cbo_currency]; ?></td>
            <td ><strong>Source :</strong>&nbsp;<? echo $source[$source_id]; ?></td>
            <td ><strong>D/O No.:</strong>&nbsp;<? echo $do_no; ?></td>
            <td ><strong>Remarks:</strong>&nbsp;<? echo $remarks; ?></td>
        </tr>

        <tr>
            <td align="right" colspan="5" >&nbsp;</td>
        </tr>
    </table>
    <br>
    <table align="center" cellspacing="0" width="1000"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="110">Yarn Color</th>
            <th width="50">Count</th>
            <th width="140">Composition</th>
            <th width="30">%</th>
            <th width="70">Yarn Type</th>
            <th width="40" >UOM</th>
            <th width="70">Req Qty. </th>
            <th width="50">Rate</th>
            <th width="80">Amount</th>
            <th width="70">OPD</th>
            <th width="70">TOD</th>
            <th width="40">Lead Time (Days)</th>
            <th width="70">Yarn Inhouse Date</th>
            <th >Remarks</th>
        </thead>
        <tbody>
    <?


    $i=1; $buy_job_sty_val="";
    $mst_id=$dataArray[0][csf('id')];

    $sql_dtls="Select a.id, a.mst_id, a.job_id, a.job_no, a.buyer_id, a.style_ref_no, a.color_id, a.count_id, a.composition_id, a.com_percent, a.yarn_type_id, a.yarn_inhouse_date, a.cons_uom, a.quantity, a.rate, a.amount, a.remarks from  inv_purchase_requisition_dtls a where a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0 order by a.id";
    //echo $sql_dtls;
    $sql_result = sql_select($sql_dtls);

    foreach($sql_result as $row)
    {
       $job_numbers .= "'".$row[csf("job_no")]."',";
       $booking_numbers .= "'".$row[csf("booking_no")]."',";
    }

    $job_numbers = chop( $job_numbers,"," );
    $booking_numbers = chop( $booking_numbers, "," );

    $sales_sql_result = sql_select("SELECT sales_booking_no,job_no,within_group,buyer_id,po_buyer FROM fabric_sales_order_mst WHERE status_active=1 and is_deleted=0 and job_no in($job_numbers) and sales_booking_no in($booking_numbers)");

    $salesData = array();
    foreach($sales_sql_result as $row)
    {
        if($row[csf("within_group")]==1)
        {
            $salesData[$row[csf("sales_booking_no")]][$row[csf("job_no")]]['buyer_id'] = $row[csf("po_buyer")];
        }else {
            $salesData[$row[csf("sales_booking_no")]][$row[csf("job_no")]]['buyer_id'] = $row[csf("buyer_id")];
        }
    }

    $job_independ_check=$sql_result[0][csf("job_id")];
    $job_id_ref=array();
    $i=1;$k=1;
    foreach($sql_result as $row)
    {
      if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

      if($salesData[$row[csf("booking_no")]][$row[csf("job_no")]]['buyer_id']!="")
      {
            $buyerId = $salesData[$row[csf("booking_no")]][$row[csf("job_no")]]['buyer_id'];
      }else {
            $buyerId = $row[csf("buyer_id")];
      }

      if($job_independ_check>0)
      {
        if(!in_array($row[csf("job_id")],$job_id_ref))
        {
          $job_id_ref[]=$row[csf("job_id")];

          if($k!=1)
          {
            ?>
                        <tr bgcolor="#CCCCCC">
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                      <td align="right" colspan="2">Job Total:</td>
                            <td align="right"><? echo number_format($job_wise_qnty,2); ?></td>
                            <td >&nbsp;</td>
                            <td align="right"><? echo number_format($job_wise_amount,4); ?></td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                        </tr>
                        <?
            $job_wise_qnty=$job_wise_amount=0;
          }
          ?>
          <tr bgcolor="#FFFFCC">
            <td colspan="15">Job No: <? echo $row[csf("job_no")];?> &nbsp;&nbsp;Buyer Name : <? echo $buyer_arr[$buyerId];?> &nbsp;&nbsp; Style : <? echo $row[csf("style_ref_no")];?></td>
          </tr>
          <?
          $k++;
        }
        ?>
                <tr bgcolor="<? echo $bgcolor; ?>">
                    <td align="center"><? echo $i; ?></td>
                    <td align="center"><p><? echo $color_arr[$row[csf("color_id")]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $count_arr[$row[csf("count_id")]]; ?>&nbsp;</p></td>
                    <td ><p><? echo $composition[$row[csf("composition_id")]]; ?></p></td>
                    <td align="center"><p><? echo $row[csf("com_percent")]; ?></p></td>
                    <td align="center"><p><? echo $yarn_type[$row[csf("yarn_type_id")]]; ?></p></td>
                    <td align="center"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf("quantity")],2); $grand_tot_qnty+=$row[csf("quantity")]; ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf("rate")],4,".",""); ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf("amount")],4,".",""); $grand_total_val+=$row[csf("amount")];  ?></p></td>
                    <td align="center"><p><? if($buyer_job_arr[$row[csf("job_id")]]["po_received_date"]!="" && $buyer_job_arr[$row[csf("job_id")]]["po_received_date"]!="0000-00-00") echo change_date_format($buyer_job_arr[$row[csf("job_id")]]["po_received_date"]); else echo "&nbsp;"; ?></p></td>
                    <td align="center"><p><? if($buyer_job_arr[$row[csf("job_id")]]["pub_shipment_date"]!="" && $buyer_job_arr[$row[csf("job_id")]]["pub_shipment_date"]!="0000-00-00") echo change_date_format($buyer_job_arr[$row[csf("job_id")]]["pub_shipment_date"]); else echo "&nbsp;"; ?></p></td>
                    <td align="center"><p><?  $days_remian=datediff("d",$buyer_job_arr[$row[csf("job_id")]]["po_received_date"],$buyer_job_arr[$row[csf("job_id")]]["pub_shipment_date"]); if($days_remian!="")  echo $days_remian; ?></p></td>
                    <td align="center"><p><? if($row[csf("yarn_inhouse_date")]!="" && $row[csf("yarn_inhouse_date")]!="0000-00-00") echo change_date_format($row[csf("yarn_inhouse_date")]); ?></p></td>
                    <td ><p><? echo $row[csf("remarks")]; ?></p></td>
                </tr>
                <?
        $job_wise_qnty +=$row[csf("quantity")];
        $job_wise_amount +=$row[csf("amount")];
      }
      else
      {
        ?>
                <tr bgcolor="<? echo $bgcolor; ?>">
                    <td align="center"><? echo $i; ?></td>
                    <td align="center"><p><? echo $color_arr[$row[csf("color_id")]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $count_arr[$row[csf("count_id")]]; ?>&nbsp;</p></td>
                    <td ><p><? echo $composition[$row[csf("composition_id")]]; ?></p></td>
                    <td align="center"><p><? echo $row[csf("com_percent")]; ?></p></td>
                    <td align="center"><p><? echo $yarn_type[$row[csf("yarn_type_id")]]; ?></p></td>
                    <td align="center"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf("quantity")],2); $grand_tot_qnty+=$row[csf("quantity")]; ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf("rate")],4,".",""); ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf("amount")],4,".",""); $grand_total_val+=$row[csf("amount")]; ?></p></td>
                    <td align="center"><p><? //echo $row[csf("com_percent")]; ?></p></td>
                    <td align="center"><p><? //echo $row[csf("com_percent")]; ?></p></td>
                    <td align="center"><p><? //echo $row[csf("com_percent")]; ?></p></td>
                    <td align="center"><p><? if($row[csf("yarn_inhouse_date")]!="" && $row[csf("yarn_inhouse_date")]!="0000-00-00") echo change_date_format($row[csf("yarn_inhouse_date")]); ?></p></td>
                    <td ><p><? echo $row[csf("remarks")]; ?></p></td>
                </tr>
                <?
      }
      //if
                        $i++;
    }
    if($job_independ_check>0)
    {
      ?>
          <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td align="right" colspan="2">Job Total:</td>
                <td align="right"><? echo number_format($job_wise_qnty,2); ?></td>
                <td >&nbsp;</td>
                <td align="right"><? echo number_format($job_wise_amount,4); ?></td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
            </tr>
            <?
    }
    ?>
    </tbody>
        <tfoot>
          <tr>
              <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th colspan="2">Grand Total</th>
                <th><? echo number_format($grand_tot_qnty,2); ?></th>
                <th>&nbsp;</th>
                <th><? echo number_format($grand_total_val,4); ?></th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>

            </tr>
        </tfoot>
  </table>
    <br>

     <?
        echo get_spacial_instruction($requ_no,$width="1000px");

        $approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form=20 AND  mst_id ='$data[1]'  group by mst_id, approved_by order by  approved_by");
        $approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date,approved_no  from approval_history where entry_form=20 AND  mst_id ='$data[1]'  order by  approved_no,approved_date");
        $user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
        $user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
        $designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");
      ?>
     <? if(count($approved_sql)>0)
      {
          $sl=1;
          ?>
          <div style="margin-top:15px">
              <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                  <label><b>Others Purchase Order Approval Status </b></label>
                  <thead>
                    <tr style="font-weight:bold">
                        <th width="20">SL</th>
                        <th width="250">Name</th>
                        <th width="200">Designation</th>
                        <th width="100">Approval Date</th>
                    </tr>
                </thead>
                  <? foreach ($approved_sql as $key => $value)
                  {
                      ?>
                      <tr>
                          <td width="20"><? echo $sl; ?></td>
                          <td width="250"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
                          <td width="200"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
                          <td width="100"><? $approved_date=explode(" ",$value[csf("approved_date")]);

              echo change_date_format($approved_date[0])." ".$approved_date[1];  ?></td>
                      </tr>
                      <?
                      $sl++;
                  }
                  ?>
              </table>
          </div>
          <?
      }
      ?>
      <? if(count($approved_his_sql) > 0)
      {
          $sl=1;
          ?>
          <div style="margin-top:15px">
              <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
                  <label><b>Others Purchase Order Approval / Un-Approval History </b></label>
                  <thead>
                    <tr style="font-weight:bold">
                        <th width="20">SL</th>
                        <th width="150">Approved / Un-Approved</th>
                        <th width="150">Designation</th>
                        <th width="50">Approval Status</th>
                        <th width="150">Reason for Un-Approval</th>
                        <th width="150">Date</th>
                    </tr>
                </thead>
                  <? foreach ($approved_his_sql as $key => $value)
                  {
                    if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
                      ?>
                      <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
                          <td width="20"><? echo $sl; ?></td>
                          <td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
                          <td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
                          <td width="50">Yes</td>
                          <td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
                          <td width="150"><? $approved_date=explode(" ",$value[csf("approved_date")]);

              echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
                      </tr>
                          <?
                          $sl++;
                          $un_approved_date= explode(" ",$value[csf('un_approved_date')]);
                          $un_approved_date=$un_approved_date[0];
                          if($db_type==0) //Mysql
                          {
                              if($un_approved_date=="" || $un_approved_date=="0000-00-00") $un_approved_date="";else $un_approved_date=$un_approved_date;
                          }
                          else
                          {
                              if($un_approved_date=="") $un_approved_date="";else $un_approved_date=$un_approved_date;
                          }

                          if($un_approved_date!="")
                          {
                          ?>
                          <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
                          <td width="20"><? echo $sl; ?></td>
                          <td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
                          <td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
                          <td width="50">No</td>
                          <td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
                          <td width="150"><? $approved_date=explode(" ",$value[csf("un_approved_date")]);

              echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
                      </tr>

              <?
              $sl++;

            }

                  }
                  ?>
              </table>
          </div>
          <?
      }
      ?>
       <br/>
      <?
        echo signature_table(102, $data[0], "900px");
      ?>
  </div>
  <? if($data[4] == 2){ ?>
      <script type="text/javascript" src="../../../js/jquery.js"></script>
     <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
  <? }else{ ?>
     <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <? }?>
    <script>
    fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
    </script>
  <?
    exit();

}

if($action=="yarn_requisition_print_2")
{
	$data=explode('*',$data);
	echo load_html_head_contents($data[2],"../../", 1, 1, $unicode,'','');
	$com_sql=sql_select("select a.id, a.company_name, a.city from lib_company a where a.id=$data[0]");

	$company_name=$com_sql[0][csf("company_name")];
	$location_name=$com_sql[0][csf("city")];
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	if($db_type==0)
	{
		$sql_data = sql_select("SELECT a.id, a.requ_prefix_num, a.requ_no, a.item_category_id, a.supplier_id, a.delivery_date, a.pay_mode, a.requisition_date, a.cbo_currency, a.source, a.attention, a.do_no, a.remarks, group_concat(b.job_id) as job_id FROM  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b WHERE a.id=b.mst_id and a.id = $data[1] group by a.id, a.requ_prefix_num, a.requ_no, a.item_category_id, a.supplier_id, a.delivery_date, a.pay_mode, a.requisition_date, a.cbo_currency, a.source, a.attention, a.do_no, a.remarks");
	}
	else if($db_type==2)
	{
		$sql_data = sql_select("SELECT a.id, a.requ_prefix_num, a.requ_no, a.item_category_id, a.supplier_id, a.delivery_date, a.pay_mode, a.requisition_date, a.cbo_currency, a.source, a.attention, a.do_no, a.remarks,a.is_approved, LISTAGG(CAST(b.job_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.job_id) as job_id FROM  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b WHERE a.id=b.mst_id and a.id = $data[1] group by a.id, a.requ_prefix_num, a.requ_no, a.item_category_id, a.supplier_id, a.delivery_date, a.pay_mode, a.requisition_date, a.cbo_currency, a.source, a.attention, a.do_no, a.remarks, a.is_approved");
	}



	$job_all_id="";
	foreach($sql_data as $row)
	{
		$requ_prefix_num=$row[csf("requ_prefix_num")];
		$requ_no=$row[csf("requ_no")];
		$item_category_id=$row[csf("item_category_id")];
		$supplier_id=$row[csf("supplier_id")];
		$delivery_date=$row[csf("delivery_date")];
		$requisition_date=$row[csf("requisition_date")];
		$cbo_currency=$row[csf("cbo_currency")];
		$pay_mode_id=$row[csf("pay_mode")];
		$source_id=$row[csf("source")];
		$attention=$row[csf("attention")];
		$do_no=$row[csf("do_no")];
    $remarks=$row[csf("remarks")];
		$is_approved=$row[csf("is_approved")];
		$job_id_all=array_unique(explode(",",$row[csf("job_id")]));
		foreach($job_id_all as $job_id)
		{
		  if($job_all_id=="") $job_all_id=$job_id; else $job_all_id.=",".$job_id;
		}
	}


	$sql_supplier = sql_select("SELECT id,supplier_name,contact_no,country_id,web_site,email,address_1,address_2,address_3,address_4 FROM  lib_supplier WHERE id = $supplier_id");
	foreach($sql_supplier as $supplier_data)
	{
		//contact_no
		$row_mst[csf('supplier_id')];

		if($supplier_data[csf('address_1')]!='')$address_1 = $supplier_data[csf('address_1')].','.' ';else $address_1='';
		if($supplier_data[csf('address_2')]!='')$address_2 = $supplier_data[csf('address_2')].','.' ';else $address_2='';
		if($supplier_data[csf('address_3')]!='')$address_3 = $supplier_data[csf('address_3')].','.' ';else $address_3='';
		if($supplier_data[csf('address_4')]!='')$address_4 = $supplier_data[csf('address_4')].','.' ';else $address_4='';
		if($supplier_data[csf('contact_no')]!='')$contact_no = $supplier_data[csf('contact_no')].','.' ';else $contact_no='';
		if($supplier_data[csf('web_site')]!='')$web_site = $supplier_data[csf('web_site')].','.' ';else $web_site='';
		if($supplier_data[csf('email')]!='')$email = $supplier_data[csf('email')].','.' ';else $email='';
		//if($supplier_data[csf('country_id')]!=0)$country = $supplier_data[csf('country_id')].','.' ';else $country='';
		$country = $supplier_data['country_id'];

		$supplier_address = $address_1;
		$supplier_country =$country;
		$supplier_phone =$contact_no;
		$supplier_email = $email;
	}
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	$varcode_booking_no=$requ_no;
	?>
	<div style="width:1070px;">
    <table width="1050" cellspacing="0" align="center">
        <tr>
        	<td rowspan="3" width="70"><img src="../../<? echo $image_location; ?>" height="70" width="200"></td>
            <td colspan="2" style="font-size:xx-large;" align="center"><strong><? echo $company_name; ?></strong></td>
            <td rowspan="3" colspan="2" width="250" id="barcode_img_id"></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="2" align="center" style="font-size:14px"><? echo $location_name; //.",".$address; ?></td>
        </tr>
        <tr>
            <td colspan="2" align="center" style="font-size:x-large"><strong><? echo $data[2]; ?></strong></td>
        </tr>
    </table>
    <table width="1070" cellspacing="0" align="center">
    	<tr>
            <td width="300" ><strong>To</strong>,&nbsp;<? echo $attention; ?></td>
            <td width="200"><strong>Req. No:</strong> &nbsp;<? echo $requ_no; ?></td>
            <td width="200" ><strong>Req. Date:</strong>&nbsp;<? if($delivery_date!="" && $delivery_date!="0000-00-00") echo change_date_format($requisition_date); ?></td>
            <td width="200"><strong>Delivery. Date:</strong>&nbsp;<? if($delivery_date!="" && $delivery_date!="0000-00-00") echo change_date_format($delivery_date); ?></td>
            <td align="left"><strong>Pay Mode :</strong>&nbsp;<? echo $pay_mode[$pay_mode_id]; ?></td>
        </tr>
        <tr>
            <td ><? echo $supplier_arr[$supplier_id];  echo $supplier_address;  echo  $lib_country_arr[$country];  echo "<br> Cell :".$supplier_phone;  echo "Email :".$supplier_email; ?></td>
            <td ><strong>Currency :</strong>&nbsp;<? echo $currency[$cbo_currency]; ?></td>
            <td ><strong>Source :</strong>&nbsp;<? echo $source[$source_id]; ?></td>
            <td ><strong>D/O No.:</strong>&nbsp;<? echo $do_no; ?></td>
            <td ><strong>Remarks:</strong>&nbsp;<? echo $remarks; ?></td>
        </tr>

        <tr>
            <th align="center" colspan="5" style="color: red; font-weight: bold; font-size: 30px ">
              <? if($is_approved !=0 && $is_approved ==1) { echo 'Approved' ;}else if($is_approved ==3){ echo 'Partial Approved' ;} ?>
            </th>
        </tr>
    </table>
    <br>
    <table align="center" cellspacing="0" width="1250"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="20">SL</th>
            <th width="80">Job No</th>
 			      <th width="100">Internal Ref.</th>
 			      <th width="80">Fab Booking</th>
            <th width="90">Buyer Name</th>
            <th width="50">Style Reff.</th>
            <th width="70">Yarn Color</th>
            <th width="40">Count</th>
            <th width="140">Composition</th>
            <th width="30">%</th>
            <th width="70">Yarn Type</th>
            <th width="40" >UOM</th>
            <th width="70">Req Qty. </th>
            <th width="50">Rate</th>
            <th width="80">Amount</th>
            <th width="65">Yarn Inhouse Date</th>
            <th width="65">Remarks</th>
            <th>LC/SC</th>
        </thead>
        <tbody>
		<?
        $i=1; $buy_job_sty_val="";
        $mst_id=$dataArray[0][csf('id')];

        $sql_dtls="Select a.id, a.mst_id, a.job_id, a.job_no, a.booking_no, a.buyer_id, a.style_ref_no, a.color_id, a.count_id, a.composition_id, a.com_percent, a.yarn_type_id, a.yarn_inhouse_date, a.cons_uom, a.quantity, a.rate, a.amount, a.remarks
        from inv_purchase_requisition_dtls a where a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0 order by a.id";
        //echo $sql_dtls;
        $sql_result = sql_select($sql_dtls);

        foreach($sql_result as $row)
        {
          if($array_check[$row[csf("job_no")]]!=$row[csf("job_no")]){
            $array_check[$row[csf("job_no")]] =$row[csf("job_no")];
            $job_numbers .= "'".$row[csf("job_no")]."',";
          }

           $booking_numbers .= "'".$row[csf("booking_no")]."',";
        }

        $job_numbers = chop( $job_numbers,"," );
        $booking_numbers = chop( $booking_numbers, "," );
		$sql_job=sql_select("select a.job_no, b.sc_lc, b.grouping
		from wo_po_details_master a, wo_po_break_down b
		where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no in($job_numbers)
		group by a.job_no, b.sc_lc, b.grouping");
    
		foreach($sql_job as $row)
		{
      $buyer_job_arr[$row[csf("job_no")]]["sc_lc"].=$row[csf("sc_lc")].",";
			$internal_ref_arr[$row[csf("job_no")]]["internal_ref"].=$row[csf("grouping")].",";
		}

        $sales_sql_result = sql_select("SELECT sales_booking_no,job_no,within_group,buyer_id,po_buyer FROM fabric_sales_order_mst WHERE status_active=1 and is_deleted=0 and job_no in($job_numbers) and sales_booking_no in($booking_numbers)");

        $salesData = array();
        foreach($sales_sql_result as $row)
        {
            if($row[csf("within_group")]==1)
            {
                $salesData[$row[csf("sales_booking_no")]][$row[csf("job_no")]]['buyer_id'] = $row[csf("po_buyer")];
            }else {
                $salesData[$row[csf("sales_booking_no")]][$row[csf("job_no")]]['buyer_id'] = $row[csf("buyer_id")];
            }
        }


        $job_independ_check=$sql_result[0][csf("job_id")];
        $job_id_ref=array();
        $i=1;$k=1;
        foreach($sql_result as $row)
        {

            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            if($salesData[$row[csf("booking_no")]][$row[csf("job_no")]]['buyer_id']!="")
            {
                $buyerId = $salesData[$row[csf("booking_no")]][$row[csf("job_no")]]['buyer_id'];
            }else {
                $buyerId = $row[csf("buyer_id")];
            }

            if($job_independ_check>0)
            {
                if(!in_array($row[csf("job_no")],$job_id_ref))
                {
                    $job_id_ref[]=$row[csf("job_no")];
                    if($k!=1)
                    {
                        ?>
                        <tr bgcolor="#CCCCCC">
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td align="right" colspan="2">Style Total:</td>
                            <td align="right"><? echo number_format($job_wise_qnty,2); ?></td>
                            <td >&nbsp;</td>
                            <td align="right"><? echo number_format($job_wise_amount,4); ?></td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                        </tr>
                        <?
                        $job_wise_qnty=$job_wise_amount=0;
                    }
                    $k++;
                }
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>">
                    <td align="center"><? echo $i++; ?></td>
                    <td align="center"><? echo $row[csf("job_no")]; ?></td>
                    <td align="center"><p><? echo chop($internal_ref_arr[$row[csf("job_no")]]["internal_ref"],","); ?></td>
                    <td align="center"><? echo $row[csf("booking_no")]; ?></td>
                    <td align="center"><? echo $buyer_arr[$buyerId]; ?></td>
                    <td align="center"><? echo $row[csf("style_ref_no")]; ?></td>
                    <td align="center"><p><? echo $color_arr[$row[csf("color_id")]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $count_arr[$row[csf("count_id")]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $composition[$row[csf("composition_id")]]; ?></p></td>
                    <td align="center"><p><? echo $row[csf("com_percent")]; ?></p></td>
                    <td align="center"><p><? echo $yarn_type[$row[csf("yarn_type_id")]]; ?></p></td>
                    <td align="center"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf("quantity")],2); $grand_tot_qnty+=$row[csf("quantity")]; ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf("rate")],4,".",""); ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf("amount")],4,".",""); $grand_total_val+=$row[csf("amount")];  ?></p></td>
                    <td align="center"><p><? if($row[csf("yarn_inhouse_date")]!="" && $row[csf("yarn_inhouse_date")]!="0000-00-00") echo change_date_format($row[csf("yarn_inhouse_date")]); ?></p></td>
                    <td><p><? echo $row[csf("remarks")]; ?></p></td>
                    <td><p><? echo chop($buyer_job_arr[$row[csf("job_no")]]["sc_lc"],","); ?></p></td>
                </tr>
                <?
                $job_wise_qnty +=$row[csf("quantity")];
                $job_wise_amount +=$row[csf("amount")];
            }
            else
            {
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>">
                    <td align="center"><? echo $i++; ?></td>
                    <td align="center"><? echo $row[csf("job_no")]; ?></td>
                    <td align="center"><p><? echo chop($internal_ref_arr[$row[csf("job_no")]]["internal_ref"],","); ?></td>
                    <td align="center"><? echo $row[csf("booking_no")]; ?></td>
                    <td align="center"><? echo $buyer_arr[$buyerId]; ?></td>
                    <td align="center"><? echo $row[csf("style_ref_no")]; ?></td>
                    <td align="center"><p><? echo $color_arr[$row[csf("color_id")]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $count_arr[$row[csf("count_id")]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $composition[$row[csf("composition_id")]]; ?></p></td>
                    <td align="center"><p><? echo $row[csf("com_percent")]; ?></p></td>
                    <td align="center"><p><? echo $yarn_type[$row[csf("yarn_type_id")]]; ?></p></td>
                    <td align="center"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf("quantity")],2); $grand_tot_qnty+=$row[csf("quantity")]; ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf("rate")],4,".",""); ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf("amount")],4,".",""); $grand_total_val+=$row[csf("amount")]; ?></p></td>
                    <td align="center"><p><? if($row[csf("yarn_inhouse_date")]!="" && $row[csf("yarn_inhouse_date")]!="0000-00-00") echo change_date_format($row[csf("yarn_inhouse_date")]); ?></p></td>
                    <td><p><? echo $row[csf("remarks")]; ?></p></td>
                    <td><p><? echo chop($buyer_job_arr[$row[csf("job_no")]]["sc_lc"],","); ?></p></td>
                </tr>
                <?
            }
        }
        if($job_independ_check>0)
        {
            ?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td align="right" colspan="2">Style Total:</td>
                <td align="right"><? echo number_format($job_wise_qnty,2); ?></td>
                <td >&nbsp;</td>
                <td align="right"><? echo number_format($job_wise_amount,4); ?></td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
            </tr>
            <?
        }
        ?>
        </tbody>
        <tfoot>
          <tr>
                <th colspan="12">Grand Total</th>
                <th><? echo number_format($grand_tot_qnty,2); ?></th>
                <th>&nbsp;</th>
                <th><? //echo number_format($grand_total_val,4); ?></th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
        </tfoot>
	</table>
    <br>
    <h2>Summery</h2>
    <br>
    <table  width="700" class="rpt_table" border="1" cellpadding="0" cellspacing="0" align="center" rules="all">
        <thead>
            <tr>
                <th width="3%">Sl</th>
                <th width="25%">Composition</th>
                <th width="27%">Color</th>
                <th width="15%">Yarn type</th>
                <th width="15%">Count</th>
                <th>Yarn Qty</th>
            </tr>
        </thead>
        <tbody>
        <?php
$i = 1;
$buy_job_sty_val = "";
$mst_id = $dataArray[0][csf('id')];

$sql_dtls = "Select a.count_id, a.composition_id, a.color_id,a.yarn_type_id, sum(a.quantity) as yarn_group_total from  inv_purchase_requisition_dtls a where a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0 group by a.count_id, a.composition_id,a.color_id,a.yarn_type_id";
//echo $sql_dtls;//die;
$sql_result = sql_select($sql_dtls);
$total = 0;
foreach ($sql_result as $row) {
	?>
			<tr>
        <td align="center"><? echo $i++; ?></td>
        <td align="center" ><p><? echo $composition[$row[csf("composition_id")]]; ?></p></td>
        <td align="center" ><p><? echo $color_arr[$row[csf("color_id")]]; ?></p></td>
        <td align="center" ><? echo $yarn_type[$row[csf("yarn_type_id")]]; ?><p><? //echo $composition[$row[csf("composition_id")]]; ?></p></td>
        <td align="center"><p><? echo $count_arr[$row[csf("count_id")]]; ?>&nbsp;</p></td>
        <td align="center"><p><? echo number_format($row[csf("yarn_group_total")],2); $tot_qnty+=$row[csf("yarn_group_total")]; ?>&nbsp;</p></td>
			</tr>
			<?php
}
?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" align="center">Total</th>
                <th align="center"><? echo number_format($tot_qnty,2); ?></th>
            </tr>
        </tfoot>
    </table>
    <br>
    <?

     $lib_designation=return_library_array(" select id,custom_designation from lib_designation","id","custom_designation");

   $data_array=sql_select("select b.approved_by,b.approved_no, b.approved_date, b.un_approved_reason, c.user_full_name,c.designation  from  inv_purchase_requisition_mst a , approval_history b, user_passwd c where a.id=b.mst_id and b.approved_by=c.id and a.id=$data[1] and b.entry_form=20 order by b.id asc");

  ?>
       <table  width="100%" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
            <tr style="border:1px solid black;">
              <th colspan="5" style="border:1px solid black;">Approval Status</th>
            </tr>
            <tr style="border:1px solid black;">
              <th width="3%" style="border:1px solid black;">Sl</th>
              <th width="20%" style="border:1px solid black;">Name/Designation</th>
              <th width="27%" style="border:1px solid black;">Approval Date</th>
              <th width="20%" style="border:1px solid black;">Approval No</th>
              <th width="30%" style="border:1px solid black;">Un Approval Cause</th>
            </tr>
            </thead>
            <tbody>
            <?
      $i=1;
      foreach($data_array as $row){
      ?>
        <tr style="border:1px solid black;">
          <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
          <td width="20%" style="border:1px solid black;"><? echo $row[csf('user_full_name')]." / ". $lib_designation[$row[csf('designation')]];?></td>
          <td width="27%" style="border:1px solid black;"><? echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')])); ?></td>
          <td width="20%" style="border:1px solid black;"><? echo $row[csf('approved_no')];?></td>
          <td width="30%" style="border:1px solid black;"><? echo $row[csf('un_approved_reason')];?></td>
        </tr>
      <?
        $i++;
      }
        ?>
            </tbody>
        </table>
        <br>
    <?
    //echo $job_numbers;
	   //------------------------------ Query for TNA start-----------------------------------
        $job_no_all=explode(",","",$job_numbers);
        $sql_tna_task = "select id,po_number_id,
        (case when task_number=45 then task_start_date else null end) as yarn_requisition_start_date,
        (case when task_number=45 then task_finish_date else null end) as yarn_requisition_end_date,
        (case when task_number=47 then task_start_date else null end) as yarn_rec_start_date,
        (case when task_number=47 then task_finish_date else null end) as yarn_rec_end_date
        from tna_process_mst
        where status_active=1 and job_no in($job_numbers)";

        $tna_start_sql=sql_select( $sql_tna_task);
				$tna_date_task_arr=array();
				foreach($tna_start_sql as $row)
				{
					if($row[csf("yarn_requisition_start_date")]!="" && $row[csf("yarn_requisition_start_date")]!="0000-00-00"){
						$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_requisition_start_date']=$row[csf("yarn_requisition_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_requisition_end_date']=$row[csf("yarn_requisition_end_date")];
          }
          if($row[csf("yarn_rec_start_date")]!="" && $row[csf("yarn_rec_start_date")]!="0000-00-00"){
						$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_rec_start_date']=$row[csf("yarn_rec_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_rec_end_date']=$row[csf("yarn_rec_end_date")];
          }

          if($po_number_ids[$row[csf("po_number_id")]] != $row[csf("po_number_id")]){
            $all_po_number_ids[$row[csf("po_number_id")]] = $row[csf("po_number_id")];
          }
        }

        $po_sql ="SELECT a.style_ref_no,a.job_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in(".implode(",", $all_po_number_ids).")  and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
        $po_sql_res=sql_select($po_sql);
        foreach ($po_sql_res as $row)
        {
          //$po_num_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
          $po_num_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
          $po_num_arr[$row[csf("id")]]['job']=$row[csf("job_no")];
        }
        unset($po_sql_res);
        //$po_num_arr=return_library_array("select id,po_number from wo_po_break_down where id in(".implode(",", $all_po_number_ids).")",'id','po_number');
        //$po_num_arr=return_library_array("select id,job_no from wo_po_break_down where id in(".implode(",", $all_po_number_ids).")",'id','po_number');

        //print_r($tna_date_task_arr);//die;

	  //------------------------------ Query for TNA end-----------------------------------

    $task_short_name_arr=return_library_array( "select task_name,task_short_name from lib_tna_task where is_deleted=0 and status_active=1 and task_name in(45,47) order by task_name",'task_name','task_short_name');

    if(count($task_short_name_arr)>0)
    {
	   ?>
      <fieldset id="div_size_color_matrix" style="max-width:1200;">
        <legend>TNA Information</legend>
        <!--<span style="font-size:180; font-weight:bold;"></span>-->
        <table width="100%" class="rpt_table"  style="border:1px solid black;font-size:12px" border="1" cellpadding="2" cellspacing="0" rules="all">
          <thead>
            <tr>
            	<th width="50"  rowspan="2" align="center" valign="middle">SL</th>
              <th width="100" rowspan="2"  align="center" valign="middle"><b>Job No</b></th>
            	<th width="100" rowspan="2"  align="center" valign="middle"><b>Order No</b></th>
              <?
                $i=0;
                foreach ($task_short_name_arr as $key => $value) {
                  ?>
                    <th colspan="2" align="center" valign="middle"><b><? echo $task_short_name_arr[$key];?></b></th>
                  <?
                  $i++;
                }
              ?>
              <!-- <th colspan="2" align="center" valign="top"><b><? //echo $task_short_name_arr[252];?></b></th>
              <th colspan="2" align="center" valign="top"><b><? //echo $task_short_name_arr[47];?></b></th> -->
            </tr>
            <tr>
            	<th width="85" align="center" valign="middle"><b>Start Date</b></th>
              <th width="85" align="center" valign="middle"><b>End Date</b></th>

              <th width="85" align="center" valign="middle"><b>Start Date</b></th>
              <th width="85" align="center" valign="middle"><b>End Date</b></th>
            </tr>
          </thead>
          <tbody>
            <?
              $i=1;
              foreach($tna_date_task_arr as $order_id=>$row)
              {
                ?>
                <tr>
                	<td align="center"><? echo $i; ?></td>
                    <td align="center"><? echo $po_num_arr[$order_id]['job']; ?></td>
                    <td align="center"><? echo $po_num_arr[$order_id]['po']; ?></td>
                    <td align="center"><? echo change_date_format($row['yarn_requisition_start_date']); ?></td>
                    <td  align="center"><? echo change_date_format($row['yarn_requisition_end_date']); ?></td>

                    <td align="center"><? echo change_date_format($row['yarn_rec_start_date']); ?></td>
                    <td  align="center"><? echo change_date_format($row['yarn_rec_end_date']); ?></td>
                </tr>
                <?
                $i++;
              }
            ?>
          </tbody>
        </table>
      </fieldset>
      <br>
      <?
    }
    echo get_spacial_instruction($requ_no,$width="1070px");
    echo signature_table(102, $data[0], "1070px");
    ?>
    </div>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
    	fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
    </script>
    <?
    exit();
}
if($action=="yarn_requisition_print_5")
{
	$data=explode('*',$data);
	echo load_html_head_contents($data[2],"../../", 1, 1, $unicode,'','');
	$com_sql=sql_select("select a.id, a.company_name, a.city from lib_company a where a.id=$data[0]");

	$company_name=$com_sql[0][csf("company_name")];
	$location_name=$com_sql[0][csf("city")];
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	if($db_type==0)
	{
		$sql_data = sql_select("SELECT a.id, a.requ_prefix_num, a.requ_no, a.item_category_id, a.supplier_id, a.delivery_date, a.pay_mode, a.requisition_date, a.cbo_currency, a.source, a.attention, a.do_no, a.remarks, group_concat(b.job_id) as job_id FROM  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b WHERE a.id=b.mst_id and a.id = $data[1] group by a.id, a.requ_prefix_num, a.requ_no, a.item_category_id, a.supplier_id, a.delivery_date, a.pay_mode, a.requisition_date, a.cbo_currency, a.source, a.attention, a.do_no, a.remarks");
	}
	else if($db_type==2)
	{
		$sql_data = sql_select("SELECT a.id, a.requ_prefix_num, a.requ_no, a.item_category_id, a.supplier_id, a.delivery_date, a.pay_mode, a.requisition_date, a.cbo_currency, a.source, a.attention, a.do_no, a.remarks, LISTAGG(CAST(b.job_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.job_id) as job_id FROM  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b WHERE a.id=b.mst_id and a.id = $data[1] group by a.id, a.requ_prefix_num, a.requ_no, a.item_category_id, a.supplier_id, a.delivery_date, a.pay_mode, a.requisition_date, a.cbo_currency, a.source, a.attention, a.do_no, a.remarks");
	}



	$job_all_id="";
	foreach($sql_data as $row)
	{
		$requ_prefix_num=$row[csf("requ_prefix_num")];
		$requ_no=$row[csf("requ_no")];
		$item_category_id=$row[csf("item_category_id")];
		$supplier_id=$row[csf("supplier_id")];
		$delivery_date=$row[csf("delivery_date")];
		$requisition_date=$row[csf("requisition_date")];
		$cbo_currency=$row[csf("cbo_currency")];
		$pay_mode_id=$row[csf("pay_mode")];
		$source_id=$row[csf("source")];
		$attention=$row[csf("attention")];
		$do_no=$row[csf("do_no")];
		$remarks=$row[csf("remarks")];
		$job_id_all=array_unique(explode(",",$row[csf("job_id")]));
		foreach($job_id_all as $job_id)
		{
		  if($job_all_id=="") $job_all_id=$job_id; else $job_all_id.=",".$job_id;
		}
	}


	$sql_supplier = sql_select("SELECT id,supplier_name,contact_no,country_id,web_site,email,address_1,address_2,address_3,address_4 FROM  lib_supplier WHERE id = $supplier_id");
	foreach($sql_supplier as $supplier_data)
	{
		//contact_no
		$row_mst[csf('supplier_id')];

		if($supplier_data[csf('address_1')]!='')$address_1 = $supplier_data[csf('address_1')].','.' ';else $address_1='';
		if($supplier_data[csf('address_2')]!='')$address_2 = $supplier_data[csf('address_2')].','.' ';else $address_2='';
		if($supplier_data[csf('address_3')]!='')$address_3 = $supplier_data[csf('address_3')].','.' ';else $address_3='';
		if($supplier_data[csf('address_4')]!='')$address_4 = $supplier_data[csf('address_4')].','.' ';else $address_4='';
		if($supplier_data[csf('contact_no')]!='')$contact_no = $supplier_data[csf('contact_no')].','.' ';else $contact_no='';
		if($supplier_data[csf('web_site')]!='')$web_site = $supplier_data[csf('web_site')].','.' ';else $web_site='';
		if($supplier_data[csf('email')]!='')$email = $supplier_data[csf('email')].','.' ';else $email='';
		//if($supplier_data[csf('country_id')]!=0)$country = $supplier_data[csf('country_id')].','.' ';else $country='';
		$country = $supplier_data['country_id'];

		$supplier_address = $address_1;
		$supplier_country =$country;
		$supplier_phone =$contact_no;
		$supplier_email = $email;
	}
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	$varcode_booking_no=$requ_no;
	?>
	<div style="width:1070px;">
    <table width="1050" cellspacing="0" align="center">
        <tr>
        	<td rowspan="3" width="70"><img src="../../<? echo $image_location; ?>" height="70" width="200"></td>
            <td colspan="2" style="font-size:xx-large;" align="center"><strong><? echo $company_name; ?></strong></td>
            <td rowspan="3" colspan="2" width="250" id="barcode_img_id"></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="2" align="center" style="font-size:14px"><? echo $location_name; //.",".$address; ?></td>
        </tr>
        <tr>
            <td colspan="2" align="center" style="font-size:x-large"><strong><? echo $data[2]; ?></strong></td>
        </tr>
    </table>
    <table width="1070" cellspacing="0" align="center">
    	<tr>
            <td width="300" ><strong>To</strong>,&nbsp;<? echo $attention; ?></td>
            <td width="200"><strong>Req. No:</strong> &nbsp;<? echo $requ_no; ?></td>
            <td width="200" ><strong>Req. Date:</strong>&nbsp;<? if($delivery_date!="" && $delivery_date!="0000-00-00") echo change_date_format($requisition_date); ?></td>
            <td width="200"><strong>Delivery. Date:</strong>&nbsp;<? if($delivery_date!="" && $delivery_date!="0000-00-00") echo change_date_format($delivery_date); ?></td>
            <td align="left"><strong>Pay Mode :</strong>&nbsp;<? echo $pay_mode[$pay_mode_id]; ?></td>
        </tr>
        <tr>
            <td ><? echo $supplier_arr[$supplier_id];  echo $supplier_address;  echo  $lib_country_arr[$country];  echo "<br> Cell :".$supplier_phone;  echo "Email :".$supplier_email; ?></td>
            <td ><strong>Currency :</strong>&nbsp;<? echo $currency[$cbo_currency]; ?></td>
            <td ><strong>Source :</strong>&nbsp;<? echo $source[$source_id]; ?></td>
            <td ><strong>D/O No.:</strong>&nbsp;<? echo $do_no; ?></td>
            <td ><strong>Remarks:</strong>&nbsp;<? echo $remarks; ?></td>
        </tr>
        <tr>
            <td align="right" colspan="5" >&nbsp;</td>
        </tr>
    </table>
    <br>
    <?
      $i=1; $buy_job_sty_val="";
      $mst_id=$dataArray[0][csf('id')];

      $sql_dtls="Select a.id, a.mst_id, a.job_id, a.job_no, a.booking_no, a.buyer_id, a.style_ref_no, a.color_id, a.count_id, a.composition_id, a.com_percent, a.yarn_type_id, a.yarn_inhouse_date, a.cons_uom, a.quantity, a.rate, a.amount, a.remarks
      from inv_purchase_requisition_dtls a where a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0 order by a.id";
     //echo $sql_dtls;
      $sql_result = sql_select($sql_dtls);
      $job_independ_check=$sql_result[0][csf("job_id")];
    ?>
    <table align="center" cellspacing="0" width="1230"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="20">SL</th>
            <th width="80">Job No</th>
            <th width="80">Fab Booking</th>
            <th width="90">Buyer Name</th>
            <th width="50">Style Reff.</th>
            <th width="70">Yarn Color</th>
            <th width="40">Count</th>
            <th width="140">Composition</th>
            <th width="30">%</th>
            <th width="70">Yarn Type</th>
            <th width="40" >UOM</th>
            <th width="70">Req Qty. </th>
            <th width="50">Rate</th>
            <th width="80">Amount</th>
            <? if($job_independ_check>0){ ?>
            <th width="80">Job Qnty(Pcs)</th>
            <th width="80">FOB</th>
            <th width="70">Shipment date</th>
              <? }?>
            <th width="65">Yarn Inhouse Date</th>
            <th width="65">Remarks</th>
            <th>LC/SC</th>
        </thead>
        <tbody>
		<?

        foreach($sql_result as $row)
        {
          if($array_check[$row[csf("job_no")]]!=$row[csf("job_no")]){
            $array_check[$row[csf("job_no")]] =$row[csf("job_no")];
            $job_numbers .= "'".$row[csf("job_no")]."',";
          }

           $booking_numbers .= "'".$row[csf("booking_no")]."',";
        }

        $job_numbers = chop( $job_numbers,"," );
        $booking_numbers = chop( $booking_numbers, "," );
        $job_sql_query = "select a.job_no, b.sc_lc, b.po_total_price,b.po_quantity,max(b.pub_shipment_date) as pub_shipment_date
        from wo_po_details_master a, wo_po_break_down b
        where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no in($job_numbers)
        group by a.job_no, b.sc_lc,b.po_total_price,b.po_quantity";
        //echo $job_sql_query;//die;
        $sql_job=sql_select($job_sql_query);
        foreach($sql_job as $row)
        {
          $buyer_job_arr[$row[csf("job_no")]]["sc_lc"].=$row[csf("sc_lc")].",";
          //if($FOB_amount_array[$row[csf("job_no")]]==$FOB_amount_array[$row[csf("job_no")]])
          //{
            //$FOB_amount_array[$row[csf("job_no")]]=$row[csf("job_no")];
            $FOB_amount_array[$row[csf("job_no")]]+=$row[csf("po_total_price")];
            $job_qntity_array[$row[csf("job_no")]]+=$row[csf("po_quantity")];
			      $job_ship_date_array[$row[csf("job_no")]]=$row[csf("pub_shipment_date")];
          //}
        }

        $sales_sql_result = sql_select("SELECT sales_booking_no,job_no,within_group,buyer_id,po_buyer FROM fabric_sales_order_mst WHERE status_active=1 and is_deleted=0 and job_no in($job_numbers) and sales_booking_no in($booking_numbers)");

        $salesData = array();
        foreach($sales_sql_result as $row)
        {
            if($row[csf("within_group")]==1)
            {
                $salesData[$row[csf("sales_booking_no")]][$row[csf("job_no")]]['buyer_id'] = $row[csf("po_buyer")];
            }else {
                $salesData[$row[csf("sales_booking_no")]][$row[csf("job_no")]]['buyer_id'] = $row[csf("buyer_id")];
            }
        }

        foreach ($sql_result as $row) {
          $FOB_array[$row[csf("job_no")]]+=1;

        }
        //print_r($FOB_array);//die;
        $job_id_ref=array();
        $i=1;$k=1;
        foreach($sql_result as $row)
        {

            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            if($salesData[$row[csf("booking_no")]][$row[csf("job_no")]]['buyer_id']!="")
            {
                $buyerId = $salesData[$row[csf("booking_no")]][$row[csf("job_no")]]['buyer_id'];
            }else {
                $buyerId = $row[csf("buyer_id")];
            }


            if($check_data[$row[csf("job_no")]]!=$row[csf("job_no")])
            {
              $check_data[$row[csf("job_no")]]=$row[csf("job_no")];
              //print_r( $check_data);die;
              $rowspan=1;
            }else{
              $rowspan++;
            }
            if($job_independ_check>0)
            {
                if(!in_array($row[csf("job_no")],$job_id_ref))
                {
                    $job_id_ref[]=$row[csf("job_no")];
                    if($k!=1)
                    {
                        ?>
                        <tr bgcolor="#CCCCCC">
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td align="right" colspan="2">Style Total:</td>
                            <td align="right"><? echo number_format($job_wise_qnty,2); ?></td>
                            <td >&nbsp;</td>
                            <td align="right"><? echo number_format($job_wise_amount,4); ?></td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                            <td >&nbsp;</td>
                           	<td >&nbsp;</td>
                        </tr>
                        <?
                        $job_wise_qnty=$job_wise_amount=0;
                    }
                    $k++;
                }
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>">
                    <td align="center"><? echo $i++; ?></td>
                    <td align="center"><? echo $row[csf("job_no")]; ?></td>
                    <td align="center"><? echo $row[csf("booking_no")]; ?></td>
                    <td align="center"><? echo $buyer_arr[$buyerId]; ?></td>
                    <td align="center"><? echo $row[csf("style_ref_no")]; ?></td>
                    <td align="center"><p><? echo $color_arr[$row[csf("color_id")]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $count_arr[$row[csf("count_id")]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $composition[$row[csf("composition_id")]]; ?></p></td>
                    <td align="center"><p><? echo $row[csf("com_percent")]; ?></p></td>
                    <td align="center"><p><? echo $yarn_type[$row[csf("yarn_type_id")]]; ?></p></td>
                    <td align="center"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf("quantity")],2); $grand_tot_qnty+=$row[csf("quantity")]; ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf("rate")],4,".",""); ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf("amount")],4,".",""); $grand_total_val+=$row[csf("amount")];  ?></p></td>
                    <? if($rowspan==1){ ?>
                    <td align="right" rowspan="<? echo $FOB_array[$row[csf("job_no")]];?>" style="vertical-align:middle;"><p>
                      <?
                          echo $job_qntity_array[$row[csf("job_no")]];
                          $grand_total_job_qnty+=$job_qntity_array[$row[csf("job_no")]];
                      ?>
                      </p>
                    </td>
                    <td align="right" rowspan="<? echo $FOB_array[$row[csf("job_no")]];?>" style="vertical-align:middle;"><p>
                      <?
                          echo number_format($FOB_amount_array[$row[csf("job_no")]],2,".","");
                          $grand_total_fob+=$FOB_amount_array[$row[csf("job_no")]];
                      ?>
                      </p>
                    </td>
                    <td rowspan="<? echo $FOB_array[$row[csf("job_no")]];?>"><? echo change_date_format($job_ship_date_array[$row[csf("job_no")]]);?></td>
                    <? }?>
                    <td align="center"><p><? if($row[csf("yarn_inhouse_date")]!="" && $row[csf("yarn_inhouse_date")]!="0000-00-00") echo change_date_format($row[csf("yarn_inhouse_date")]); ?></p></td>
                    <td><p><? echo $row[csf("remarks")]; ?></p></td>
                    <td><p><? echo chop($buyer_job_arr[$row[csf("job_no")]]["sc_lc"],","); ?></p></td>
                </tr>
                <?
                $job_wise_qnty +=$row[csf("quantity")];
                $job_wise_amount +=$row[csf("amount")];
            }
            else
            {
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>">
                    <td align="center"><? echo $i++; ?></td>
                    <td align="center"><? echo $row[csf("job_no")]; ?></td>
                    <td align="center"><? echo $row[csf("booking_no")]; ?></td>
                    <td align="center"><? echo $buyer_arr[$buyerId]; ?></td>
                    <td align="center"><? echo $row[csf("style_ref_no")]; ?></td>
                    <td align="center"><p><? echo $color_arr[$row[csf("color_id")]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $count_arr[$row[csf("count_id")]]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $composition[$row[csf("composition_id")]]; ?></p></td>
                    <td align="center"><p><? echo $row[csf("com_percent")]; ?></p></td>
                    <td align="center"><p><? echo $yarn_type[$row[csf("yarn_type_id")]]; ?></p></td>
                    <td align="center"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf("quantity")],2); $grand_tot_qnty+=$row[csf("quantity")]; ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf("rate")],4,".",""); ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf("amount")],4,".",""); $grand_total_val+=$row[csf("amount")]; ?></p></td>
                    <td align="center"><p><? if($row[csf("yarn_inhouse_date")]!="" && $row[csf("yarn_inhouse_date")]!="0000-00-00") echo change_date_format($row[csf("yarn_inhouse_date")]); ?></p></td>
                    <td><p><? echo $row[csf("remarks")]; ?></p></td>
                    <td><p><? echo chop($buyer_job_arr[$row[csf("job_no")]]["sc_lc"],","); ?></p></td>
                </tr>
                <?
            }

        }
        if($job_independ_check>0)
        {
            ?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td align="right" colspan="2">Style Total:</td>
                <td align="right"><? echo number_format($job_wise_qnty,2); ?></td>
                <td >&nbsp;</td>
                <td align="right"><? echo number_format($job_wise_amount,4); ?></td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
            </tr>
            <?
        }
        ?>
        </tbody>
        <tfoot>
          <tr>
                <th colspan="11">Grand Total</th>
                <th><? echo number_format($grand_tot_qnty,2); ?></th>
                <th>&nbsp;</th>
                <th><? echo number_format($grand_total_val,2); ?></th>
                <? if($job_independ_check>0){ ?>
                <th><? echo $grand_total_job_qnty; ?></th>
                <th><? echo number_format($grand_total_fob,2); ?></th>
                <th>&nbsp;</th>
                <? } ?>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>

            </tr>
        </tfoot>
	</table>
    <br>
    <h2>Summery</h2>
    <br>
    <table  width="700" class="rpt_table" border="1" cellpadding="0" cellspacing="0" align="center" rules="all">
        <thead>
            <tr>
                <th width="3%">Sl</th>
                <th width="25%">Composition</th>
                <th width="27%">Color</th>
                <th width="15%">Yarn type</th>
                <th width="15%">Count</th>
                <th>Yarn Qty</th>
            </tr>
        </thead>
        <tbody>
        <?php
$i = 1;
$buy_job_sty_val = "";
$mst_id = $dataArray[0][csf('id')];

$sql_dtls = "Select a.count_id, a.composition_id, a.color_id,a.yarn_type_id, sum(a.quantity) as yarn_group_total from  inv_purchase_requisition_dtls a where a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0 group by a.count_id, a.composition_id,a.color_id,a.yarn_type_id";
//echo $sql_dtls;//die;
$sql_result = sql_select($sql_dtls);
$total = 0;
foreach ($sql_result as $row) {
	?>
			<tr>
                <td align="center"><? echo $i++; ?></td>
                <td align="center" ><p><? echo $composition[$row[csf("composition_id")]]; ?></p></td>
                <td align="center" ><p><? echo $color_arr[$row[csf("color_id")]]; ?></p></td>
                <td align="center" ><? echo $yarn_type[$row[csf("yarn_type_id")]]; ?><p><? //echo $composition[$row[csf("composition_id")]]; ?></p></td>
                <td align="center"><p><? echo $count_arr[$row[csf("count_id")]]; ?>&nbsp;</p></td>
                <td align="center"><p><? echo number_format($row[csf("yarn_group_total")],2); $tot_qnty+=$row[csf("yarn_group_total")]; ?>&nbsp;</p></td>
			</tr>
			<?php
}
?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" align="center">Total</th>
                <th align="center"><? echo number_format($tot_qnty,2); ?></th>
            </tr>
        </tfoot>
    </table>
    <br>
    <?
    //echo $job_numbers;
	   //------------------------------ Query for TNA start-----------------------------------
        $job_no_all=explode(",","",$job_numbers);



        $sql_tna_task = "select id,po_number_id,
        (case when task_number=45 then task_start_date else null end) as yarn_requisition_start_date,
        (case when task_number=45 then task_finish_date else null end) as yarn_requisition_end_date,
        (case when task_number=47 then task_start_date else null end) as yarn_rec_start_date,
        (case when task_number=47 then task_finish_date else null end) as yarn_rec_end_date
        from tna_process_mst
        where status_active=1 and job_no in($job_numbers)";

        $tna_start_sql=sql_select( $sql_tna_task);

				$tna_date_task_arr=array();
				foreach($tna_start_sql as $row)
				{

					if($row[csf("yarn_requisition_start_date")]!="" && $row[csf("yarn_requisition_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_requisition_start_date']=$row[csf("yarn_requisition_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_requisition_end_date']=$row[csf("yarn_requisition_end_date")];
          }
          if($row[csf("yarn_rec_start_date")]!="" && $row[csf("yarn_rec_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_rec_start_date']=$row[csf("yarn_rec_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_rec_end_date']=$row[csf("yarn_rec_end_date")];
          }

          if($po_number_ids[$row[csf("po_number_id")]] != $row[csf("po_number_id")])
          {
            $all_po_number_ids[$row[csf("po_number_id")]] = $row[csf("po_number_id")];
          }
        }

        $po_num_arr=return_library_array("select id,po_number from wo_po_break_down where id in(".implode(",", $all_po_number_ids).")",'id','po_number');

        //print_r($tna_date_task_arr);//die;

	  //------------------------------ Query for TNA end-----------------------------------

    $task_short_name_arr=return_library_array( "select task_name,task_short_name from lib_tna_task where is_deleted=0 and status_active=1 and task_name in(45,47)",'task_name','task_short_name');

    if(count($task_short_name_arr)>0)
    {
	   ?>

      <fieldset id="div_size_color_matrix" style="max-width:1200;">
        <legend>TNA Information</legend>
        <!--<span style="font-size:180; font-weight:bold;"></span>-->
        <table width="100%" class="rpt_table"  style="border:1px solid black;font-size:12px" border="1" cellpadding="2" cellspacing="0" rules="all">
          <thead>
            <tr>
            	<th width="50"  rowspan="2" align="center" valign="middle">SL</th>
            	<th width="100" rowspan="2"  align="center" valign="middle"><b>Order No</b></th>
              <?
                $i=0;
                foreach ($task_short_name_arr as $key => $value) {
                  ?>
                    <th colspan="2" align="center" valign="middle"><b><? echo $task_short_name_arr[$key];?></b></th>
                  <?
                  $i++;
                }
              ?>
              <!-- <th colspan="2" align="center" valign="top"><b><? //echo $task_short_name_arr[252];?></b></th>
              <th colspan="2" align="center" valign="top"><b><? //echo $task_short_name_arr[47];?></b></th> -->
            </tr>
            <tr>
            	<th width="85" align="center" valign="middle"><b>Start Date</b></th>
              <th width="85" align="center" valign="middle"><b>End Date</b></th>

              <th width="85" align="center" valign="middle"><b>Start Date</b></th>
              <th width="85" align="center" valign="middle"><b>End Date</b></th>
            </tr>
          </thead>
          <tbody>
            <?
              $i=1;
              foreach($tna_date_task_arr as $order_id=>$row)
              {
                ?>
                <tr>
                	<td align="center"><? echo $i; ?></td>
                    <td align="center"><? echo $po_num_arr[$order_id]; ?></td>
                    <td align="center"><? echo change_date_format($row['yarn_requisition_start_date']); ?></td>
                    <td  align="center"><? echo change_date_format($row['yarn_requisition_end_date']); ?></td>

                    <td align="center"><? echo change_date_format($row['yarn_rec_start_date']); ?></td>
                    <td  align="center"><? echo change_date_format($row['yarn_rec_end_date']); ?></td>
                </tr>
                <?
                $i++;
              }
            ?>
          </tbody>
        </table>
      </fieldset>
      <br>
      <?
    }
    echo get_spacial_instruction($requ_no,$width="1070px");
    echo signature_table(102, $data[0], "1070px");
    ?>
    </div>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
    	fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
    </script>
    <?
    exit();
}

if($action=="yarn_requisition_print_3")
{
  $data=explode('*',$data);
  echo load_html_head_contents($data[2],"../../", 1, 1, $unicode,'','');

  //echo "jahid";die;

  $com_sql=sql_select("select a.id, a.company_name, a.city from lib_company a where a.id=$data[0]");

  $company_name=$com_sql[0][csf("company_name")];
  $location_name=$com_sql[0][csf("city")];
  //$address=$com_sql[0][csf("address")];
  $count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
  $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer ",'id','buyer_name');


  if($db_type==0)
  {
    $sql_data = sql_select("SELECT a.id, a.requ_prefix_num, a.requ_no, a.item_category_id, a.supplier_id, a.delivery_date, a.pay_mode, a.requisition_date, a.cbo_currency, a.source, a.attention, a.do_no, a.dealing_marchant, a.remarks, b.buyer_id, group_concat(b.job_id) as job_id,a.basis FROM  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b WHERE a.id=b.mst_id and a.id = $data[1] group by a.id, a.requ_prefix_num, a.requ_no, a.item_category_id, a.supplier_id, a.delivery_date, a.pay_mode, a.requisition_date, a.cbo_currency, a.source, a.attention, a.do_no, a.dealing_marchant, a.remarks, b.buyer_id,a.basis");
  }
  else if($db_type==2)
  {
    $sql_data = sql_select("SELECT a.id, a.requ_prefix_num, a.requ_no, a.item_category_id, a.supplier_id, a.delivery_date, a.pay_mode, a.requisition_date, a.cbo_currency, a.source, a.attention, a.do_no, a.dealing_marchant, a.remarks, b.buyer_id, LISTAGG(CAST(b.job_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.job_id) as job_id,a.basis FROM  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b WHERE a.id=b.mst_id and a.id = $data[1] group by a.id, a.requ_prefix_num, a.requ_no, a.item_category_id, a.supplier_id, a.delivery_date, a.pay_mode, a.requisition_date, a.cbo_currency, a.source, a.attention, a.do_no, a.dealing_marchant, a.remarks, b.buyer_id,a.basis");
  }
  $job_all_id="";$buyer_name='';
  foreach($sql_data as $row)
  {
    if($buyer_name!='') $buyer_name.=",";
    $buyer_name.=$buyer_arr[$row[csf("buyer_id")]];
    $requ_prefix_num=$row[csf("requ_prefix_num")];
    $requ_no=$row[csf("requ_no")];
    $item_category_id=$row[csf("item_category_id")];
    $supplier_id=$row[csf("supplier_id")];
    $delivery_date=$row[csf("delivery_date")];
    $requisition_date=$row[csf("requisition_date")];
    $cbo_currency=$row[csf("cbo_currency")];
    $pay_mode_id=$row[csf("pay_mode")];
    $source_id=$row[csf("source")];
    $attention=$row[csf("attention")];
    $do_no=$row[csf("do_no")];
    $dealing_marchant=$row[csf("dealing_marchant")];
    $remarks=$row[csf("remarks")];
    $job_id_all=array_unique(explode(",",$row[csf("job_id")]));
    foreach($job_id_all as $job_id)
    {
      if($job_all_id=="") $job_all_id=$job_id; else $job_all_id.=",".$job_id;
    }

  }


  if($job_all_id!="")
  {
    $sql_job=sql_select("select a.id, min(b.po_received_date) as po_received_date, min(b.pub_shipment_date) as pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id in($job_all_id) group by a.id");
    foreach($sql_job as $row)
    {
      $buyer_job_arr[$row[csf("id")]]["po_received_date"]=$row[csf("po_received_date")];
      $buyer_job_arr[$row[csf("id")]]["pub_shipment_date"]=$row[csf("pub_shipment_date")];
    }
  }




  $sql_supplier = sql_select("SELECT id,supplier_name,contact_no,country_id,web_site,email,address_1,address_2,address_3,address_4 FROM  lib_supplier WHERE id = $supplier_id");

   foreach($sql_supplier as $supplier_data)
  {//contact_no
    $row_mst[csf('supplier_id')];

    if($supplier_data[csf('address_1')]!='')$address_1 = $supplier_data[csf('address_1')].','.' ';else $address_1='';
    if($supplier_data[csf('address_2')]!='')$address_2 = $supplier_data[csf('address_2')].','.' ';else $address_2='';
    if($supplier_data[csf('address_3')]!='')$address_3 = $supplier_data[csf('address_3')].','.' ';else $address_3='';
    if($supplier_data[csf('address_4')]!='')$address_4 = $supplier_data[csf('address_4')].','.' ';else $address_4='';
    if($supplier_data[csf('contact_no')]!='')$contact_no = $supplier_data[csf('contact_no')].','.' ';else $contact_no='';
    if($supplier_data[csf('web_site')]!='')$web_site = $supplier_data[csf('web_site')].','.' ';else $web_site='';
    if($supplier_data[csf('email')]!='')$email = $supplier_data[csf('email')].','.' ';else $email='';
    //if($supplier_data[csf('country_id')]!=0)$country = $supplier_data[csf('country_id')].','.' ';else $country='';
    $country = $supplier_data['country_id'];

    $supplier_address = $address_1;
    $supplier_country =$country;
    $supplier_phone =$contact_no;
    $supplier_email = $email;
  }
  $image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
  $varcode_booking_no=$requ_no;
  ?>
  <div style="width:1030px;">
    <table width="1000" cellspacing="0" align="center">
        <tr>
          <td rowspan="3" width="70"><img src="../../<? echo $image_location; ?>" height="70" width="200"></td>
            <td colspan="2" style="font-size:xx-large;" align="center"><strong><? echo $company_name; ?></strong></td>
            <td rowspan="3" colspan="2" width="250" id="barcode_img_id"> </td>
        </tr>
        <tr class="form_caption">
          <td colspan="2" align="center" style="font-size:14px"><? echo $location_name; //.",".$address; ?></td>
        </tr>
        <tr>
            <td colspan="2" align="center" style="font-size:x-large"><strong><? echo $data[2]; ?></strong></td>
        </tr>
    </table>
    <table width="1000" cellspacing="0" align="center">
         <tr>
            <td width="300" ><strong>To</strong>,&nbsp;<? echo $attention; ?></td>
          </tr>
          <tr>
            <td width="175"><strong>Dealing Merchant:</strong>&nbsp;<? echo $dealing_marchant; ?></td>
            <td width="175"><strong>Req. No:</strong> &nbsp;<? echo $requ_no; ?></td>
            <td width="175" ><strong>Req. Date:</strong>&nbsp;<? if($delivery_date!="" && $delivery_date!="0000-00-00") echo change_date_format($requisition_date); ?></td>
            <td width="175"><strong>Delivery. Date:</strong>&nbsp;<? if($delivery_date!="" && $delivery_date!="0000-00-00") echo change_date_format($delivery_date); ?></td>
            <td align="left"><strong>Pay Mode :</strong>&nbsp;<? echo $pay_mode[$pay_mode_id]; ?></td>
        </tr>
        <tr>
            <td ><? echo $supplier_arr[$supplier_id];  echo $supplier_address;  echo  $lib_country_arr[$country];  echo "<br> <b>Cell</b> :".$supplier_phone;  echo "<b>Email</b> :".$supplier_email; ?></td>
            <td ><strong>Currency :</strong>&nbsp;<? echo $currency[$cbo_currency]; ?></td>
            <td ><strong>Source :</strong>&nbsp;<? echo $source[$source_id]; ?></td>
            <td ><strong>D/O No.:</strong>&nbsp;<? echo $do_no; ?></td>
            <td ><strong>Remarks:</strong>&nbsp;<? echo $remarks; ?></td>
        </tr>
        <tr>
            <td ><strong>Buyer:</strong>&nbsp;<? echo $buyer_name; ?></td>
        </tr>

        <tr>
            <td align="right" colspan="5" >&nbsp;</td>
        </tr>
    </table>
    <br>
    <table align="center" cellspacing="0" width="1000"  border="1" rules="all" class="rpt_table" >
      <thead bgcolor="#dddddd" align="center">
          <th width="30">SL</th>
          <th width="110">Yarn Color</th>
          <th width="50">Count</th>
          <th width="140">Composition</th>
          <th width="30">%</th>
          <th width="70">Yarn Type</th>
          <th width="40" >UOM</th>
          <th width="70">Req Qty. </th>
          <th width="50">Rate</th>
          <th width="80">Amount</th>
          <th width="70">OPD</th>
          <th width="70">TOD</th>
          <th width="40">Lead Time (Days)</th>
          <th width="70">Yarn Inhouse Date</th>
          <th >Remarks</th>
      </thead>
      <tbody>
        <?
          $i=1; $buy_job_sty_val="";
          $mst_id=$dataArray[0][csf('id')];

          if($data[4] == 4)  //sales order
          {
            $fso_or_job_text = "FSO No";
          }else{
            $fso_or_job_text = "Job No";
          }
          //$sql_dtls="Select a.id, a.mst_id, a.job_id, a.job_no, a.buyer_id, a.style_ref_no, a.color_id, a.count_id, a.composition_id, a.com_percent, a.yarn_type_id, a.yarn_inhouse_date, a.cons_uom, a.quantity, a.rate, a.amount, a.remarks from  inv_purchase_requisition_dtls a where a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0 order by a.id";

          $sql_dtls = " select a.id, a.mst_id, a.job_id , a.job_no ,a.booking_no, a.buyer_id, a.style_ref_no, a.color_id, a.count_id, a.composition_id,
           a.com_percent, a.yarn_type_id, a.yarn_inhouse_date, a.cons_uom, a.quantity, a.rate, a.amount, a.remarks ,c.within_group,d.job_no as po_job
           from inv_purchase_requisition_mst b,inv_purchase_requisition_dtls a
           left join fabric_sales_order_mst c on a.job_no = c.job_no
           left join wo_booking_dtls d on c.sales_booking_no = d.booking_no
           where a.mst_id = b.id and a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0
            group by a.id, a.mst_id, a.job_id , a.job_no ,a.booking_no, a.buyer_id, a.style_ref_no, a.color_id, a.count_id, a.composition_id,
          a.com_percent, a.yarn_type_id, a.yarn_inhouse_date, a.cons_uom, a.quantity, a.rate, a.amount, a.remarks ,c.within_group,d.job_no
           order by a.id";
          //echo $sql_dtls;
          $sql_result = sql_select($sql_dtls);

          foreach($sql_result as $row)
          {
             $job_numbers .= "'".$row[csf("job_no")]."',";
             $booking_numbers .= "'".$row[csf("booking_no")]."',";
          }

          $job_numbers = chop( $job_numbers,"," );
          $booking_numbers = chop( $booking_numbers, "," );

          $sales_sql_result = sql_select("SELECT sales_booking_no,job_no,within_group,buyer_id,po_buyer FROM fabric_sales_order_mst WHERE status_active=1 and is_deleted=0 and job_no in($job_numbers) and sales_booking_no in($booking_numbers)");

          $salesData = array();
          foreach($sales_sql_result as $row)
          {
              if($row[csf("within_group")]==1)
              {
                  $salesData[$row[csf("sales_booking_no")]][$row[csf("job_no")]]['buyer_id'] = $row[csf("po_buyer")];
              }else {
                  $salesData[$row[csf("sales_booking_no")]][$row[csf("job_no")]]['buyer_id'] = $row[csf("buyer_id")];
              }
          }


          $job_independ_check=$sql_result[0][csf("job_id")];
          $job_id_ref=array();
          $i=1;$k=1;
          foreach($sql_result as $row)
          {
            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

            if($salesData[$row[csf("booking_no")]][$row[csf("job_no")]]['buyer_id']!="")
            {
                 $buyerId = $salesData[$row[csf("booking_no")]][$row[csf("job_no")]]['buyer_id'];
            }else {
                 $buyerId = $row[csf("buyer_id")];
            }

            if($job_independ_check>0)
            {
                if(!in_array($row[csf("job_id")],$job_id_ref))
                {
                  $job_id_ref[]=$row[csf("job_id")];

                  if($k!=1)
                  {
                    ?>
                      <tr bgcolor="#CCCCCC">
                          <td >&nbsp;</td>
                          <td >&nbsp;</td>
                          <td >&nbsp;</td>
                          <td >&nbsp;</td>
                          <td >&nbsp;</td>
                          <td align="right" colspan="2"><? echo $fso_or_job_text;?> Total:</td>
                          <td align="right"><? echo number_format($job_wise_qnty,2); ?></td>
                          <td >&nbsp;</td>
                          <td align="right"><? echo number_format($job_wise_amount,4); ?></td>
                          <td >&nbsp;</td>
                          <td >&nbsp;</td>
                          <td >&nbsp;</td>
                          <td >&nbsp;</td>
                          <td >&nbsp;</td>
                      </tr>
                      <?
                    $job_wise_qnty=$job_wise_amount=0;
                  }
                  ?>
                  <tr bgcolor="#FFFFCC">
                    <td colspan="15"><? echo $fso_or_job_text;?> : <? echo $row[csf("job_no")];?> &nbsp;&nbsp;Buyer Name : <? echo $buyer_arr[$buyerId];?> &nbsp;&nbsp; Style : <? echo $row[csf("style_ref_no")];

                    if($row[csf("within_group")] == 1) {
                      echo "&nbsp;&nbsp; Job No: ".$row[csf("po_job")] ;
                    } echo " &nbsp;&nbsp; Booking No: ".$row[csf("booking_no")];
                    ?>
                    </td>
                  </tr>
                  <?
                  $k++;
                }
                ?>
                  <tr bgcolor="<? echo $bgcolor; ?>">
                      <td align="center"><? echo $i; ?></td>
                      <td align="center"><p><? echo $color_arr[$row[csf("color_id")]]; ?>&nbsp;</p></td>
                      <td align="center"><p><? echo $count_arr[$row[csf("count_id")]]; ?>&nbsp;</p></td>
                      <td ><p><? echo $composition[$row[csf("composition_id")]]; ?></p></td>
                      <td align="center"><p><? echo $row[csf("com_percent")]; ?></p></td>
                      <td align="center"><p><? echo $yarn_type[$row[csf("yarn_type_id")]]; ?></p></td>
                      <td align="center"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                      <td align="right"><p><? echo number_format($row[csf("quantity")],2); $grand_tot_qnty+=$row[csf("quantity")]; ?></p></td>
                      <td align="right"><p><? echo number_format($row[csf("rate")],4,".",""); ?></p></td>
                      <td align="right"><p><? echo number_format($row[csf("amount")],4,".",""); $grand_total_val+=$row[csf("amount")];  ?></p></td>
                      <td align="center"><p><? if($buyer_job_arr[$row[csf("job_id")]]["po_received_date"]!="" && $buyer_job_arr[$row[csf("job_id")]]["po_received_date"]!="0000-00-00") echo change_date_format($buyer_job_arr[$row[csf("job_id")]]["po_received_date"]); else echo "&nbsp;"; ?></p></td>
                      <td align="center"><p><? if($buyer_job_arr[$row[csf("job_id")]]["pub_shipment_date"]!="" && $buyer_job_arr[$row[csf("job_id")]]["pub_shipment_date"]!="0000-00-00") echo change_date_format($buyer_job_arr[$row[csf("job_id")]]["pub_shipment_date"]); else echo "&nbsp;"; ?></p></td>
                      <td align="center"><p><?  $days_remian=datediff("d",$buyer_job_arr[$row[csf("job_id")]]["po_received_date"],$buyer_job_arr[$row[csf("job_id")]]["pub_shipment_date"]); if($days_remian!="")  echo $days_remian; ?></p></td>
                      <td align="center"><p><? if($row[csf("yarn_inhouse_date")]!="" && $row[csf("yarn_inhouse_date")]!="0000-00-00") echo change_date_format($row[csf("yarn_inhouse_date")]); ?></p></td>
                      <td ><p><? echo $row[csf("remarks")]; ?></p></td>
                  </tr>
                <?
                $job_wise_qnty +=$row[csf("quantity")];
                $job_wise_amount +=$row[csf("amount")];

            }
            else
            {
              ?>
                      <tr bgcolor="<? echo $bgcolor; ?>">
                          <td align="center"><? echo $i; ?></td>
                          <td align="center"><p><? echo $color_arr[$row[csf("color_id")]]; ?>&nbsp;</p></td>
                          <td align="center"><p><? echo $count_arr[$row[csf("count_id")]]; ?>&nbsp;</p></td>
                          <td ><p><? echo $composition[$row[csf("composition_id")]]; ?></p></td>
                          <td align="center"><p><? echo $row[csf("com_percent")]; ?></p></td>
                          <td align="center"><p><? echo $yarn_type[$row[csf("yarn_type_id")]]; ?></p></td>
                          <td align="center"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                          <td align="right"><p><? echo number_format($row[csf("quantity")],2); $grand_tot_qnty+=$row[csf("quantity")]; ?></p></td>
                          <td align="right"><p><? echo number_format($row[csf("rate")],4,".",""); ?></p></td>
                          <td align="right"><p><? echo number_format($row[csf("amount")],4,".",""); $grand_total_val+=$row[csf("amount")]; ?></p></td>
                          <td align="center"><p><? //echo $row[csf("com_percent")]; ?></p></td>
                          <td align="center"><p><? //echo $row[csf("com_percent")]; ?></p></td>
                          <td align="center"><p><? //echo $row[csf("com_percent")]; ?></p></td>
                          <td align="center"><p><? if($row[csf("yarn_inhouse_date")]!="" && $row[csf("yarn_inhouse_date")]!="0000-00-00") echo change_date_format($row[csf("yarn_inhouse_date")]); ?></p></td>
                          <td ><p><? echo $row[csf("remarks")]; ?></p></td>
                      </tr>
                      <?
            }
            //if
            $i++;
          }
          if($job_independ_check>0)
          {
            ?>
                <tr bgcolor="#CCCCCC">
                      <td >&nbsp;</td>
                      <td >&nbsp;</td>
                      <td >&nbsp;</td>
                      <td >&nbsp;</td>
                      <td >&nbsp;</td>
                      <td align="right" colspan="2"><? echo $fso_or_job_text; ?> Total:</td>
                      <td align="right"><? echo number_format($job_wise_qnty,2); ?></td>
                      <td >&nbsp;</td>
                      <td align="right"><? echo number_format($job_wise_amount,4); ?></td>
                      <td >&nbsp;</td>
                      <td >&nbsp;</td>
                      <td >&nbsp;</td>
                      <td >&nbsp;</td>
                      <td >&nbsp;</td>
                  </tr>
                  <?
          }
        ?>
    </tbody>
    <tfoot>
      <tr>
          <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th colspan="2">Grand Total</th>
            <th><? echo number_format($grand_tot_qnty,2); ?></th>
            <th>&nbsp;</th>
            <th><? echo number_format($grand_total_val,4); ?></th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>

        </tr>
    </tfoot>
  </table>
    <br>

     <?
        echo get_spacial_instruction($requ_no,$width="1000px");
	    echo signature_table(102, $data[0], "900px");
     ?>
  </div>
     <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
    fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
    </script>
  <?
    exit();
}

if($action=="yarn_requisition_print_4")
{
  $data=explode('*',$data);
  echo load_html_head_contents($data[2],"../../", 1, 1, $unicode,'','');

  $com_sql=sql_select("select a.id, a.company_name, a.city from lib_company a where a.id=$data[0]");

  $company_name=$com_sql[0][csf("company_name")];
  $location_name=$com_sql[0][csf("city")];
  $count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
  if($db_type==0)
  {
    $sql_data = sql_select("SELECT a.id, a.requ_prefix_num, a.requ_no, a.item_category_id, a.supplier_id, a.delivery_date, a.pay_mode, a.requisition_date, a.cbo_currency, a.source, a.attention, a.do_no, a.remarks, group_concat(b.job_id) as job_id FROM  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b WHERE a.id=b.mst_id and a.id = $data[1] group by a.id, a.requ_prefix_num, a.requ_no, a.item_category_id, a.supplier_id, a.delivery_date, a.pay_mode, a.requisition_date, a.cbo_currency, a.source, a.attention, a.do_no, a.remarks");
  }
  else if($db_type==2)
  {
    $sql_data = sql_select("SELECT a.id, a.requ_prefix_num, a.requ_no, a.item_category_id, a.supplier_id, a.delivery_date, a.pay_mode, a.requisition_date, a.cbo_currency, a.source, a.attention, a.do_no, a.remarks, LISTAGG(CAST(b.job_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.job_id) as job_id FROM  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b WHERE a.id=b.mst_id and a.id = $data[1] group by a.id, a.requ_prefix_num, a.requ_no, a.item_category_id, a.supplier_id, a.delivery_date, a.pay_mode, a.requisition_date, a.cbo_currency, a.source, a.attention, a.do_no, a.remarks");
  }

  foreach($sql_data as $row)
  {
    $requ_prefix_num=$row[csf("requ_prefix_num")];
    $requ_no=$row[csf("requ_no")];
    $item_category_id=$row[csf("item_category_id")];
    $supplier_id=$row[csf("supplier_id")];
    $delivery_date=$row[csf("delivery_date")];
    $requisition_date=$row[csf("requisition_date")];
    $cbo_currency=$row[csf("cbo_currency")];
    $pay_mode_id=$row[csf("pay_mode")];
    $source_id=$row[csf("source")];
    $attention=$row[csf("attention")];
    $do_no=$row[csf("do_no")];
    $remarks=$row[csf("remarks")];
  }

  $varcode_booking_no=$requ_no;
  ?>
  <div style="width:690px;">
    <table width="650" cellspacing="0" align="center">
        <tr>
            <td colspan="2" style="font-size:xx-large;" align="center"><strong><? echo $company_name; ?></strong></td>
        </tr>
        <tr>
            <td colspan="2" align="center" style="font-size:x-large"><strong><? echo $data[2]; ?></strong></td>
        </tr>
    </table>
    <br>
    <table width="650" cellspacing="0" align="center">
        <tr>
            <td width="300"><strong>Req. No:</strong> &nbsp;<? echo $requ_no; ?></td>
            <td width="175" ><strong>Req. Date:</strong>&nbsp;<? if($delivery_date!="" && $delivery_date!="0000-00-00") echo change_date_format($requisition_date); ?></td>
            <td><strong>Delivery. Date:</strong>&nbsp;<? if($delivery_date!="" && $delivery_date!="0000-00-00") echo change_date_format($delivery_date); ?></td>
        </tr>

        <tr>
            <td align="right" colspan="3" >&nbsp;</td>
        </tr>
    </table>
    <br>
    <table  width="650"  cellpadding="0" cellspacing="0" align="center" rules="all">
    <tr><td>Summery</td></tr>
    </table>
  <br>
      <table  width="650" class="rpt_table" border="1" cellpadding="0" cellspacing="0" align="center" rules="all">
          <thead>
              <tr>
                  <th width="3%">Sl</th>
                  <th width="20%">Count</th>
                  <th width="20%">Yarn type</th>
                  <th width="37%">Composition</th>
                  <th>Yarn Qty</th>
              </tr>
          </thead>
          <tbody>
             <!-- write the code -->
             <?php
$i = 1;
$buy_job_sty_val = "";
$mst_id = $dataArray[0][csf('id')];

$sql_dtls = "Select a.count_id, a.composition_id,a.yarn_type_id, sum(a.quantity) as yarn_group_total from  inv_purchase_requisition_dtls a where a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0 group by a.count_id, a.composition_id,a.yarn_type_id";
//echo $sql_dtls;//die;
$sql_result = sql_select($sql_dtls);
$total = 0;
foreach ($sql_result as $row) {
	?>
                  <tr>
                    <td align="center"><? echo $i++; ?></td>
                     <td align="center"><p><? echo $count_arr[$row[csf("count_id")]]; ?>&nbsp;</p></td>
                    <!-- <td align="center"><p><? echo $row[csf("yarn_group_total")]; ?>&nbsp;</p></td> -->
                     <td align="center" ><? echo $yarn_type[$row[csf("yarn_type_id")]]; ?><p><? //echo $composition[$row[csf("composition_id")]]; ?></p></td>
                     <td align="center" ><p><? echo $composition[$row[csf("composition_id")]]; ?></p></td>
                    <td align="center"><p><? echo number_format($row[csf("yarn_group_total")],2); $tot_qnty+=$row[csf("yarn_group_total")]; ?>&nbsp;</p></td>

                  </tr>

                  <?php
}

?>
          </tbody>
          <tfoot>
            <tr>
                <th colspan="4" align="center">Total</th>
                <th align="center"><? echo number_format($tot_qnty,2); ?></th>
            </tr>
          </tfoot>
      </table>
      <br>
      <table  width="650"  cellpadding="0" cellspacing="0" align="center" rules="all">
      <tr><td>
       <?
          echo get_spacial_instruction($requ_no,$width="650px");
          echo signature_table(102, $data[0], "650px");
       ?>
      </td></tr>
    </table>
    </div>
       <script type="text/javascript" src="../../js/jquery.js"></script>
    <?
      exit();
}

if($action=="sales_order_search_with_wo_popup")
{
  echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
  extract($_REQUEST);
  $permission=$_SESSION['page_permission'];
  //echo $company;
  ?>
  <script>
    function js_set_value_sales(str)
    {
      $("#hidden_tbl_id").val(str);
      parent.emailwindow.hide();
    }
    var permission='<? echo $permission; ?>';
  </script>

  <div style="width:750px;" >
    <form name="searchjob"  id="searchjob" autocomplete="off">
      <input type="hidden" id="hidden_tbl_id" value="" />
      <table width="750" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
        <thead>
            <th width="140">Company</th>
            <th width="105">Within Group</th>
            <th width="105">Buyer</th>
            <th width="125">Sales Order No.</th>
            <th width="125">Booking No.</th>
            <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('searchjob','search_div','','','')"  /></th>
        </thead>
        <tbody>
            <tr class="general">
                <td align="center">
                <?
                  echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", str_replace("'","",$company), "load_drop_down( 'yarn_requisition_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
                ?>
                </td>
                <td>
               <?
                    echo create_drop_down( "cbo_within_group", 105, $yes_no,"", 1, "-- Select --","","");
                ?>
                </td>
                <td align="center" id="buyer_td">
                <?
                $blank_array="select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name";
                echo create_drop_down( "cbo_buyer_name", 105, $blank_array,"id,buyer_name", 1, "-- Select Buyer --",0);
                ?>
                </td>
                <td align="center">
                    <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:125px" />
                </td>
                 <td align="center">
                    <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:125px" />
                 </td>
                 <td align="center">
                    <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_booking_no').value+'_'+document.getElementById('cbo_within_group').value, 'create_salesorder_search_list_view', 'search_div', 'yarn_requisition_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:70px;" />
                </td>
          </tr>
        </tbody>
      </table>
      <br>
      <div valign="top" id="search_div"> </div>
    </form>
  </div>
  </body>
  <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
  </html>

<?
}


if ($action=="create_salesorder_search_list_view")
{
  $data=explode("_",$data);
  $cbo_company_name=str_replace("'","",$data[0]);
  $cbo_buyer_name=str_replace("'","",$data[1]);
  $txt_order_no=str_replace("'","",$data[2]);
  $txt_booking_no=str_replace("'","",$data[3]);
  $cbo_within_group=str_replace("'","",$data[4]);
  //echo $cbo_company_name."**".$txt_job_no."**".$txt_booking_no."<br>";die;
  $company_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");
  $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

  if($cbo_company_name!=0) $cbo_company_name="and a.company_id='$cbo_company_name'"; else $cbo_company_name="";

  if($cbo_buyer_name!=0)
  {
    $cbo_buyer_cond_1=" and c.buyer_id='$cbo_buyer_name'";
    $cbo_buyer_cond_2=" and a.buyer_id='$cbo_buyer_name'";
  }
  else
  {
    $cbo_buyer_cond_1="";
    $cbo_buyer_cond_2="";
  }
  if($txt_order_no!="") $order_cond="and a.job_no like '%".trim($txt_order_no)."%'"; else $order_cond="";
  if($txt_booking_no!="") $booking_cond="and a.sales_booking_no like '%".trim($txt_booking_no)."%'"; else $booking_cond="";

  if($db_type == 1) $select_uom = " group_concat(b.order_uom) as order_uom"; else $select_uom = " listagg(b.order_uom,',' ) within group (order by b.order_uom) order_uom";
    $sql1= "select a.id, a.company_id, c.buyer_id, a.style_ref_no, a.job_no,a.po_buyer, a.within_group, a.sales_booking_no, $select_uom
   from fabric_sales_order_mst a,fabric_sales_order_dtls b, wo_booking_mst c
   where a.id = b.mst_id and a.booking_id = c.id and a.status_active=1 and b.status_active=1 and b.status_active=1 $cbo_company_name $cbo_buyer_cond_1 $order_cond $booking_cond and a.within_group = 1
   group by a.id, a.company_id, c.buyer_id, a.style_ref_no, a.job_no,a.po_buyer, a.within_group, a.sales_booking_no";

   $sql2= "select a.id, a.company_id, a.buyer_id, a.style_ref_no, a.job_no, a.po_buyer, a.within_group, a.sales_booking_no, $select_uom
   from fabric_sales_order_mst a,fabric_sales_order_dtls b
   where a.id = b.mst_id and a.status_active=1 and b.status_active=1 and b.status_active=1 $cbo_company_name $cbo_buyer_cond_2 $order_cond $booking_cond and a.within_group = 2
   group by a.id, a.company_id, a.buyer_id, a.style_ref_no, a.job_no,a.po_buyer, a.within_group, a.sales_booking_no
   order by id";

  if($cbo_within_group==1)
  {
   $sql = $sql1;
  }
  else if($cbo_within_group==2)
  {
  $sql = $sql2;
  }else{
  $sql = $sql1. " union all ". $sql2;
  }
  //echo $sql;

  ?>
  <div style="width:690px;">
    <input type="hidden" id="hidden_tbl_id_wo">
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table">
          <thead>
            <th width="40">SL</th>
            <th width="100">Buyer</th>
            <th width="140">Sales Order No</th>
            <th width="140">Booking No</th>
            <th width="100">Within Group</th>
            <th width="">Order Uom</th>
          </thead>
    </table>
    <div style="width:670px; overflow-y:scroll; max-height:250px;" id="buyer_list_view" >
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table" id="tbl_list_search">
      <?
        $i=1;
          $job_sql=sql_select( $sql );
          foreach ($job_sql as $rows)
          {
            if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer" onClick="js_set_value_sales('<? echo $rows[csf('id')].'_'.$rows[csf('job_no')].'_'.$rows[csf('buyer_id')]; ?>'); ">
              <td width="40" align="center"><p> <? echo $i; ?></p></td>
              <td width="100">
                <p>
                  <?
                  if($rows[csf('within_group')] == 1)
                  {
                      echo  $buyer_arr[$rows[csf('po_buyer')]];
                  }else {
                      echo  $buyer_arr[$rows[csf('buyer_id')]];
                  }
                  ?>
                </p>
              </td>
              <td width="140" align="center"><? echo $rows[csf("job_no")]; ?></td>
              <td width="140"><p><? echo $rows[csf('sales_booking_no')]; ?></p></td>
              <td width="100" align="center"><p><? if ($rows[csf('within_group')] == 1) echo "Yes"; else echo "No"; ?></p></td>
              <td width="" align="center">
                <?
                $uom= ""; $uom_arr = array();
                $uom_arr =  array_unique(explode(",", $rows[csf('order_uom')]));
                foreach ($uom_arr as $val)
                {
                  $uom .= $unit_of_measurement[$val].",";
                }
                echo chop($uom,",");
                ?>
              </td>
            </tr>
            <?
            $i++;
          }
      ?>
    </table>
    </div>
  </div>
  <?

}


if($action=="dtls_part_html_sales_row")
{
  $data_ex=explode("_",$data);
  $company_id=$data_ex[3];
  $sales_order_no=$data_ex[1];
  $buyer_id=$data_ex[4];

  //$sql="select b.job_no,b.id,b.style_ref_no, a.yarn_count_id,a.composition_id,a.composition_perc,a.color_id,a.yarn_type,a.cons_qty as qnty from fabric_sales_order_yarn_dtls a,fabric_sales_order_mst b where a.mst_id = b.id and a.mst_id = '".$data_ex[0]."' and a.status_active = 1 and a.is_deleted = 0 ";
  $sql = " select b.job_no,b.id,b.style_ref_no,b.sales_booking_no, a.yarn_count_id,a.composition_id,a.composition_perc,a.color_id,a.yarn_type,a.cons_qty as qnty
 from fabric_sales_order_mst b left join fabric_sales_order_yarn_dtls a on a.mst_id = b.id and a.status_active = 1 and a.is_deleted = 0
 where b.id = '".$data_ex[0]."' and b.status_active = 1 and b.is_deleted = 0 ";

 // echo $sql;die;
  $sql_result=sql_select($sql);
  $i=$data_ex[2];
  $k=1;
  foreach($sql_result as $row)
  {
    ?>
    <tr class="general" id="tr_<? echo $i; ?>" >
      <td align="center" >
      <input type="text" name="txtjobno_<? echo $i; ?>" id="txtjobno_<? echo $i; ?>" class="text_boxes" style="width:75px;"  value="<? echo $row[csf("job_no")]?>" readonly />
      <input type="hidden" id="txtjobid_<? echo $i; ?>" name="txtjobid_<? echo $i; ?>" style="width:100px;" value="<? echo $row[csf("id")]?>">
      <input type="hidden" name="txtrowid_<? echo $i; ?>" id="txtrowid_<? echo $i; ?>" class="text_boxes" value="" style="width:70px;" />
      <input type="hidden" name="hiderow_<? echo $i; ?>" id="hiderow_<? echo $i; ?>" class="text_boxes" value="0" style="width:70px;" />
      </td>
      <td>
      <input type="text" name="txtwono_<? echo $i; ?>" id="txtwono_<? echo $i; ?>" class="text_boxes"  style="width:75px;" value="<? echo $row[csf("sales_booking_no")]; ?>"  readonly disabled/>
      <input type="hidden" id="txtwoid_<? echo $i; ?>" name="txtwoid_<? echo $i; ?>" value="" style="width:100px;">
      </td>
      <td>
      <?
        echo create_drop_down( "cbobuyername_".$i, 90, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='".$company_id."' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select --", $buyer_id, "",1 );
      ?>
      </td>
      <td align="center"><input type="text" name="txtstyleno_<? echo $i; ?>" id="txtstyleno_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf("style_ref_no")]; ?>" style="width:75px;" readonly disabled /></td>
      <td align="center">
        <? $color_name =  ($row[csf("color_id")]) ? $color_arr[$row[csf("color_id")]]: "GREY";?>
        <input type="text" name="txtyarncolor[]" id="txtyarncolor_<? echo $i; ?>" class="text_boxes" onFocus="add_auto_complete( <? echo $i; ?> )" value="<? echo $color_name;  ?>" style="width:75px;" />
      </td>
      <td align="center">
      <?
        echo create_drop_down( "cbocount_".$i, 70, "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count",1,"-- Select --", $row[csf("yarn_count_id")], "","","" );
      ?>
      </td>
      <td align="center">
      <?
        echo create_drop_down( "cbocompone_".$i, 100, $composition,"", 1, "-- Select --", $row[csf("composition_id")], "","","","","",$ommitComposition );
      ?>
      </td>
      <td>
        <? $composition_perc = ($row[csf("composition_perc")])? $row[csf("composition_perc")]:"100";?>
        <input type="text" name="txtpacent_<? echo $i; ?>" id="txtpacent_<? echo $i; ?>" class="text_boxes" value="<? echo $composition_perc; ?>" style="width:40px;" />
      </td>
      <td>
      <?
        echo create_drop_down( "cbotype_".$i, 100, $yarn_type,"", 1, "-- Select --", $row[csf("yarn_type")], "","","","","",$ommitYarnType );
      ?>
      </td>
      <td>
      <?
        echo create_drop_down( "cbouom_".$i, 60, $unit_of_measurement,"", 1, "-- Select--", 12, "",1 );
      ?>
      </td>
      <td title="<? echo number_format($row[csf("qnty")],4,'.',''); ?>">
          <input type="text" id="reqqnty_<? echo $i; ?>" name="reqqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:55px" value="<? echo number_format($row[csf("qnty")],4,'.',''); ?>" onKeyUp="calculate_amount(<? echo $i; ?>)" />
          <input type="hidden" id="hiddenreqqnty_<? echo $i; ?>" name="hiddenreqqnty_<? echo $i; ?>" value="<? echo number_format($row[csf("qnty")],4,'.',''); ?>" />
      </td>
      <td>
        <input type="text" name="txtrate_<? echo $i; ?>" id="txtrate_<? echo $i; ?>" class="text_boxes_numeric" value="<??>"  style="width:40px;" onKeyUp="calculate_amount(<? echo $i; ?>)" />
        <input type="hidden" name="hiddentxtrate_<? echo $i; ?>" id="hiddentxtrate_<? echo $i; ?>" value="<??>" />
      </td>
      <td><input type="text" name="txtamount_<? echo $i; ?>" id="txtamount_<? echo $i; ?>" class="text_boxes_numeric" value="<??>" style="width:50px;" readonly /></td>
      <td><input class="datepicker" type="text" style="width:65px;" name="txtyarndate_<? echo $i; ?>" id="txtyarndate_<? echo $i; ?>" placeholder="Select Date" /></td>
      <td><input type="text" name="txtremarks_<? echo $i; ?>" id="txtremarks_<? echo $i; ?>" class="text_boxes" value="" style="width:110px;" /></td>
            <td><input type="button" id="decrease_<? echo $i; ?>" name="decrease_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deletebreak_down_tr(<? echo $i; ?>);" /></td>
    </tr>
    <?
    $i++;

  }
  ?>

    <tr class="general" id="tr_<? echo $i; ?>">
        <td align="center">
        <input type="text" name="txtjobno_<? echo $i; ?>" id="txtjobno_<? echo $i; ?>" class="text_boxes" value="" style="width:75px;" placeholder="Double Click For Job" onDblClick="openmypage_job(<? echo $i; ?>)" readonly/>
        <input type="hidden" id="txtjobid_<? echo $i; ?>" name="txtjobid_<? echo $i; ?>" style="width:100px;">
        <input type="hidden" name="txtrowid_<? echo $i; ?>" id="txtrowid_<? echo $i; ?>" class="text_boxes" value="" style="width:70px;" />
        <input type="hidden" name="hiderow_<? echo $i; ?>" id="hiderow_<? echo $i; ?>" class="text_boxes" value="1" style="width:70px;" />
        </td>
        <td>
        <input type="text" name="txtwono_<? echo $i; ?>" id="txtwono_<? echo $i; ?>" class="text_boxes" value="" style="width:75px;" readonly disabled />
        <input type="hidden" id="txtwoid_<? echo $i; ?>" name="txtwoid_<? echo $i; ?>" style="width:100px;">
        </td>
        <td>
        <?
            echo create_drop_down( "cbobuyername_".$i, 90, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='".$company_name."' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select --", 0, "" );
        ?>
        </td>
        <td align="center"><input type="text" name="txtstyleno_<? echo $i; ?>" id="txtstyleno_<? echo $i; ?>" class="text_boxes" value="" style="width:75px;" /></td>
        <td align="center"><input type="text" name="txtyarncolor[]" id="txtyarncolor_<? echo $i; ?>" class="text_boxes" value="GREY" onFocus="add_auto_complete( <? echo $i; ?> )" style="width:75px;"/></td>
        <td align="center">
        <?
            echo create_drop_down( "cbocount_".$i, 70, "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count",1,"-- Select --", 0, "",0,"" );
        ?>
        </td>
        <td align="center">
        <?
            echo create_drop_down( "cbocompone_".$i, 100, $composition,"", 1, "-- Select --", 0, "",0,"","","",$ommitComposition );
        ?>
        </td>
        <td><input type="text" name="txtpacent_<? echo $i; ?>" id="txtpacent_<? echo $i; ?>" class="text_boxes" value="100" style="width:40px;" /></td>
        <td>
        <?
            echo create_drop_down( "cbotype_".$i, 100, $yarn_type,"", 1, "-- Select --", $row[csf("type_id")], "",$disabled,"","","",$ommitYarnType );
        ?>
        </td>
        <td>
        <?
            echo create_drop_down( "cbouom_".$i, 60, $unit_of_measurement,"", 1, "-- Select--", 12, "",1 );
        ?>
        </td>
        <td title="<? echo 'Previous Recv-'.$prev_recv_qty;?>">
            <input type="text" id="reqqnty_<? echo $i; ?>" name="reqqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:55px" onKeyUp="calculate_amount(<? echo $i; ?>)" />
            <input type="hidden" id="hiddenreqqnty_<? echo $i; ?>" name="hiddenreqqnty_<? echo $i; ?>" value=""  />
        </td>
        <td>
            <input type="text" name="txtrate_<? echo $i; ?>" id="txtrate_<? echo $i; ?>" class="text_boxes_numeric" value="" style="width:40px;" onKeyUp="calculate_amount(<? echo $i; ?>)" />
            <input type="hidden" name="hiddentxtrate_<? echo $i; ?>" id="hiddentxtrate_<? echo $i; ?>" value="" />
        </td>
        <td><input type="text" name="txtamount_<? echo $i; ?>" id="txtamount_<? echo $i; ?>" class="text_boxes_numeric" value="" style="width:50px;" readonly /></td>
        <td><input class="datepicker" type="text" style="width:65px;" name="txtyarndate_<? echo $i; ?>" id="txtyarndate_<? echo $i; ?>" placeholder="Select Date" /></td>
        <td><input type="text" name="txtremarks_<? echo $i; ?>" id="txtremarks_<? echo $i; ?>" class="text_boxes" value="" style="width:110px;" /></td>
        <td><input type="button" id="decrease_<? echo $i; ?>" name="decrease_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deletebreak_down_tr(<? echo $i; ?>);" /></td>
    </tr>
   <?

}
?>

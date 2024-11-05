<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_department")
{
	echo create_drop_down( "cbo_department", 120, "select a.id,a.department_name from  lib_department a, lib_division b where b.id=a.division_id and a.status_active =1 and a.is_deleted=0 and b.company_id='$data' order by department_name","id,department_name", 1, "-- Select --", $selected,"load_drop_down( 'requires/general_item_issue_report_controller', this.value , 'load_drop_down_section', 'section_td' );",0 );
	exit();
}

if ($action=="load_drop_down_section")
{
	echo create_drop_down( "cbo_section", 110, "select id,section_name from lib_section where status_active =1 and is_deleted=0 and department_id=$data order by section_name","id,section_name", 1, "-- Select --", $selected, "",0 );
	exit();
}

if ($action=="load_drop_down_floor")
{
    echo create_drop_down( "cbo_floor", "110", "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.company_id=$data and b.status_active=1 and b.is_deleted=0  group by a.id, a.floor_name order by a.floor_name","id,floor_name", 1, "-- Select --", 0, "",0 );
    exit();
}


if($action=="item_group_such_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	//echo $style_id;die;

	?>
    <script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		
    	function check_all_data() 
		{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) 
			{
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
				
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
		
		function js_set_value( strCon ) 
		{
			//alert(strCon);
            var splitSTR = strCon.split("_");
            var str = splitSTR[0];
            var selectID = splitSTR[1];
            var selectDESC = splitSTR[2];
            
            toggle( document.getElementById( 'tr_' + str), '#FFFFCC' );
            
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
            var id = ''; var name = ''; var job = ''; var num='';
            for( var i = 0; i < selected_id.length; i++ ) {
                id += selected_id[i] + ',';
                name += selected_name[i] + ',';
            }
            id 		= id.substr( 0, id.length - 1 );
            name 	= name.substr( 0, name.length - 1 ); 
            //alert(num);
            $('#txt_selected_id').val( id );
            $('#txt_selected').val( name ); 
		}
		
    </script>
    <?
	$company=str_replace("'","",$company);
	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
	$sql="SELECT id,item_name from  lib_item_group where item_category in($cbo_item_category_id) and status_active=1 ";
	//echo $sql; die;
	$arr=array();
	echo create_list_view("list_view", "Item Group","250","300","300",0, $sql , "js_set_value", "id,item_name", "", 1, "0", $arr, "item_name", "","setFilterGrid('list_view',-1)","0","",1);
		
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}

if ($action=="item_account_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data);  
	?>	
    <script>
	    var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
	 
        function check_all_data()
        {
            var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
            // alert (tbl_row_count);
            tbl_row_count = tbl_row_count - 1;

            for( var i = 1; i <= tbl_row_count; i++ ) {
                    eval($('#tr_'+i).attr("onclick"));  
            }
        }
		
	    function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
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
            $('#item_account_id').val( id );
            $('#item_account_val').val( ddd );
        } 
		  
		  
	</script>
    <input type="hidden" id="item_account_id" />
    <input type="hidden" id="item_account_val" />
 	<?
 	$group_cond = "";
 	if($data[2] !=""){$group_cond=" and item_group_id in($data[2])";}
	$itemgroupArr = return_library_array("SELECT id,item_name from  lib_item_group where status_active=1 ","id","item_name");
	$supplierArr = return_library_array("SELECT id,supplier_name from lib_supplier where status_active=1 ","id","supplier_name");
		
	 $sql="SELECT id,item_account,item_category_id,item_group_id,item_description,supplier_id from product_details_master where company_id=$data[0]and item_category_id in ($data[1]) $group_cond and status_active=1 "; 
	$arr=array(1=>$item_category,2=>$itemgroupArr,4=>$supplierArr);
	echo  create_list_view("list_view", "Item Account,Item Category,Item Group,Item Description,Supplier,Product ID", "70,110,150,150,100,70","780","400",0, $sql , "js_set_value", "id,item_description", "", 0, "0,item_category_id,item_group_id,0,supplier_id,0", $arr , "item_account,item_category_id,item_group_id,item_description,supplier_id,id", "",'setFilterGrid("list_view",-1);','0,0,0,0,0,0','',1) ;
	exit();
}

if($action=="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_department=str_replace("'","",$cbo_department);
	$cbo_section=str_replace("'","",$cbo_section);
	$cbo_floor=str_replace("'","",$cbo_floor);

    //library array-------------------
    $companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
    $userArr = return_library_array("select id,user_full_name from user_passwd","id","user_full_name");
    $departArr = return_library_array("select id,department_name from lib_department where status_active=1 and is_deleted=0","id","department_name");
    $sectionArr = return_library_array("select id,section_name from lib_section where status_active=1 and is_deleted=0","id","section_name");
    $lineArr = return_library_array("select id,line_name from lib_sewing_line where status_active=1 and is_deleted=0 and company_name=$cbo_company_name","id","line_name");
    $floorArr = return_library_array("select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.company_id=$cbo_company_name and b.status_active=1 and b.is_deleted=0  group by a.id, a.floor_name order by a.floor_name","id","floor_name");
    $groupArr = return_library_array("select id,item_name from lib_item_group  where status_active=1","id","item_name");
	$supplierArr = return_library_array("SELECT id,supplier_name from lib_supplier where status_active=1 ","id","supplier_name");
    $buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");

    $sql_cond = "";
	if ($cbo_item_category==''){
        $category = array_keys($general_item_category);
        $sql_cond .= " and b.item_category in (".implode(',',$category).")";
    }else{
        $sql_cond .= " and b.item_category in ($cbo_item_category)";
    }
	if ($cbo_department > 0)
        $sql_cond .=" and b.department_id=$cbo_department";
	if ($cbo_section > 0)
        $sql_cond .=" and b.section_id=$cbo_section";
    if ($cbo_floor > 0)
        $sql_cond .=" and b.production_floor=$cbo_floor";

    if($item_account_id!=""){ $sql_cond.=" and b.prod_id in ($item_account_id)"; }
    if($item_group_id!=''){ $sql_cond.=" and c.item_group_id in($item_group_id)"; }
	if($db_type==0)
	{
		if($from_date != '' && $to_date != '' )
            $sql_cond .= " and b.transaction_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
 	}
	if($db_type==2)
	{
		if( $from_date != '' && $to_date != '' )
            $sql_cond .= " and b.transaction_date  between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";
 	
	}

    $sql_issue = sql_select("SELECT a.id, a.req_no, b.production_floor as floor_id, b.machine_id, b.machine_category, b.department_id, b.section_id, b.inserted_by, a.issue_number,a.attention , to_char(a.issue_date, 'dd-mm-YYYY') as issue_date,b.cons_rate, b.cons_quantity, b.cons_uom, b.item_category, b.line_id, b.remarks, c.item_description, c.item_group_id,a.issue_purpose,a.knit_dye_company, a.knit_dye_source
    from inv_issue_master a, inv_transaction b, product_details_master c 
    where a.id = b.mst_id and b.transaction_type = 2 and b.prod_id = c.id and b.company_id = $cbo_company_name and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0  $sql_cond 
    order by a.id desc");
  
    if(count($sql_issue) == 0){
        echo "<h4>Transaction not found!</h4>";
        die();
    }

    $dataArr = []; $issue_id = []; $machine_id = [];
    foreach ($sql_issue as $k => $v){

        $key = $v[csf('id')]."*".$v[csf('item_description')]."*".$v[csf('cons_uom')]."*".$v[csf('item_category')];
        $dataArr[$key]['challan'] = $v[csf('req_no')];
        $dataArr[$key]['issue_no'] = $v[csf('issue_number')];
        $dataArr[$key]['date'] = $v[csf('issue_date')];
        $dataArr[$key]['department'] = $departArr[$v[csf('department_id')]];
        $dataArr[$key]['section'] = $sectionArr[$v[csf('section_id')]];
        $dataArr[$key]['floor'] = $floorArr[$v[csf('floor_id')]];
        $dataArr[$key]['line'] = $lineArr[$v[csf('line_id')]];
        $dataArr[$key]['item_name'] = $v[csf('item_description')];
        $dataArr[$key]['uom'] = $unit_of_measurement[$v[csf('cons_uom')]];
        $dataArr[$key]['qty'] += $v[csf('cons_quantity')];
        $dataArr[$key]['rate'] += $v[csf('cons_rate')];
        $dataArr[$key]['user'] = $userArr[$v[csf('inserted_by')]];
        $dataArr[$key]['attention'] = $v[csf('attention')];
        $dataArr[$key]['knit_dye_source'] = $v[csf('knit_dye_source')];
        $dataArr[$key]['machine'] = $v[csf('machine_id')];
        $dataArr[$key]['machine_cat'] = $v[csf('machine_category')];
        $dataArr[$key]['issue_purpose'] = $v[csf('issue_purpose')];
        $dataArr[$key]['knit_dye_company'] = $v[csf('knit_dye_company')];
        $dataArr[$key]['remarks'] = $v[csf('remarks')];
        $dataArr[$key]['category'] = $general_item_category[$v[csf('item_category')]];
        $dataArr[$key]['item_group'] = $groupArr[$v[csf('item_group_id')]];
        $issue_id[$v[csf('id')]] = $v[csf('id')];
        $machine_id[$v[csf('machine_id')]] = $v[csf('machine_id')];
    }
    $issue_rtn = [];
    if(count($issue_id) > 0){
        $issue_id = array_chunk(array_unique($issue_id), 900);
        $rtn_cond = "";
        foreach ($issue_id as $k => $v){
            if($k == 0) {
                $rtn_cond .= " b.issue_id in (" . implode(',', $v) . ") ";
            }else{
                $rtn_cond .= " or b.issue_id in (".implode(',', $v).") ";
            }
        }
        $sql_issue_rtn = sql_select("select b.issue_id, b.cons_quantity, b.cons_uom, b.item_category, c.item_description  from inv_transaction b, product_details_master c where b.transaction_type = 4 and b.prod_id = c.id  and b.status_active = 1 and b.is_deleted = 0  and ($rtn_cond)");
        foreach ($sql_issue_rtn as $v) {
            $key = $v[csf('issue_id')]."*".$v[csf('item_description')]."*".$v[csf('cons_uom')]."*".$v[csf('item_category')];
            $issue_rtn[$key]['qty'] += $v[csf('cons_quantity')];
        }
    }
    $machine_arr = [];
    if(count($machine_id) > 0){
        $machine_id = array_chunk(array_unique($machine_id), 900);
        $machine_cond = "";
        foreach ($machine_id as $k => $v){
            if($k == 0) {
                $machine_cond .= " id in (" . implode(',', $v) . ") ";
            }else{
                $machine_cond .= " or id in (".implode(',', $v).") ";
            }
        }
        $machine_arr = return_library_array("select id, machine_no from lib_machine_name where status_active = 1 and is_deleted = 0  and ($machine_cond)", "id", "machine_no");
    }



	ob_start();	
	?>
	<div style="width:100%;"> 
         <fieldset style="width:2170px; margin-left: 10px;">
            <table style="border:none;" width="1650" border="0" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" align="left">
                <tr style="border:none;">
                    <td colspan="14" align="center" style="border:none; font-size:22px;font-weight:bold">
                        <strong>
                            <? echo $companyArr[str_replace("'","",$cbo_company_name)]; ?>
                        </strong>
                    </td>
                </tr>
                <tr class="form_caption" style="border:none;">
                    <td colspan="15" align="center" style="border:none;font-size:18px; font-weight:bold" ><strong>General Item Issue Report</strong></td>
                </tr>
                <tr style="border:none;">
                    <td colspan="15" align="center" style="border:none;font-size:14px; font-weight:bold">
                        <? if($from_date!="" || $to_date!="")echo "Date : ".change_date_format($from_date)."   to   ".change_date_format($to_date)."" ;?>
                    </td>
                </tr>
            </table>
            <table width="2140" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_2" align="left" >
               <thead>
                    <tr>
                        <th style="padding: 1px 2px;" width="40">SL No.</th>
                        <th style="padding: 1px 2px;" width="80">SR No.</th>
                        <th style="padding: 1px 2px;" width="100">Issue No</th>
                        <th style="padding: 1px 2px;" width="80">Issue Date</th>
                        <th style="padding: 1px 2px;" width="80">Purpose</th>
                        <th style="padding: 1px 2px;" width="110">Party</th>
                        <th style="padding: 1px 2px;" width="110">Department</th>
                        <th style="padding: 1px 2px;" width="100">Section</th>
                        <th style="padding: 1px 2px;" width="90">Line</th>
                        <th style="padding: 1px 2px;" width="90">Floor</th>
                        <th style="padding: 1px 2px;" width="100">Machine Name</th>
                        <th style="padding: 1px 2px;" width="100">Machine Category</th>
                        <th style="padding: 1px 2px;" width="110">Categories</th>
                        <th style="padding: 1px 2px;" width="100">Item Group</th>
                        <th style="padding: 1px 2px;" width="130">Item Name</th>
                        <th style="padding: 1px 2px;" width="60">UOM</th>
                        <th style="padding: 1px 2px;" width="90">Quantity</th>
                        <th style="padding: 1px 2px;" width="90">Rate</th>
                        <th style="padding: 1px 2px;" width="100">Value TK.</th>
                        <th style="padding: 1px 2px;" width="120">Issue by</th>
                        <th style="padding: 1px 2px;" width="100">Attention</th>
                        <th style="padding: 1px 2px;">Remarks</th>
                    </tr>
               </thead>
            </table>
             <div style="width:2170px; overflow-y: scroll; max-height:290px;" id="scroll_body">
                 <table width="2140" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body_id" align="left">
                     <tbody>
                     <?
                     $i = 1;
                     foreach ($dataArr as $key => $row){
                         $qty = $row['qty'] - $issue_rtn[$key]['qty'];
                         if($qty > 0){
                             if ($i%2==0)
                                 $bgcolor="#E9F3FF";
                             else
                                 $bgcolor="#FFFFFF";

                                if($row['issue_purpose']==1){
                                    $party= $companyArr[$row['knit_dye_company']];
                                }
                                else if($row['knit_dye_source']==3 && $row['issue_purpose']==2 ){
                                    $party=$supplierArr[$row['knit_dye_company']];
                                }
                                else if($row['knit_dye_source']==3 && $row['issue_purpose']==15 ){
                                    $party=$buyer_library[$row['knit_dye_company']];
                                }
                                else if($row['knit_dye_source']==3){
                                    $party=$supplierArr[$row['knit_dye_company']];
                                }
                                else{
                                    $party= $companyArr[$row['knit_dye_company']];
                                }

                             ?>
                             <tr bgcolor="<?=$bgcolor?>">
                                 <td style="font-size: 12.5px; padding: 1px 2px;" valign="middle" width="40" align="center"><?=$i?></td>
                                 <td style="font-size: 12.5px; padding: 1px 2px;" valign="middle" width="80" align="center"><?=$row['challan']?></td>
                                 <td style="font-size: 12.5px; padding: 1px 2px;" valign="middle" width="100" align="center"><?=$row['issue_no']?></td>
                                 <td style="font-size: 12.5px; padding: 1px 2px;" valign="middle" width="80" align="center"><?=$row['date']?></td>
                                 <td style="font-size: 12.5px; padding: 1px 2px;" valign="middle" width="80"><?=$general_issue_purpose[$row['issue_purpose']]?></td>
                                 <td style="font-size: 12.5px; padding: 1px 2px;" valign="middle" width="110"><?=$party;?></td>
                                 <td style="font-size: 12.5px; padding: 1px 2px;" valign="middle" width="110"><?=$row['department']?></td>
                                 <td style="font-size: 12.5px; padding: 1px 2px;" valign="middle" width="100"><?=$row['section']?></td>
                                 <td style="font-size: 12.5px; padding: 1px 2px;" valign="middle" width="90"><?=$row['line']?></td>
                                 <td style="font-size: 12.5px; padding: 1px 2px;" valign="middle" width="90"><?=$row['floor']?></td>
                                 <td style="font-size: 12.5px; padding: 1px 2px;" valign="middle" width="100"><?=$machine_arr[$row['machine']]?></td>
                                 <td style="font-size: 12.5px; padding: 1px 2px;" valign="middle" width="100"><?=$machine_category[$row['machine_cat']]?></td>
                                 <td style="font-size: 12.5px; padding: 1px 2px;" valign="middle" width="110"><?=$row['category']?></td>
                                 <td style="font-size: 12.5px; padding: 1px 2px;" valign="middle" width="100"><p style="word-break: normal;"><?=$row['item_group']?></p></td>
                                 <td style="font-size: 12.5px; padding: 1px 2px;" valign="middle" width="130"><p style="word-break: normal;"><?=$row['item_name']?></p></td>
                                 <td style="font-size: 12.5px; padding: 1px 2px;" valign="middle" width="60" align="center"><?=$row['uom']?></td>
                                 <td style="font-size: 12.5px; padding: 1px 2px;" valign="middle" width="90" align="right"><?=number_format($qty, 2)?></td>
                                 <td style="font-size: 12.5px; padding: 1px 2px;" valign="middle" width="90" align="right"><?=number_format( $row['rate'], 2)?></td>
                                 <td style="font-size: 12.5px; padding: 1px 2px;" valign="middle" width="100" align="right"><?$value = $qty*$row['rate'] ; echo number_format($value, 2)?></td>
                                 <td style="font-size: 12.5px; padding: 1px 2px;" valign="middle" width="120"><p style="word-break: normal;"><?=$row['user']?></p></td>
                                 <td style="font-size: 12.5px; padding: 1px 2px;" valign="middle" width="100"><p style="word-break: normal;"><?=$row['attention']?></p></td>
                                 <td style="font-size: 12.5px; padding: 1px 2px;" valign="middle"><p style="word-break: normal;"><?=$row['remarks']?></p></td>
                             </tr>
                     <?
                            $i++;
                            $total += $qty;
                            $total_rate += $row['rate'];
                            $total_value += $value;
                         }
                     }
                     ?>
                     </tbody>
                    </table>
                 </div>
                     <table width="2140" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" align="left">
                        <tfoot>
                            <tr>
                                <th  style="padding: 1px 2px;" width="40"></th>
                                <th  style="padding: 1px 2px;" width="80"></th>
                                <th  style="padding: 1px 2px;" width="100"></th>
                                <th  style="padding: 1px 2px;" width="80"></th>
                                <th  style="padding: 1px 2px;" width="80"></th>
                                <th  style="padding: 1px 2px;" width="110"></th>
                                <th  style="padding: 1px 2px;" width="110"></th>
                                <th  style="padding: 1px 2px;" width="100"></th>
                                <th  style="padding: 1px 2px;" width="90"></th>
                                <th  style="padding: 1px 2px;" width="90"></th>
                                <th  style="padding: 1px 2px;" width="100"> </th>
                                <th  style="padding: 1px 2px;" width="100"> </th>
                                <th  style="padding: 1px 2px;" width="110"></th>
                                <th  style="padding: 1px 2px;" width="100"> </th>
                                <th  style="padding: 1px 2px;" width="130"> </th>                     
                                <th  align="right" width="60"  style="font-size: 12.5px; padding: 1px 2px;"><strong>Total</strong></th>
                                <th align="right" width="90" id="prod_total" style="font-size: 12.5px; padding: 1px 2px;"><strong><?=number_format($total, 2)?></strong></th>
                                <th align="right" width="90" style="font-size: 12.5px; padding: 1px 2px;"><strong><?=number_format($total_rate, 2)?></strong></th>
                                <th align="right" width="100" id="value_prod_bal_qty" style="font-size: 12.5px; padding: 1px 2px;"><strong><?=number_format($total_value, 2)?></strong></th>
                                <th  style="padding: 1px 2px;" width="120"></th>
                                <th  style="padding: 1px 2px;" width="100"></th>
                                <th  style="padding: 1px 2px;"></th>
                            </tr>
                        </tfoot>
                    </table>
                
            
        </fieldset>
   </div>
     <?

	$html = ob_get_contents();
	ob_clean();
	foreach (glob("$user_id*.xls") as $filename) {
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}

	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename"; 
	exit();	
}

?>
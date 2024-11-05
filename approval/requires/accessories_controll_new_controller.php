<?
/**
 * Created by Mohammad Shafiqur Rahman.
 * User: shafiq-sumon
 * Date: 5/29/2018
 * Time: 12:54 PM
 */

header('Content-type:text/html; charset=utf-8');
session_start();
include('../../includes/common.php');
$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//---------------------------------------------------- Start---------------------------------------------------------------------------

if ($action=="load_drop_down_buyer")
{
    echo create_drop_down( "cbo_buyer_id", 110, " select a.id, a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id and b.tag_company in($data) and a.status_active =1 and a.is_deleted=0 group by a.id, a.buyer_name order by a.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0);
    exit();
}

if ($action=="style_popup")
{
    echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
    extract($_REQUEST);
    $data=explode('_',$data);
    //print_r ($data);
    ?>
    <script>
        var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();

        function toggle( x, origColor ) {
            var newColor = 'yellow';
            if ( x.style ) {
                x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
            }
        }
        function check_all_data()
        {
            var row_num=$('#list_view tr').length-1;
            for(var i=1;  i<=row_num;  i++)
            {
                $("#tr_"+i).click();
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
            $('#txt_po_id').val( id );
            $('#txt_po_val').val( ddd );
        }

    </script>


    <input type="hidden" id="txt_po_id" />
    <input type="hidden" id="txt_po_val" />
    <?
    if ($data[0]==0) $company_name=""; else $company_name="company_name='$data[0]'";
    if ($data[1]==0) $buyer_name=""; else $buyer_name=" and buyer_name='$data[1]'";
    if($db_type==0)
    {
        $year_field="YEAR(insert_date) as year";
        $year_cond=" and YEAR(insert_date)=".$data[2];
    }
    else
    {
        $year_field="to_char(insert_date,'YYYY') as year";
        $year_cond=" and to_char(insert_date,'YYYY')=".$data[2];
    }


    $sql ="select id,style_ref_no,job_no_prefix_num as job_prefix,$year_field from wo_po_details_master where $company_name $buyer_name $year_cond";
    echo create_list_view("list_view", "Style Ref. No.,Job No,Year","200,100,100","450","310",0, $sql , "js_set_value", "id,style_ref_no", "", 1, "0", $arr, "style_ref_no,job_prefix,year", "","setFilterGrid('list_view',-1)","0","",1) ;
    exit();
}

if($action == "job_popup")
{
    echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
    extract($_REQUEST);
    $data=explode('_',$data);
    //print_r ($data);
    ?>
    <script>
        var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();

        function toggle( x, origColor ) {
            var newColor = 'yellow';
            if ( x.style ) {
                x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
            }
        }
        function check_all_data()
        {
            var row_num=$('#list_view tr').length-1;
            for(var i=1;  i<=row_num;  i++)
            {
                $("#tr_"+i).click();
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
            $('#txt_job').val( ddd );
            $('#txt_job_id').val( id );
        }

    </script>


    <input type="hidden" id="txt_job" />
    <input type="hidden" id="txt_job_id" />
    <?
    if ($data[0]==0) $company_name=""; else $company_name="company_name='$data[0]'";
    if ($data[1]==0) $buyer_name=""; else $buyer_name=" and buyer_name='$data[1]'";
    if($db_type==0)
    {
        $year_field="YEAR(insert_date) as year";
        $year_cond=" and YEAR(insert_date)=".$data[2];
    }
    else
    {
        $year_field="to_char(insert_date,'YYYY') as year";
        $year_cond=" and to_char(insert_date,'YYYY')=".$data[2];
    }


    $sql ="select id,job_no,insert_date from wo_po_details_master where $company_name $buyer_name $year_cond";
    echo create_list_view("list_view", "Job No,Year","200,100,150","450","310",0, $sql , "js_set_value", "id,job_no,insert_date", "", 1, "0", $arr, "job_no,insert_date", "","setFilterGrid('list_view',-1)","0","",1) ;
    exit();
}

if ($action=="order_no_popup")
{
    echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
    extract($_REQUEST);
    $data=explode('_',$data);
    //print_r($data);die;
    ?>
    <script>
        var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();

        function toggle( x, origColor ) {
            var newColor = 'yellow';
            if ( x.style ) {
                x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
            }
        }
        function check_all_data()
        {
            var row_num=$('#list_view tr').length-1;
            for(var i=1;  i<=row_num;  i++)
            {
                $("#tr_"+i).click();
            }

        }

        function js_set_value(id)
        { //alert(id);
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
            $('#txt_po_id').val( id );
            $('#txt_po_val').val( ddd );
        }

    </script>
    <input type="hidden" id="txt_po_id" />
    <input type="hidden" id="txt_po_val" />
    <?
    $sql_cond="";
    if ($data[0]>0) $sql_cond=" and company_name=$data[0]";
    if ($data[1]>0) $sql_cond.=" and buyer_name=$data[1]";
    if ($data[2]!="") $sql_cond.=" and b.id in($data[2])";

    if($db_type==0)
    {
        $year_field="YEAR(b.insert_date) as year";
        $year_cond=" and YEAR(b.insert_date)=".$data[3];
    }
    else
    {
        $year_field="to_char(b.insert_date,'YYYY') as year";
        $year_cond=" and to_char(b.insert_date,'YYYY')=".$data[3];
    }

    $sql ="select distinct a.id,a.po_number,b.job_no_prefix_num as job_prefix,$year_field from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.status_active in(1,3) $sql_cond  $year_cond";
    //echo $sql;
    echo create_list_view("list_view", "Order Number,Job No, Year","350,90","580","310",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "po_number,job_prefix,year", "","setFilterGrid('list_view',-1)","0","",1) ;
    exit();
}

if ($action=="approve")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
	$con = connect();
	$approval_type=str_replace("'","",$approval_type);
	$data_string=str_replace("'","",$data_string);
	$all_order_id=str_replace("'","",$all_order_id);
	
	
	//echo $data_string;die;
	if($approval_type==2) $approval_status=1; else $approval_status=0;
	//echo "10** $approval_type";die;
	if($approval_type==2)
	{ //Approve Button
		//echo "select po_breakdown_id, approval_status from accessories_access_mst where status_active=1 and approval_status=0 and po_breakdown_id in($all_order_id)";die;
		/*$prev_acc_sql=sql_select("select id, po_breakdown_id, approval_status from accessories_access_mst where status_active=1 and po_breakdown_id in($all_order_id)");
		foreach($prev_acc_sql as $row)
		{
			$prev_data[$row[csf("po_breakdown_id")]]["id"]=$row[csf("id")];
			$prev_data[$row[csf("po_breakdown_id")]]["approval_status"]=$row[csf("approval_status")];
		}
		unset($prev_acc_sql);*/
		
		$prev_acc_item_sql=sql_select("select id, po_break_down_id, prod_id, approval_status from accessories_item_approval where po_break_down_id in($all_order_id)");
		foreach($prev_acc_item_sql as $row)
		{
			$prev_item_data[$row[csf("po_break_down_id")]][$row[csf("prod_id")]]["id"]=$row[csf("id")];
		}
		unset($prev_acc_item_sql);
		
		//$id = return_next_id( "id","accessories_access_mst", 1 ) ;
		//$field_array = "id,po_breakdown_id,approval_status,sew_quantity,is_deleted,status_active,inserted_by,insert_date";
		//$update_field_array = "approval_status*update_by*update_date";
        $data_string_ref=explode("__",$data_string);
        //print_r($data_string_ref);die;
        foreach ($data_string_ref as $key => $value) {
            $po_id_amount_array = explode("**",$value);
            $po_id_amt_arr[$po_id_amount_array[0]] = $po_id_amount_array[0];            
        }
        $selected_po_ids = implode(",",$po_id_amt_arr);

        $sql_item_dtls = "select a.id, a.po_breakdown_id, a.prod_id
		from order_wise_pro_details a 
        where a.po_breakdown_id in ($selected_po_ids) and a.status_active = 1 and a.entry_form in (25) and a.trans_type in(2)";
        //echo $sql_item_dtls;die;
        $result = sql_select($sql_item_dtls);
        foreach ($result as  $value) {
            $item_data_array[$value[csf("po_breakdown_id")]][$value[csf("prod_id")]] = $value[csf("prod_id")];
        }

        $id_item = return_next_id( "id","accessories_item_approval", 1 ) ;
        $field_array_accessories_approval = "id, po_break_down_id, prod_id, approval_status, approved_by, insert_by, insert_date";
        $update_field_item_array = "approval_status*update_by*update_date";
        $data_array_accesories_approval="";$update_id_item_arr=array();
        foreach ($item_data_array as $po_id => $po_value) 
		{
            foreach ($po_value as $prod_id => $prod_val) 
			{
				if($prev_item_data[$po_id][$prod_id]["id"])
				{
					$update_id_item_arr[]=$prev_item_data[$po_id][$prod_id]["id"];
					$update_data_item_array[$prev_item_data[$po_id][$prod_id]["id"]]=explode("*",("".$approval_status."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				}
				else
				{
					if($data_array_accesories_approval!="") $data_array_accesories_approval.=",";
					$data_array_accesories_approval .="(".$id_item.",".$po_id.",".$prod_id.",".$approval_status.",".$user_id.",".$user_id.",'".$pc_date_time."')";
					$id_item++;
				}
            }
        }
       //print_r($data_array_accesories_approval );die;
        //print_r($po_id_amt_arr);die;
		/*$data_array="";$update_id_arr=array();
		foreach($data_string_ref as $data_ref)
		{
            $data_ref_string=explode("**",$data_ref);
			//$all_po_id.=$data_ref_string[0].",";
			if($prev_data[$data_ref_string[0]]["id"] > 0)
			{
				if($prev_data[$data_ref_string[0]]["approval_status"] ==0 && $data_ref_string[2] > 0)
				{
					$update_id_arr[]=$data_ref_string[2];
					$update_data_array[$data_ref_string[2]]=explode("*",("".$approval_status."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				}
			}
			else
			{
				if($data_array!="") $data_array.=",";
				$data_array.="(".$id.",".$data_ref_string[0].",".$approval_status.",".$data_ref_string[1].",0,1,".$user_id.",'".$pc_date_time."')";
                $id++;
            }
		}
		$all_po_id=chop($all_po_id,",");*/
		//echo "10**$pc_date_time";die;
		
		$rID=$rID2=$rID3=$rID4=true;
		/*if($data_array!="")
		{
			$rID = sql_insert("accessories_access_mst", $field_array, $data_array, 0);
		}
		if(count($update_id_arr)>0)
		{
            $rID2=execute_query(bulk_update_sql_statement("accessories_access_mst","id",$update_field_array,$update_data_array,$update_id_arr));
		}*/
        //echo "10**insert into accessories_item_approval ($field_array_accessories_approval) values $data_array_accesories_approval";die;
        if($data_array_accesories_approval!="")
        {
            $rID3 = sql_insert("accessories_item_approval", $field_array_accessories_approval, $data_array_accesories_approval, 0);
        }
		if(count($update_id_item_arr)>0)
		{
            $rID4=execute_query(bulk_update_sql_statement("accessories_item_approval","id",$update_field_item_array,$update_data_item_array,$update_id_item_arr));
		}
        //echo "10**".$rID."==".$rID2."==".$rID3."==".$rID4;oci_rollback($con);die;
		if ($db_type == 0) 
		{
			if($rID && $rID2 && $rID3 && $rID4) 
			{
				mysql_query("COMMIT");
				echo "0**".$all_order_id."**".$approval_type;
			} 
			else 
			{
				mysql_query("ROLLBACK");
				echo "10**0".$rID."__".$rID2."__".$rID3;
			}
		} 
		else if ($db_type == 2 || $db_type == 1) 
		{
			if($rID && $rID2 && $rID3 && $rID4) 
			{
				oci_commit($con);
				echo "0**".$all_order_id."**".$approval_type;
			} 
			else 
			{
				oci_rollback($con);
				echo "10**0".$rID."__".$rID2."__".$rID3;
			}
		}
		disconnect($con);
		die;
	}
	else
	{ 
		//Unapprove Button
		$update_field_array = "approval_status*update_by*update_date";
        $data_string_ref=explode("__",$data_string);
        //$all_order_id;
        //print_r($data_string_ref);die;
		$data_array=$all_po_id="";
		foreach($data_string_ref as $data_ref)
		{
			$data_ref_string=explode("**",$data_ref);
			$all_po_id.=$data_ref_string[0].",";
			if($data_ref_string[2])
			{
				$update_id_arr[]=$data_ref_string[2];
				$update_data_array[$data_ref_string[2]]=explode("*",("".$approval_status."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
			}
        }
        
        $all_po_id=chop($all_po_id,",");
        //echo "select id from accessories_item_approval where po_break_down_id in($all_po_id)";die;
		$items_to_delete=0;
        if($all_po_id !="")
		{
            $approved_items = sql_select("select id from accessories_item_approval where po_break_down_id in($all_po_id)");
            foreach ($approved_items as $value) {
                $items_to_delete .= $value[csf("id")].",";
            }
            $items_to_delete = chop($items_to_delete,",");
        }
        $rID=true;
        //$rID=execute_query(bulk_update_sql_statement("accessories_access_mst","id",$update_field_array,$update_data_array,$update_id_arr),0);
        $rID2= execute_query("update accessories_item_approval set approval_status=$approval_status where id in($items_to_delete)",0);
		
		if ($db_type == 0) 
		{
			if($rID && $rID2) 
			{
				mysql_query("COMMIT");
				echo "0**".$all_order_id."**".$approval_type;
			} 
			else 
			{
				mysql_query("ROLLBACK");
				echo "10**0";
			}
		} 
		else if ($db_type == 2 || $db_type == 1) 
		{
			if($rID && $rID2) 
			{
				oci_commit($con);
				echo "0**".$all_order_id."**".$approval_type;
			} 
			else 
			{
				oci_rollback($con);
				echo "10**0";
			}
		}
		disconnect($con);
		die;
	}
}

//if ($action=="check_approval_status"){
//    echo 1;
//}

if ($action=="report_generate")// Item Group Wise Search.
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));

    $cbo_company=str_replace("'","",$cbo_company_id);
    $cbo_buyer=str_replace("'","",$cbo_buyer_id);
    $cbo_year=str_replace("'","",$cbo_year);
    $job_no=str_replace("'","",$job_no);
    $job_no_id=str_replace("'","",$job_no_id);
    $txt_style_id=str_replace("'","",$txt_style_id);
    $txt_style=str_replace("'","",$txt_style);
    $txt_order_no=str_replace("'","",$txt_order_no);
    $txt_order_id=str_replace("'","",$txt_order_no_id);
    $cbo_approval_type=str_replace("'","",$cbo_approval_type);

    $sql_cond="";
    if($cbo_company>0) $sql_cond.=" and a.company_name=$cbo_company";
    if($cbo_buyer>0) $sql_cond.=" and a.buyer_name=$cbo_buyer";
    if ($job_no_id!="") $sql_cond.=" and a.id in($job_no_id)";
    if ($txt_style!="") $sql_cond.=" and a.id in($txt_style)";
    if ($txt_order_id!="") $sql_cond.=" and b.id in($txt_order_id)";
	if($db_type==0)
	{
		if($cbo_year>0) $sql_cond.=" and year(a.insert_date)='$cbo_year'";
	}
	else
	{
		if($cbo_year>0) $sql_cond.=" and to_char(a.insert_date,'YYYY')='$cbo_year'";
	}

	$prev_acc_sql=sql_select("select b.po_break_down_id as po_breakdown_id, min(b.approval_status) as approval_status 
	from  accessories_item_approval b group by b.po_break_down_id");
	$prev_access_data=array();
	$prev_dtls_id=array();
	foreach($prev_acc_sql as $row)
	{
		$prev_access_data[$row[csf("po_breakdown_id")]]=$row[csf("approval_status")];
	}
	unset($prev_acc_sql);
	
	
    $sql = "select a.id, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id as po_id, b.po_number, (b.po_quantity*a.total_set_qnty) as issue_amt
    from wo_po_details_master a, wo_po_break_down b, order_wise_pro_details c 
    where a.job_no=b.job_no_mst and b.id=c.po_breakdown_id and c.entry_form=25 and c.trans_type=2 and a.status_active =1 and a.is_deleted=0 and b.status_active =1 
    and b.is_deleted=0 and c.status_active =1 and c.is_deleted=0 $sql_cond
    group by a.id, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id, b.po_number ,b.po_quantity,a.total_set_qnty";
    //echo $sql;//die;

    $company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
    $buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

    ob_start();
    ?>
    <div style="width:820px; margin: 0 auto;">

        <table width="820" cellspacing="0" cellpadding="0" border="0" rules="all"  >
            <tr class="form_caption">
                <td colspan="20" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
            </tr>
            <tr class="form_caption">
                <td colspan="20" align="center"><? echo $company_library[$cbo_company]; ?></td>
            </tr>
        </table>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" >
            <thead>
            <th width="30"></th>
            <th width="50">SL</th>
            <th width="150">Company</th>
            <th width="150">Buyer Name</th>
            <th width="150">Job No</th>
            <th width="80">Style Ref.</th>
            <th width="80">PO No.</th>
            <th>PO Qty (pcs)</th>
            </thead>
        </table>
        <div style="width:820px; overflow-y:scroll; max-height:350px;font-size:12px; overflow-x:hidden;" id="scroll_body">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table"  id="tbl_accessories_controll" >
                <tbody>
                <?

                $data_array=sql_select($sql);
                $i=1;
                foreach ($data_array as $selectResult)
                {
					if($prev_access_data[$selectResult[csf('po_id')]]!=1 && $cbo_approval_type==2)
					{
						if ($i%2==0)
                        $bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30" align="center">
                            <input type="checkbox" id="tbl_<? echo $i;?>" name="tbl[]" />
                            <input type="hidden" id="dtlsId_<? echo $i;?>" name="dtlsId[]" value="<? //echo $prev_dtls_id[$selectResult[csf('po_id')]]; ?>" />
                            </td>
							<td width="50" align="center"><? echo $i; ?></td>
							<td width="150"><p><? echo  $company_library[$selectResult[csf('company_name')]]; ?></p></td>
							<td width="150" id="buyer_name_<? echo $i;?>"><? echo $buyer_arr[$selectResult[csf('buyer_name')]]; ?> </td>
							<td width="150"><? echo $selectResult[csf('job_no')]; ?> </td>
							<td width="80"> <? echo $selectResult[csf('style_ref_no')]; ?>  </td>
							<td width="80" id="po_id_<? echo $i;?>" title="<? echo $selectResult[csf('po_id')] ?>"><? echo $selectResult[csf('po_number')]; ?></td>
							<td align="right" id="issue_amt_<? echo $i;?>"><? echo $selectResult[csf('issue_amt')]; ?></td>
						</tr>
						<?
						$i++;
					}
					else if($prev_access_data[$selectResult[csf('po_id')]]==1 && $cbo_approval_type==1)
					{
						if ($i%2==0)
                        $bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30" align="center">
                            <input type="checkbox" id="tbl_<? echo $i;?>" name="tbl[]" />
                            <input type="hidden" id="dtlsId_<? echo $i;?>" name="dtlsId[]" value="<? echo $prev_dtls_id[$selectResult[csf('po_id')]]; ?>" />
                            </td>
							<td width="50" align="center"><? echo $i; ?></td>
							<td width="150"><p><? echo  $company_library[$selectResult[csf('company_name')]]; ?></p></td>
							<td width="150" id="buyer_name_<? echo $i;?>"><? echo $buyer_arr[$selectResult[csf('buyer_name')]]; ?> </td>
							<td width="150"><? echo $selectResult[csf('job_no')]; ?> </td>
							<td width="80"> <? echo $selectResult[csf('style_ref_no')]; ?>  </td>
							<td width="80" id="po_id_<? echo $i;?>" title="<? echo $selectResult[csf('po_id')] ?>"> <? echo $selectResult[csf('po_id')]; ?></td>
							<td align="right" id="issue_amt_<? echo $i;?>"><? echo $selectResult[csf('issue_amt')]; ?></td>
						</tr>
						<?
						$i++;
					}
                }
                ?>

                </tbody>
                <tfoot>
                    <tr>
                        <th width="30" style="text-align:center">
                            <input type="checkbox" id="all_check" onClick="check_all('all_check')" />
                            <input type="hidden" name="hide_approval_type" id="hide_approval_type" value="<? echo $cbo_approval_type; ?>">
    
                        </th>
                        <th colspan="7" align="left"><input type="button" value="<? if($cbo_approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px; float:left;" onClick="submit_approved(<? echo $i; ?>,<? echo $cbo_approval_type; ?>)"/></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <?
    exit();
}

if($action=="show_dtls_list_view")
{
	$data_ref=explode("__",$data);
	$all_ord_id=$data_ref[0];
	$cbo_approval_type=$data_ref[1];
	//echo $cbo_approval_type;die;
	
	//echo $cbo_approval_type.jahid;
	
	/*$prev_access_data=array();
	$prev_acc_sql=sql_select("select id, po_breakdown_id, approval_status from accessories_access_mst where status_active=1");
	foreach($prev_acc_sql as $row)
	{
		$prev_access_data[$row[csf("po_breakdown_id")]]=$row[csf("approval_status")];
		$prev_dtls_id[$row[csf("po_breakdown_id")]]=$row[csf("id")];
	}
	unset($prev_acc_sql);*/
	
	$prev_acc_sql=sql_select("select b.po_break_down_id as po_breakdown_id, min(b.approval_status) as approval_status 
	from  accessories_item_approval b group by b.po_break_down_id");
	$prev_access_data=array();
	$prev_dtls_id=array();
	foreach($prev_acc_sql as $row)
	{
		$prev_access_data[$row[csf("po_breakdown_id")]]=$row[csf("approval_status")];
	}
	unset($prev_acc_sql);

    $sql = "select a.id, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id as po_id, b.po_number, sum(c.production_quantity) as issue_amt
from wo_po_details_master a, wo_po_break_down b, pro_garments_production_mst c
where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.production_type=4 and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and c.status_active =1 and c.is_deleted=0 and b.id in($all_ord_id)
group by   a.id, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.id, b.po_number";

    //echo $sql;//die;

    $company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
    $buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

    ob_start();
    ?>
    <div style="width:820px; margin: 0 auto;">

        <table width="820" cellspacing="0" cellpadding="0" border="0" rules="all"  >
            <tr class="form_caption">
                <td colspan="20" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
            </tr>
            <tr class="form_caption">
                <td colspan="20" align="center"><? echo $company_library[$cbo_company]; ?></td>
            </tr>
        </table>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" >
            <thead>
            <th width="30"></th>
            <th width="50">SL</th>
            <th width="150">Company</th>
            <th width="150">Buyer Name</th>
            <th width="150">Job No</th>
            <th width="80">Style Ref.</th>
            <th width="80">PO ID</th>
            <th>Cons Qty</th>
            </thead>
        </table>
        <div style="width:820px; overflow-y:scroll; max-height:350px;font-size:12px; overflow-x:hidden;" id="scroll_body">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table"  id="tbl_accessories_controll" >
                <tbody>
                <?

                $data_array=sql_select($sql);
                $i=1;
                foreach ($data_array as $selectResult)
                {
					if($prev_access_data[$selectResult[csf('po_id')]]!=1 && $cbo_approval_type==2)
					{
						if ($i%2==0)
                        $bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30" align="center">
                            <input type="checkbox" id="tbl_<? echo $i;?>" name="tbl[]" />
                            <input type="hidden" id="dtlsId_<? echo $i;?>" name="dtlsId[]" value="<? //echo $prev_dtls_id[$selectResult[csf('po_id')]]; ?>" />
                            </td>
							<td width="50" align="center"><? echo $i; ?></td>
							<td width="150"><p><? echo  $company_library[$selectResult[csf('company_name')]]; ?></p></td>
							<td width="150" id="buyer_name_<? echo $i;?>"><? echo $buyer_arr[$selectResult[csf('buyer_name')]]; ?> </td>
							<td width="150"><? echo $selectResult[csf('job_no')]; ?> </td>
							<td width="80"> <? echo $selectResult[csf('style_ref_no')]; ?>  </td>
							<td width="80" id="po_id_<? echo $i;?>" title="<? echo $selectResult[csf('po_id')] ?>"><? echo $selectResult[csf('po_number')]; ?></td>
							<td align="right" id="issue_amt_<? echo $i;?>"><? echo $selectResult[csf('issue_amt')]; ?></td>
						</tr>
						<?
						$i++;
					}
					else if($prev_access_data[$selectResult[csf('po_id')]]==1 && $cbo_approval_type==1)
					{
						if ($i%2==0)
                        $bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30" align="center">
                            <input type="checkbox" id="tbl_<? echo $i;?>" name="tbl[]" />
                            <input type="hidden" id="dtlsId_<? echo $i;?>" name="dtlsId[]" value="<? echo $prev_dtls_id[$selectResult[csf('po_id')]]; ?>" />
                            </td>
							<td width="50"><? echo $i; ?></td>
							<td width="150"><p><? echo  $company_library[$selectResult[csf('company_name')]]; ?></p></td>
							<td width="150" id="buyer_name_<? echo $i;?>"><? echo $buyer_arr[$selectResult[csf('buyer_name')]]; ?> </td>
							<td width="150"><? echo $selectResult[csf('job_no')]; ?> </td>
							<td width="80"> <? echo $selectResult[csf('style_ref_no')]; ?>  </td>
							<td width="80" id="po_id_<? echo $i;?>" title="<? echo $selectResult[csf('po_id')] ?>"> <? echo $selectResult[csf('po_id')]; ?></td>
							<td align="right" id="issue_amt_<? echo $i;?>"><? echo $selectResult[csf('issue_amt')]; ?></td>
						</tr>
						<?
						$i++;
					}
                }
                ?>

                </tbody>
                <tfoot>
                    <tr>
                        <th width="30" style="text-align:center">
                            <input type="checkbox" id="all_check" onClick="check_all('all_check')" />
                            <input type="hidden" name="hide_approval_type" id="hide_approval_type" value="<? echo $cbo_approval_type; ?>">
    
                        </th>
                        <th colspan="7" align="left"><input type="button" value="<? if($cbo_approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px; float:left;" onClick="submit_approved(<? echo $i; ?>,<? echo $cbo_approval_type; ?>)"/></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <?
    exit();
}

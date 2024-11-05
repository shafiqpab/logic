<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.fabrics.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']['user_id'];

//--------------------------------------------------------------------------------------------------------------------

    if ($action=="load_drop_down_location")
    {
        echo create_drop_down( "cbo_location", 100, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-Select Location-", $selected, "load_drop_down( 'requires/style_owner_wise_production_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );",0);
        exit();
    }

if ($action == "load_drop_down_party")
{
	$data = explode("_", $data);
	$company_id = $data[1];

	//$company_id
	if ($data[0] == 1) 
	{
		echo create_drop_down("cbo_wo_company_name", 130, "SELECT comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--Select Company--", "", "", "");
	} 
	else if ($data[0] == 3) 
	{
		echo create_drop_down("cbo_wo_company_name", 130, "SELECT a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=23 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select Supplier--", 0, "");
	} 
	else 
	{
		echo create_drop_down("cbo_wo_company_name", 130, $blank_array, "", 1, "--Select Company--", 0, "",1);
	}
	exit();
}

    if ($action=="load_drop_down_buyer")
    {
        echo create_drop_down( "cbo_buyer_name", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-Select Buyer-", $selected, "load_drop_down( 'requires/style_owner_wise_production_report_controller', this.value, 'load_drop_down_brand', 'brand_td');");
        exit();
    }

    if ($action=="load_drop_down_brand")
    {
        echo create_drop_down( "cbo_brand", 110, "select id, brand_name from lib_buyer_brand brand where buyer_id='$data' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "--Brand--", $selected, "" );
        exit();
    }

    if($db_type==2) $insert_year="extract( year from b.insert_date)";

    if ($action=="job_no_popup")
    {
        echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
        extract($_REQUEST);
        // $data=explode('_',$data);
        //print_r ($data);
        ?>
        <script>
        function js_set_value( job_id )
        {
            //alert(po_id)
            document.getElementById('txt_job_id').value=job_id;
            parent.emailwindow.hide();
        }

        </script>
        <input type="hidden" id="txt_job_id" />
     <?
            $sql_cond="";
            $sql_cond .= ($company) ? " and a.company_name=$company" : "";
            $sql_cond .= ($style_onwer) ? " and a.style_owner=$style_onwer" : "";
   
       
       
          if(str_replace("'","",$year)!=0) $year_cond=" and extract (year from b.insert_date)=".str_replace("'","",$year).""; else $year_cond="";
   

        $marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
        $teamMemberArr = return_library_array("select id,team_leader_name from lib_marketing_team ","id","team_leader_name");
        
            $sql= "SELECT a.id, a.job_no,a.job_no_prefix_num, a.style_ref_no, a.product_dept, a.dealing_marchant, a.team_leader,a.style_owner,a.company_name,$insert_year as year from  wo_po_details_master a, wo_po_break_down  b where  b.job_id=a.id and a.status_active=1 and a.is_deleted=0 $sql_cond $year_cond group by a.id, a.job_no,a.job_no_prefix_num, a.style_ref_no, a.product_dept, a.dealing_marchant, a.team_leader,b.insert_date,style_owner,a.company_name order by a.id DESC";
        
        // echo $sql;die;

        $arr=array(3=>$product_dept,4=>$marchentrArr,5=>$teamMemberArr);
        echo  create_list_view("list_view", "Year,Job No,Style Ref.,Prod. Dept.,Marchant,Team Name", "50,100,110,110,150,150","680","360",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "0,0,0,product_dept,dealing_marchant,team_leader", $arr , "year,job_no_prefix_num,style_ref_no,product_dept,dealing_marchant,team_leader", "",'setFilterGrid("list_view",-1);','0,0,0,0,0','') ;
        
        exit();
    }

    if ($action=="style_no_popup")
    {
        echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
        extract($_REQUEST);
        //  print_r ($_REQUEST);die;
        //  $data=explode('_',$data);
        // echo $company;die;
        // echo"<pre>";print_r($data);die;
        ?>
        <script>
        function js_set_value( style_id )
        {
            //alert(po_id)
            document.getElementById('txt_style_ref_no').value=style_id;
            parent.emailwindow.hide();
        }

        </script>
        <input type="hidden" id="txt_style_ref_no" />
     <?
         $sql_cond="";
         $sql_cond .= ($company) ? " and a.company_name=$company" : "";
         $sql_cond .= ($style_onwer) ? " and a.style_owner=$style_onwer" : "";
    
        
        
        if(str_replace("'","",$year)!=0) $year_cond=" and extract (year from b.insert_date)=".str_replace("'","",$year).""; else $year_cond="";
    
        

        $marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
        $teamMemberArr = return_library_array("select id,team_leader_name from lib_marketing_team ","id","team_leader_name");
        
    
        $sql= "SELECT a.id, a.job_no,a.job_no_prefix_num, a.company_name,a.style_ref_no, a.product_dept, a.dealing_marchant, a.team_leader,a.style_owner,$insert_year as year from  wo_po_details_master a, wo_po_break_down  b where  b.job_id=a.id and a.status_active=1 and a.is_deleted=0 $sql_cond  $year_cond group by a.id, a.job_no,a.job_no_prefix_num, a.style_ref_no, a.product_dept, a.dealing_marchant, a.team_leader,b.insert_date,a.style_owner, a.company_name order by a.id DESC";
        
        // echo $sql;die;

        $arr=array(3=>$product_dept,4=>$marchentrArr,5=>$teamMemberArr);
        echo  create_list_view("list_view", "Year,Job No,Style Ref.,Prod. Dept.,Marchant,Team Name", "50,100,110,110,150,150","680","360",0, $sql , "js_set_value", "id,style_ref_no", "", 1, "0,0,0,product_dept,dealing_marchant,team_leader", $arr , "year,job_no_prefix_num,style_ref_no,product_dept,dealing_marchant,team_leader", "",'setFilterGrid("list_view",-1);','0,0,0,0,0','') ;
        exit();
    }

    if ($action == "order_no_popup")
    {
        echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);
        ?>
        <script>
            var selected_id = new Array;
            var selected_name = new Array;

            function check_all_data() {
                var tbl_row_count = document.getElementById('list_view').rows.length;
                tbl_row_count = tbl_row_count - 0;
                for (var i = 1; i <= tbl_row_count; i++) {
                    var onclickString = $('#tr_' + i).attr('onclick');
                    var paramArr = onclickString.split("'");
                    var functionParam = paramArr[1];
                    js_set_value(functionParam);
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
                if ($('#tr_' + str).css("display") != 'none') {
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
        $sql_cond="";
         $sql_cond .= ($company) ? " and b.company_name=$company" : "";
         $sql_cond .= ($style_onwer) ? " and b.style_owner=$style_onwer" : "";
        
        if(str_replace("'","",$year)!=0) $year_cond=" and extract (year from b.insert_date)=".str_replace("'","",$year).""; else $year_cond="";
    
        $sql = "SELECT  a.id ,a.po_number,a.job_no_mst,b.style_ref_no,b.job_no_prefix_num,b.company_name,b.style_owner, $insert_year as year
        from wo_po_break_down a, wo_po_details_master b where a.job_id=b.id and  a.is_deleted=0 and a.status_active=1 and
        b.status_active=1 and b.is_deleted=0 $sql_cond   $year_cond order by  a.id DESC";
        //  echo $sql;die;
        echo create_list_view("list_view", "Order Number,Job No, Year,Style Ref", "150,100,100,150", "550", "310", 0, $sql, "js_set_value", "id,po_number", "", 1, "0", $arr, "po_number,job_no_prefix_num,year,style_ref_no", "", "setFilterGrid('list_view',-1)", "0", "", 1);
        echo "<input type='hidden' id='txt_selected_id' />";
        echo "<input type='hidden' id='txt_selected' />";
        exit();
    }
   

if($action=="report_generate")
{
   
	$process = array( &$_POST );
	// echo "<pre>";
	// print_r($process);
	extract(check_magic_quote_gpc( $process ));	
	$cbo_company_name=str_replace("'","", $cbo_company_name);
    $cbo_style_owner_name=str_replace("'","", $cbo_style_owner_name);
    $cbo_source=str_replace("'","", $cbo_source);
	$cbo_wo_company_name=str_replace("'","", $cbo_wo_company_name);
	$cbo_location=str_replace("'","", $cbo_location);	
    $cbo_buyer_name=str_replace("'","", $cbo_buyer_name);
	$cbo_brand=str_replace("'","", $cbo_brand);
	$txt_style_ref_no=str_replace("'","", $txt_style_ref_no);
    $txt_job_no=str_replace("'","", $txt_job_no);
    $hidd_job_id=str_replace("'","", $hidd_job_id);
    $txt_order_no=str_replace("'","", $txt_order_no);
    $hidd_po_id=str_replace("'","", $hidd_po_id);
    $txt_ref_no=str_replace("'","", $txt_ref_no);	
	$txt_date_from=str_replace("'","", $txt_date_from);
	$txt_date_to=str_replace("'","", $txt_date_to);
	

	// $report_type=str_replace("'","", $report_type); 
    $buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
    $company_library=return_library_array( "SELECT id,COMPANY_NAME from lib_company", "id", "COMPANY_NAME");
    $item_library=return_library_array( "SELECT id,ITEM_NAME from LIB_GARMENT_ITEM", "id", "ITEM_NAME");
    $color_library_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
    $supllier_library=return_library_array( "select id,SUPPLIER_NAME from LIB_SUPPLIER", "id", "SUPPLIER_NAME"  );

	$sql_cond="";
    
	if($cbo_company_name)$sql_cond.=" and a.company_name='$cbo_company_name'";
    if($cbo_style_owner_name)$sql_cond.=" and a.style_owner='$cbo_style_owner_name'";
    if($cbo_source)$sql_cond.=" and d.production_source='$cbo_source'";
    if($cbo_wo_company_name)$sql_cond.=" and d.serving_company='$cbo_wo_company_name'";
    if($cbo_location)$sql_cond.=" and a.location_name='$cbo_location'";
	if($cbo_buyer_name)$sql_cond.=" and a.buyer_name='$cbo_buyer_name'";
    if($hidd_job_id)$sql_cond.=" and a.id='$hidd_job_id'";
	if($txt_style_ref_no)$sql_cond.=" and a.style_ref_no like '%$txt_style_ref_no%'";
	if($txt_order_no)$sql_cond.=" and b.po_number like '%$txt_order_no%'";
    if($hidd_po_id)$sql_cond.=" and b.id='$hidd_po_id'";	
	if($txt_int_ref)$sql_cond.=" and b.grouping='$txt_int_ref'";
    if($cbo_brand)$sql_cond.=" and a.brand_id='$cbo_brand'";

    if($txt_date_from !="" && $txt_date_to !="" )
    {
		
        $sql_cond.=" and d.PRODUCTION_DATE between'$txt_date_from' and '$txt_date_to'";
	}	  



    $ex_cond="";
    if($cbo_style_owner_name)$ex_cond.=" and a.style_owner='$cbo_style_owner_name'";
    if($cbo_company_name)$ex_cond.=" and a.company_name='$cbo_company_name'";
    if($cbo_source)$ex_cond .=" and g.SOURCE ='$cbo_source'";
    if($cbo_wo_company_name)$ex_cond.=" and g.DELIVERY_COMPANY_ID='$cbo_wo_company_name'";
    if($cbo_location)$ex_cond.=" and a.location_name='$cbo_location'";
    if($cbo_buyer_name)$ex_cond.=" and a.buyer_name='$cbo_buyer_name'";
    if($hidd_job_id)$ex_cond.=" and a.id='$hidd_job_id'";
	if($txt_style_ref_no)$ex_cond.=" and a.style_ref_no like '%$txt_style_ref_no%'";
	if($txt_order_no)$ex_cond.=" and b.po_number like '%$txt_order_no%'";
    if($hidd_po_id)$ex_cond.=" and b.id='$hidd_po_id'";	
	if($txt_int_ref)$ex_cond.=" and b.grouping='$txt_int_ref'";
    if($cbo_brand)$ex_cond.=" and a.brand_id='$cbo_brand'";
	if($txt_date_from !="" && $txt_date_to !="" )
    {
		
        $ex_cond.=" and f.EX_FACTORY_DATE between'$txt_date_from' and '$txt_date_to'";
	}	  

    $cutt_qc_cond="";

	if($cbo_company_name)$cutt_qc_cond.=" and a.company_id='$cbo_company_name'";
    if($cbo_style_owner_name)$cutt_qc_cond.=" and c.style_owner='$cbo_style_owner_name'";
    if($cbo_source)$$cutt_qc_cond.=" and a.production_source='$cbo_source'";
    if($cbo_wo_company_name)$cutt_qc_cond.=" and a.serving_company='$cbo_wo_company_name'";
    if($cbo_location)$cutt_qc_cond.=" and a.location_id='$cbo_location'";
	if($cbo_buyer_name)$cutt_qc_cond.=" and c.buyer_name='$cbo_buyer_name'";
    if($hidd_job_id)$cutt_qc_cond.=" and c.id='$hidd_job_id'";
	if($txt_style_ref_no)$cutt_qc_cond.=" and c.style_ref_no like '%$txt_style_ref_no%'";
	if($txt_order_no)$cutt_qc_cond.=" and d.po_number like '%$txt_order_no%'";
    if($hidd_po_id)$cutt_qc_cond.=" and b.order_id='$hidd_po_id'";	
	if($txt_int_ref)$cutt_qc_cond.=" and d.grouping='$txt_int_ref'";
    if($cbo_brand)$cutt_qc_cond.=" and c.brand_id='$cbo_brand'";
    if($txt_date_from !="" && $txt_date_to !="" )
    {
		
        $cutt_qc_cond.=" and a.cutting_qc_date between'$txt_date_from' and '$txt_date_to'";
	}	  
  
    ?>
         
        <div>
            <table width="1580" cellspacing="0" >
                <tr class="form_caption" style="border:none;">
                    <td colspan="21" align="center" style="border:none;font-size:18px; font-weight:bold" >
                        <? echo "Style Owner Wise Production Report (Date and Order Wise Both)"; ?>
                    </td>
                </tr>
                <tr style="border:none;">
                    <td colspan="21" align="center" style="border:none; font-size:16px;">
                        Company Name : <? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>
                    </td>
                </tr>
                <tr style="border:none;">
                    <td colspan="21" align="center" style="border:none;font-size:16px; font-weight:bold">
                        <?
                        if(str_replace("'","",trim($txt_date_from))!="" && str_replace("'","",trim($txt_date_to))!="")
                        {
                            echo "From $txt_date_from To $txt_date_to" ;
                        }
                        ?>
                    </td>
                </tr>
            </table>
            
        </div>
    <?                   
    ob_start();
     // -------------------------------------SHOW Start------------------------------------------------//
	if($report_type==1) //Show
	{         
        
        $sql="SELECT a.ID as JOB_ID,a.COMPANY_NAME,a.BUYER_NAME,a.STYLE_REF_NO,a.LOCATION_NAME,a.STYLE_OWNER,a.JOB_NO,a.JOB_NO_PREFIX_NUM,a.BRAND_ID,b.ID as PO_ID,b.PO_NUMBER,b.GROUPING,b.FILE_NO,c.ITEM_NUMBER_ID,b.PUB_SHIPMENT_DATE,c.COLOR_NUMBER_ID,c.ORDER_QUANTITY,d.PRODUCTION_SOURCE,d.SERVING_COMPANY,d.EMBEL_NAME,d.PRODUCTION_DATE,d.PRODUCTION_TYPE,e.PRODUCTION_QNTY,e.REJECT_QTY,e.RE_PRODUCTION_QTY 
        FROM WO_PO_DETAILS_MASTER a,WO_PO_BREAK_DOWN b,WO_PO_COLOR_SIZE_BREAKDOWN c,PRO_GARMENTS_PRODUCTION_MST d,PRO_GARMENTS_PRODUCTION_DTLS e  
        Where a.id=b.job_id and a.id=c.job_id and b.id=c.PO_BREAK_DOWN_ID and b.ID=d.PO_BREAK_DOWN_ID and d.id=e.MST_ID and c.id=e.COLOR_SIZE_BREAK_DOWN_ID and d.PRODUCTION_TYPE in(1,2,3,4,5,7,8,11) and e.PRODUCTION_TYPE in(1,2,3,4,5,7,8,11)  and d.PRODUCTION_SOURCE in (1,3) and e.PRODUCTION_QNTY!=0 and d.GARMENTS_NATURE in(2,3) and a.STATUS_ACTIVE=1 and a.IS_DELETED=0  and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and c.STATUS_ACTIVE=1 and c.IS_DELETED=0 and d.STATUS_ACTIVE=1 and d.IS_DELETED=0 and e.STATUS_ACTIVE=1 and e.IS_DELETED=0 $sql_cond order by a.id,b.id,c.id";

        // echo $sql;die;
        $sql_result=sql_select($sql);

        if(count($sql_result)==0)
        {
            echo " <div align='center'><h3 style='color:red'> No Data Found. </h3></div>";die;
        }

        $data_array=array();
        $buyer_array=array();
        $po_id_arr=array();
        foreach ($sql_result as  $val) 
        {
            // ----------------------------------------------------Main PART START------------------------------------//
            $po_id_arr[$val['PO_ID']]=$val['PO_ID'];

            $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['job_no']=$val['JOB_NO_PREFIX_NUM'];

            $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['po_number']=$val['PO_NUMBER'];
            $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['shipment_date']=$val['PUB_SHIPMENT_DATE'];

            $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['buyer_name']=$val['BUYER_NAME'];

            $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['style_ref_no']=$val['STYLE_REF_NO'];
            $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['file_no']=$val['FILE_NO'];

            $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['grouping']=$val['GROUPING'];

            $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['order_quantity']+=$val['ORDER_QUANTITY'];

            $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['production_date']=$val['PRODUCTION_DATE'];

            $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['production_source']=$val['PRODUCTION_SOURCE'];

           
            if($val['PRODUCTION_TYPE'] ==2 && $val['EMBEL_NAME'] ==1 )//Print send
            {		
                $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['print_qty_send']+=$val['PRODUCTION_QNTY'];
            }

            if($val['PRODUCTION_TYPE'] ==3 && $val['EMBEL_NAME'] ==1 )//Print rcv
            {		
                $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['print_qty_rcv']+=$val['PRODUCTION_QNTY'];
            }

            if($val['PRODUCTION_TYPE'] ==2 && $val['EMBEL_NAME'] ==2 )//emb send
            {		
                $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['emb_qty_send']+=$val['PRODUCTION_QNTY'];
            }

            if($val['PRODUCTION_TYPE'] ==3 && $val['EMBEL_NAME'] ==2 )//emb rcv
            {		
                $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['emb_qty_rcv']+=$val['PRODUCTION_QNTY'];
            }

            if($val['PRODUCTION_TYPE'] ==2 && $val['EMBEL_NAME'] ==3 )//wash send
            {		
                $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['wash_send']+=$val['PRODUCTION_QNTY'];
            }

            if ($val['PRODUCTION_TYPE'] ==3 && $val['EMBEL_NAME'] ==3) //Wash Received
            {
                $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['wash_received'] += $val['PRODUCTION_QNTY'];
            }

            if($val['PRODUCTION_TYPE'] ==2 && $val['EMBEL_NAME'] ==4 )//Spcial.Work send
            {		
                $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['special_send']+=$val['PRODUCTION_QNTY'];
            }

            if ($val['PRODUCTION_TYPE'] ==3 && $val['EMBEL_NAME'] ==4) //Spcial.Work rcv
            {
                $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['special_rcv'] += $val['PRODUCTION_QNTY'];
            }

            if($val['PRODUCTION_TYPE'] ==4 && $val['PRODUCTION_SOURCE'] ==1 )//sewing inhouse
            {		
                $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['sweing_in']+=$val['PRODUCTION_QNTY'];
            }

            if($val['PRODUCTION_TYPE'] ==4 && $val['PRODUCTION_SOURCE'] ==3 )//sewing outbound
            {		
                $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['sweing_in_out']+=$val['PRODUCTION_QNTY'];
            }

            if($val['PRODUCTION_TYPE'] ==5 && $val['PRODUCTION_SOURCE'] ==1 )//sewing_output inhouse
            {		
                $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['sweing_out_in']+=$val['PRODUCTION_QNTY'];
            }

            if($val['PRODUCTION_TYPE'] ==5 && $val['PRODUCTION_SOURCE'] ==3 )//sewing_output outbound
            {		
                $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['sweing_out']+=$val['PRODUCTION_QNTY'];
            }

            if($val['PRODUCTION_TYPE'] ==7 && $val['PRODUCTION_SOURCE'] ==1 )//iron inhouse
            {		
                $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['iron_in']+=$val['PRODUCTION_QNTY'];
            }

            if($val['PRODUCTION_TYPE'] ==7 && $val['PRODUCTION_SOURCE'] ==3 )//iron outbound
            {		
                $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['iron_out']+=$val['PRODUCTION_QNTY'];
            }

            if($val['PRODUCTION_TYPE'] ==7 && $val['PRODUCTION_SOURCE'] ==1 )// Re iron inhouse
            {		
                $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['re_iron_qty_in']+=$val['RE_PRODUCTION_QTY'];
            }

            if($val['PRODUCTION_TYPE'] ==7 && $val['PRODUCTION_SOURCE'] ==3 )// Re iron outbound
            {		
                $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['re_iron_qty_out']+=$val['RE_PRODUCTION_QTY'];

            }

            if($val['PRODUCTION_TYPE'] ==8 && $val['PRODUCTION_SOURCE'] ==1)//Finish Qty outbound
            {		
            $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['finish_in']+=$val['PRODUCTION_QNTY'];
            }

            if($val['PRODUCTION_TYPE'] ==8 && $val['PRODUCTION_SOURCE'] ==3)//Finish Qty outbound
            {		
                $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['finish_out']+=$val['PRODUCTION_QNTY'];
            }

            if($val['PRODUCTION_TYPE'] ==8 && $val['PRODUCTION_SOURCE'] ==1)//reject Qty 
            {		
                $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['reject_in']+=$val['REJECT_QTY'] ;
            }

            if($val['PRODUCTION_TYPE'] ==8 && $val['PRODUCTION_SOURCE'] ==3)//reject Qty outbound
            {		
                $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['reject_out']+=$val['REJECT_QTY'];
            }

            if($val['PRODUCTION_TYPE'] ==11 && $val['PRODUCTION_SOURCE'] ==1)//POLY Qty INHOUSE
            {		
                $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['poly_in']+=$val['PRODUCTION_QNTY'];
            }
            if($val['PRODUCTION_TYPE'] ==11 && $val['PRODUCTION_SOURCE'] ==3)///POLY Qty  OUTBOUND
            {		
                $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['poly_out']+=$val['PRODUCTION_QNTY'];
                
            }

        // ----------------------------------------------------Main PART END------------------------------------//


        // ----------------------------------------------------BUYER PART START------------------------------------//

            if($val['PRODUCTION_TYPE'] ==1 && $val['PRODUCTION_SOURCE'] ==1 )//Cutting inhouse
            {		
                $buyer_array[$val['BUYER_NAME']]['cut_qty_in']+=$val['PRODUCTION_QNTY'];
            }

            if($val['PRODUCTION_TYPE'] ==1 && $val['PRODUCTION_SOURCE'] ==3 )//Cutting outbound
            {		
                $buyer_array[$val['BUYER_NAME']]['cut_qty_out']+=$val['PRODUCTION_QNTY'];
            }

            if($val['PRODUCTION_TYPE'] ==2 && $val['EMBEL_NAME'] ==1 )//Print send
            {		
                $buyer_array[$val['BUYER_NAME']]['print_qty_send']+=$val['PRODUCTION_QNTY'];
            }

            if($val['PRODUCTION_TYPE'] ==3 && $val['EMBEL_NAME'] ==1 )//Print rcv
            {		
                $buyer_array[$val['BUYER_NAME']]['print_qty_rcv']+=$val['PRODUCTION_QNTY'];
            }

            if($val['PRODUCTION_TYPE'] ==2 && $val['EMBEL_NAME'] ==2 )//emb send
            {		
                $buyer_array[$val['BUYER_NAME']]['emb_qty_send']+=$val['PRODUCTION_QNTY'];
            }

            if($val['PRODUCTION_TYPE'] ==3 && $val['EMBEL_NAME'] ==2 )//emb rcv
            {		
                $buyer_array[$val['BUYER_NAME']]['emb_qty_rcv']+=$val['PRODUCTION_QNTY'];
            }

            if($val['PRODUCTION_TYPE'] ==2 && $val['EMBEL_NAME'] ==3 )//wash send
            {		
                $buyer_array[$val['BUYER_NAME']]['wash_send']+=$val['PRODUCTION_QNTY'];
            }

            if ($val['PRODUCTION_TYPE'] ==3 && $val['EMBEL_NAME'] ==3) //Wash Received
            {
                $buyer_array[$val['BUYER_NAME']]['wash_received'] += $val['PRODUCTION_QNTY'];
            }

            if($val['PRODUCTION_TYPE'] ==2 && $val['EMBEL_NAME'] ==4 )//Spcial.Work send
            {		
                $buyer_array[$val['BUYER_NAME']]['special_send']+=$val['PRODUCTION_QNTY'];
            }

            if ($val['PRODUCTION_TYPE'] ==3 && $val['EMBEL_NAME'] ==4) //Spcial.Work rcv
            {
                $buyer_array[$val['BUYER_NAME']]['special_rcv'] += $val['PRODUCTION_QNTY'];
            }

            if($val['PRODUCTION_TYPE'] ==4 && $val['PRODUCTION_SOURCE'] ==1 )//sewing inhouse
            {		
                $buyer_array[$val['BUYER_NAME']]['sweing_in']+=$val['PRODUCTION_QNTY'];
            }

            if($val['PRODUCTION_TYPE'] ==4 && $val['PRODUCTION_SOURCE'] ==3 )//sewing outbound
            {		
                $buyer_array[$val['BUYER_NAME']]['sweing_in_out']+=$val['PRODUCTION_QNTY'];
            }

            if($val['PRODUCTION_TYPE'] ==5 && $val['PRODUCTION_SOURCE'] ==1 )//sewing_output inhouse
            {		
                $buyer_array[$val['BUYER_NAME']]['sweing_out_in']+=$val['PRODUCTION_QNTY'];
            }

            if($val['PRODUCTION_TYPE'] ==5 && $val['PRODUCTION_SOURCE'] ==3 )//sewing_output outbound
            {		
                $buyer_array[$val['BUYER_NAME']]['sweing_out']+=$val['PRODUCTION_QNTY'];
            }

            if($val['PRODUCTION_TYPE'] ==7 && $val['PRODUCTION_SOURCE'] ==1 )//iron inhouse
            {		
                $buyer_array[$val['BUYER_NAME']]['iron_in']+=$val['PRODUCTION_QNTY'];
            }

            if($val['PRODUCTION_TYPE'] ==7 && $val['PRODUCTION_SOURCE'] ==3 )//iron outbound
            {		
                $buyer_array[$val['BUYER_NAME']]['iron_out']+=$val['PRODUCTION_QNTY'];
            }
            if($val['PRODUCTION_TYPE'] ==8 && $val['PRODUCTION_SOURCE'] ==1)//Finish Qty inhouse
            {		
            $buyer_array[$val['BUYER_NAME']]['finish_in']+=$val['PRODUCTION_QNTY'];
            }

            if($val['PRODUCTION_TYPE'] ==8 && $val['PRODUCTION_SOURCE'] ==3)//Finish Qty outbound
            {		
            $buyer_array[$val['BUYER_NAME']]['finish_out']+=$val['PRODUCTION_QNTY'];
            }

            if($val['PRODUCTION_TYPE'] ==11 && $val['PRODUCTION_SOURCE'] ==1)//poly inhouse
            {		
                $buyer_array[$val['BUYER_NAME']]['poly_in']+=$val['PRODUCTION_QNTY'];
            }
            if($val['PRODUCTION_TYPE'] ==11 && $val['PRODUCTION_SOURCE'] ==3)//poly out_bound
            {		
                $buyer_array[$val['BUYER_NAME']]['poly_out']+=$val['PRODUCTION_QNTY'];
            }

            // ----------------------------------------------------BUYER PART End------------------------------------//


        }
        // echo"<pre>";print_r($po_id_arr);die;
        $poIds = implode(",",$po_id_arr);
        $condition= new condition();     
        $condition->po_id_in($poIds);     
        $condition->init();
        $fabric= new fabric($condition);
        // echo $fabric->getQuery();die;
        $fabric_costing_arr = $fabric->getQtyArray_by_orderGmtsitemAndGmtscolor_knitAndwoven_greyAndfinish();
        // echo"<pre>";print_r($fabric_costing_arr);die;

        //======================================================= Cutting-Query=====================================================//
        $cutt_qc_dtls=(" SELECT a.company_id,a.cutting_qc_date,a.production_source,a.location_id,a.serving_company,b.qc_pass_qty as qc_pass_qty ,b.order_id as po_id ,b.color_id,b.item_id,c.id as job_id,c.buyer_name,c.style_ref_no,c.style_owner,a.job_no,c.brand_id,d.po_number,d.grouping from pro_gmts_cutting_qc_mst a,pro_gmts_cutting_qc_dtls b,wo_po_details_master c,wo_po_break_down d  where b.mst_id=a.id and a.job_no=c.job_no and c.id=d.job_id and b.order_id=d.id and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 $cutt_qc_cond order by a.cutting_qc_date ");

        //  echo $cutt_qc_dtls;die;
        $cutt_qc_result=sql_select($cutt_qc_dtls);
        $qc_data_array=array();
        $qc_buyer_array=array();
        foreach ($cutt_qc_result  as $value) 
        {
            if( $value['PRODUCTION_SOURCE'] ==1  &&  $value['QC_PASS_QTY']!=0)//Cutting inhouse
            {		
                $qc_data_array[$value['SERVING_COMPANY']][$value['JOB_ID']][$value['PO_ID']][$value['ITEM_ID']][$value['COLOR_ID']]['cut_qty_in']+=$value['QC_PASS_QTY'];
            }    

            if( $value['PRODUCTION_SOURCE'] ==2  &&  $value['QC_PASS_QTY']!=0)//Cutting inhouse
            {		
                $qc_data_array[$value['SERVING_COMPANY']][$value['JOB_ID']][$value['PO_ID']][$value['ITEM_ID']][$value['COLOR_ID']]['cut_qty_out']+=$val['QC_PASS_QTY'];
                
            }

             //======================================================= Buyer Part =====================================================//
            if( $value['PRODUCTION_SOURCE'] ==1  &&  $value['QC_PASS_QTY']!=0)//Cutting in
            {		
                $qc_buyer_array[$value['BUYER_NAME']]['cut_in']+=$value['QC_PASS_QTY'];
            }    

            if( $value['PRODUCTION_SOURCE'] ==2  &&  $value['cut_in']!=0)//Cutting out
            {		
                $qc_buyer_array[$value['BUYER_NAME']]['cut_out']+=$val['QC_PASS_QTY'];
                
            }
           
        }
        // echo"<pre>";print_r($qc_buyer_array);die;

        //======================================================= Cutting-Query End=====================================================//
        //----------------------------------    Ex-factory_query Start------------------------------------------------------//

        $sql_delivery=" SELECT a.ID AS JOB_ID,a.COMPANY_NAME,a.BUYER_NAME,b.ID AS PO_ID,b.UNIT_PRICE,a.GMTS_ITEM_ID,c.COLOR_NUMBER_ID,c.ITEM_NUMBER_ID,d.PRODUCTION_QNTY,g.DELIVERY_DATE,g.SOURCE,g.DELIVERY_COMPANY_ID  FROM WO_PO_DETAILS_MASTER a,WO_PO_BREAK_DOWN b,WO_PO_COLOR_SIZE_BREAKDOWN c,PRO_EX_FACTORY_MST f,PRO_EX_FACTORY_DELIVERY_MST  g,PRO_EX_FACTORY_DTLS d WHERE a.id = c.job_id AND a.id = b.job_id AND b.id = c.po_break_down_id AND b.id = f.po_break_down_id AND g.id = f.delivery_mst_id and c.id=d.color_size_break_down_id and f.id=d.mst_id  AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 AND d.status_active = 1  AND d.is_deleted = 0  AND f.is_deleted = 0 AND f.status_active = 1 AND g.is_deleted = 0 AND g.status_active = 1  $ex_cond  order by a.id,b.id,c.id  ";
        // echo $sql_delivery;die;



        $ex_data_array=array();
        $ex_buyer_array=array();
        $result=sql_select($sql_delivery);
        foreach ($result  as  $row) 
        {
            if( $row['SOURCE'] ==1 )//ex  Inhouse
            {		
                $ex_data_array[$row['DELIVERY_COMPANY_ID']][$row['JOB_ID']][$row['PO_ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']]['ex_in']+=$row['PRODUCTION_QNTY'];

                $ex_data_array[$row['DELIVERY_COMPANY_ID']][$row['JOB_ID']][$row['PO_ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']]['ex_in_val'] += ($row['PRODUCTION_QNTY'] * $row['UNIT_PRICE']);

                $$ex_data_array[$row['DELIVERY_COMPANY_ID']][$row['JOB_ID']][$row['PO_ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']]['unit_price_in'] += $row['UNIT_PRICE'];

            
            }
            if( $row['SOURCE'] ==3 )//ex outbound
            {
                $ex_data_array[$row['DELIVERY_COMPANY_ID']][$row['JOB_ID']][$row['PO_ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']]['ex_out']+=$row['PRODUCTION_QNTY'];

                $ex_data_array[$row['DELIVERY_COMPANY_ID']][$row['JOB_ID']][$row['PO_ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']]['ex_out_val'] += ($row['PRODUCTION_QNTY'] * $row['UNIT_PRICE']);

                $ex_data_array[$row['DELIVERY_COMPANY_ID']][$row['JOB_ID']][$row['PO_ID']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']]['unit_price_out'] += $row['UNIT_PRICE'];


            
            }

            // ----------------------------------------------------BUYER PART START------------------------------------//

            if( $row['SOURCE'] ==1 )//ex  Inhouse
            {		
                $ex_buyer_array[$row['BUYER_NAME']]['ex_in']+=$row['PRODUCTION_QNTY'];
                $ex_buyer_array[$row['BUYER_NAME']]['ex_in_val'] += ($row['PRODUCTION_QNTY'] * $row['UNIT_PRICE']);
                $ex_buyer_array[$row['BUYER_NAME']]['unit_price_in'] += $row['UNIT_PRICE'];

            
            }
            if( $row['SOURCE'] ==3 )//ex outbound
            {
                $ex_buyer_array[$row['BUYER_NAME']]['ex_out']+=$row['PRODUCTION_QNTY'];
                $ex_buyer_array[$row['BUYER_NAME']]['ex_out_val'] += ($row['PRODUCTION_QNTY'] * $row['UNIT_PRICE']);
                $ex_buyer_array[$row['BUYER_NAME']]['unit_price_out'] += $row['UNIT_PRICE'];

            
            }
            
        }
        //  echo"<pre>";print_r($ex_data_array);die;
        //  echo"<pre>";print_r($ex_buyer_array);die;  
		?>

                 
            <div style="float:left; width:2920px">
                <table width="2900" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
                    <thead>
                    <div style="width:2900px; float:left; background-color:#FCF"><strong>Production-Regular Order Summary</strong></div>
                    <tr>
                        
                        <th width="30"><p>Sl.<p></th>
                        <th width="120"><p>Buyer Name</p></th>
                        <th width="100"><p>Cut Qty.(Pcs)</p></th>
                        <th width="100"><p>Cut Qty (Out-bound)</p></th>
                        <th width="100"><p>Sent to Print</p></th>
                        <th width="100"><p>Rec.from Print</p></th>
                        <th width="100"><p>Sent to Emb.</p></th>
                        <th width="100"><p>Rec.from Emb.</p></th>
                        <th width="100"><p>Sent to Wash</p></th>
                        <th width="100"><p>Rec.Wash</p></th>
                        <th width="100"><p>Sent to Sp.Works</p></th>
                        <th width="100"><p>Rev Sp.Works</p></th>
                        <th width="100"><p>Sewing Input</p></th>
                        <th width="100"><p>Sew Input(Outbound)</p></th>
                        <th width="100"><p>Sewing Output</p></th>
                        <th width="110"><p>Sew.Output(Outbound)</p></th>
                        <th width="100"><p>Iron Qty</p></th>
                        <th width="100"><p>Total Poly(Inhouse)</p></th>
                        <th width="100"><p>Total Poly(Outbond)</p></th>
                        <th width="100"><p>Pack./Fin(In-House)</p></th>
                        <th width="100"><p>Pack./Fin(Out-bound)</p></th>
                        <th width="100"><p>Ex-Factory Qty</p></th>
                        <th width="100"><p>Ex-Factory Qty(Out-bound)</p></th>
                        <th width="100"><p>Ex-Factory Value</p></th>
                        <th width="100"><p>Ex-Fac.Bal.Qty</p></th>
                        <th width="110"><p>Ex-Fac.Bal.FOB Value</p></th>
                    </tr>
                    </thead>
                </table>    
             <div style="max-height:425px;  width:2920px" id="scroll_body">
                    <table  break-all;" border="1" class="rpt_table" width="2900px" rules="all" id="table_body">                       
                        <tbody>                       
                           <?
                            $i=1;  
                                $ttl_cut_qty_in=0; 
                                $ttl_cut_qty_out=0;
                                $ttl_print_qty_send=0;
                                $ttl_print_qty_rcv=0;
                                $ttl_emb_qty_send=0;
                                $ttl_emb_qty_rcv=0;
                                $ttl_wash_send=0;
                                $ttl_wash_rcv=0;
                                $ttl_special_send=0;
                                $ttl_special_rcv=0;
                                $ttl_sweing_in=0;
                                $ttl_sweing_in_out=0;
                                $ttl_sweing_out_in=0;
                                $ttl_sweing_out=0;
                                $ttl_iron_qty=0;
                                $ttl_poly_in=0;
                                $ttl_poly_out=0;
                                $ttl_finish_in=0;
                                $ttl_finish_out=0;
                                $ttl_ex_in_qty=0;
                                $ttl_ex_out_qty=0;
                                $ttl_ex_val=0;
                                $ttl_ex_balance=0;
                                $ttl_ex_fob=0;
                                               
                                foreach ($buyer_array as $buyer_key => $r) 
                                {
                                    $iron_qty =($r['iron_in'] + $r['iron_out']);
                                    $total_sweing=($r['sweing_out_in']+ $r['sweing_out']);
                                    $ex_in_qty = $ex_buyer_array[$buyer_key]['ex_in'];
                                    $ex_out_qty = $ex_buyer_array[$buyer_key]['ex_out'];
                                    $total_ex_qty=($ex_buyer_array[$buyer_key]['ex_in'] + $ex_buyer_array[$buyer_key]['ex_out']);
                                    $ex_val = ($ex_buyer_array[$buyer_key]['ex_in_val'] + $ex_buyer_array[$buyer_key]['ex_out_val'] );
                                    $ex_balance = ($total_ex_qty-$total_sweing );
                                    $unit_price = ($ex_buyer_array[$buyer_key]['unit_price_in'] + $ex_buyer_array[$buyer_key['unit_price_out']]);
                                    $ex_fob_val = ($ex_balance * $unit_price );
                                    $cutting_in_buyer =$qc_buyer_array[$buyer_key]['cut_in'];
                                    $cutting_out_buyer =$qc_data_array[$buyer_key]['cut_out'];
                                 

                                    if ($i%2==0) $bgcolor="#E9F3FF";									
									else $bgcolor="#FFFFFF";	
                                    
                                    ?>                         
                                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                        <td width="30"><p><?= $i;?><p></td>
                                        <td width="120"><p><?=$buyer_library[$buyer_key];?></p></td>
                                        <td width="100" align="right"><p><?= number_format($cutting_in_buyer,0);?></p></td>
                                        <td width="100" align="right"><p><?= number_format($cutting_out_buyer,0);?></p></td>
                                        <td width="100" align="right"><p><?= number_format($r['print_qty_send'],0);?></p></td>
                                        <td width="100" align="right"><p><?= number_format($r['print_qty_rcv'],0);?></p></td>
                                        <td width="100" align="right"><p><?= number_format($r['emb_qty_send'],0);?></p></td>
                                        <td width="100" align="right"><p><?= number_format($r['emb_qty_rcv'],0);?></p></td>
                                        <td width="100" align="right"><p><?= number_format($r['wash_send'],0);?></p></td>
                                        <td width="100" align="right"><p><?= number_format($r['wash_received'],0);?></p></td>
                                        <td width="100" align="right"><p><?= number_format($r['special_send'],0);?></p></td>
                                        <td width="100"align="right"><p><?= number_format($r['special_rcv'],0);?></p></td>
                                        <td width="100"align="right"><p><?= number_format($r['sweing_in'],0);?></p></td>
                                        <td width="100"align="right"><p><?= number_format($r['sweing_in_out'],0);?></p></td>
                                        <td width="100"align="right"><p><?= number_format($r['sweing_out_in'],0);?></p></td>
                                        <td width="110"align="right"><p><?= number_format($r['sweing_out'],0);?></p></td>
                                        <td width="100"align="right"><p><?= number_format($iron_qty,0);?></p></td>
                                        <td width="100"align="right"><p><?= number_format($r['poly_in'],0);?></p></td>
                                        <td width="100"align="right"><p><?= number_format($r['poly_out'],0);?></p></td>
                                        <td width="100"align="right"><p><?= number_format($r['finish_in'],0);?></p></td>
                                        <td width="100"align="right"><p><?= number_format($r['finish_out'],0);?></p></td> 
                                        <td width="100"align="right"><p><?= number_format($ex_in_qty,2);?></p></td>
                                        <td width="100"align="right"><p><?= number_format($ex_out_qty,2);?></p></td>
                                        <td width="100"align="right"><p><?= number_format($ex_val,2);?></p></td>
                                        <td width="100"align="right"><p><?= number_format($ex_balance,2);?></p></td>                          
                                        <td width="110"align="right"><p><?= number_format($ex_fob_val,2);?></p></td>
                                
                                    </tr> 
                                        <? 
                                        $i++ ;
                                        $ttl_cut_qty_in += $cutting_in_buyer; 
                                        $ttl_cut_qty_out += $cutting_in_out;
                                        $ttl_print_qty_send += $r['print_qty_send'];
                                        $ttl_print_qty_rcv += $r['print_qty_rcv'];
                                        $ttl_emb_qty_send  += $r['emb_qty_send'];
                                        $ttl_emb_qty_rcv += $r['emb_qty_rcv'];
                                        $ttl_wash_send +=$r['wash_send'];
                                        $ttl_wash_rcv +=$r['wash_received'];;
                                        $ttl_special_send +=$r['special_send'];
                                        $ttl_special_rcv += $r['special_rcv'];;
                                        $ttl_sweing_in += $r['sweing_in'];
                                        $ttl_sweing_in_out += $r['sweing_in_out'];
                                        $ttl_sweing_out_in += $r['sweing_out_in'];
                                        $ttl_sweing_out += $r['sweing_out'];
                                        $ttl_iron_qty += $iron_qty;
                                        $ttl_poly_in += $r['poly_in'];
                                        $ttl_poly_out += $r['poly_out'];
                                        $ttl_finish_in += $r['finish_in'];
                                        $ttl_finish_out += $r['finish_out'];
                                        $ttl_ex_in_qty += $ex_in_qty;
                                        $ttl_ex_out_qty  += $ex_out_qty;
                                        $ttl_ex_val += $ex_val;
                                        $ttl_ex_balance += $ex_balance;
                                        $ttl_ex_fob +=$ex_fob_val;
                                     
                                }                                          
                                ?>           
                        </tbody>
                        <tfoot>
                            <tr>
                                <th width="30"><p><p></th>
                                <th width="120"><p>Total</p></th>
                                <th width="100"><p><?=$ttl_cut_qty_in?></p></th>
                                <th width="100"><p><?=$ttl_cut_qty_out?></p></th>
                                <th width="100"><p><?=$ttl_print_qty_send?></p></th>
                                <th width="100"><p><?=$ttl_print_qty_rcv?></p></th>
                                <th width="100"><p><?=$ttl_emb_qty_send?></p></th>
                                <th width="100"><p><?=$ttl_emb_qty_rcv?></p></th>
                                <th width="100"><p><?=$ttl_wash_send?></p></th>
                                <th width="100"><p><?= $ttl_wash_rcv?></p></th>
                                <th width="100"><p><?=$ttl_special_send?></p></th>
                                <th width="100"><p><?=$ttl_special_rcv?></p></th>
                                <th width="100"><p><?=$ttl_sweing_in?></p></th>
                                <th width="100"><p><?=$ttl_sweing_in_out?></p></th>
                                <th width="100"><p><?=$ttl_sweing_out_in?></p></th>
                                <th width="110"><p><?=$ttl_sweing_out?></p></th>
                                <th width="100"><p><?=$ttl_iron_qty?></p></th>
                                <th width="100"><p><?=$ttl_poly_in?></p></th>
                                <th width="100"><p><?=$ttl_poly_out?></p></th>
                                <th width="100"><p><?=$ttl_finish_in?></p></th>
                                <th width="100"><p><?=$ttl_finish_out?></p></th>
                                <th width="100"><p><?= number_format($ttl_ex_in_qty,2)?></p></th>
                                <th width="100"><p><?= number_format($ttl_ex_out_qty,2)?></p></th>
                                <th width="100"><p><?= number_format($ttl_ex_val,2)?></p></th>
                                <th width="100"><p><?= number_format($ttl_ex_balance,2)?></p></th>
                                <th width="110"><p><?= number_format($ttl_ex_fob,2)?></p></th>

                            </tr>

                        </tfoot>
                    </table>
            </div>  

            <div style="margin-top: 30px;">    
            </div>
               
            <div id="scroll_body">           
                <table width="4400" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
                    <thead>
                        <div style="width:4400px; float:left; background-color:#98FB98"><strong>Production-Regular Order Details</strong></div>
                        <tr>
                            <th  width="30"><p>SL</p></th>
                            <th  width="200"><p>Working Factory</p></th>
                            <th  width="80"><p>Job No</p></th>
                            <th  width="130"><p>Order No</p></th>
                            <th  width="120"><p>Ship Date</p></th>
                            <th  width="120"><p>Buyer Name</p></th>
                            <th  width="120"><p>Style Name</p></th>
                            <th  width="80"><p>File No</p></th>
                            <th  width="100"><p>Internal Ref</p></th>
                            <th  width="130"><p>Item Name</p></th>
                            <th  width="90"><p>Color</p></th>
                            <th  width="80"><p>Order Qty.</p></th>
                            <th  width="100"><p>Required Fabric</p></th>
                            <th  width="100"><p>Production Date</p></th>
                            <th  width="80"><p>Cutting</p></th>
                            <th  width="120"><p>Cutting(Out-bound)</p></th>
                            <th  width="80"><p>Sent to print</p></th>
                            <th  width="100"><p>Recv.Print/Emb</p></th>
                            <th  width="80"><p>Sent to Emb.</p></th>
                            <th  width="80"><p>Rec. Emb.</p></th>
                            <th  width="80"><p>Sent to Wash</p></th>
                            <th  width="80"><p>Rec.Wash</p></th>
                            <th  width="120"><p>Sent.to Sp.Works</p></th>
                            <th  width="120"><p>Recv.Sp.Works</p></th>
                            <th  width="120"><p>Sewing.In(Inhouse)</p></th>
                            <th  width="140"><p>Sewing.In(Out-bound)</p></th>
                            <th  width="120"><p>Total.Sewing.Input</p></th>
                            <th  width="80"><p>Sewing Out</p></th>
                            <th  width="150"><p>Sewing.Out(Out-bound)</p></th>
                            <th  width="120"><p>Total Sewing Out</p></th>
                            <th  width="110"><p>Iron Qty(Inhouse)</p></th>
                            <th  width="140"><p>Iron.Qty(Out-bound)</p></th>
                            <th  width="100"><p>Total Iron Qty</p></th>
                            <th  width="100"><p>Re-Iron Qty</p></th>
                            <th  width="120"><p>Poly Qty(Inhouse)</p></th>
                            <th  width="140"><p>Poly Qty(Out-bound)</p></th>
                            <th  width="100"><p>Total Poly Qty</p></th>
                            <th  width="130"><p>Finish Qty(Inhouse)</p></th>
                            <th  width="140"><p>Finish Qty(Out-bound)</p></th>
                            <th  width="100"><p>Total Finish Qty</th>
                            <th  width="100"><p>Reject Qty</p></th>
                            <th  width="140"><p>Ex-Fac.Qty(In-House)</p></th>
                            <th  width="100"><p>Ex-Fac.Qty(Out-bound)</p></th>
                            <th  width="120"><p>Total Ex-Fac.Qty</p></th>
                            <th width="120"><p>Ex-Factory Value</p></th>
                            <th width="100"><p>Ex-Fac.Bal.Qty</p></th>
                            <th width=""><p>Ex-Fac.Bal.FOB Value</p></th>

                        </tr>
                    </thead>
                    <tbody>
                        <?
                        $j=1;
                        $total_cut_qty_in=0; 
                        $total_cut_qty_out=0;
                        $total_print_qty_send=0;
                        $total_print_qty_rcv=0;
                        $total_emb_qty_send=0;
                        $total_emb_qty_rcv=0;
                        $total_wash_send=0;
                        $total_wash_rcv=0;
                        $total_special_send=0;
                        $total_special_rcv=0;
                        $total_sweing_in=0;
                        $total_sweing_in_out=0;
                        $total_sweing_out_in=0;
                        $total_sweing_out=0;
                        $total_iron_qty_in=0;
                        $total_iron_qty_out=0;
                        $total_poly_in=0;
                        $total_poly_out=0;
                        $total_finish_in=0;
                        $total_finish_out=0;
                        $total_ex_in_qty =0;
                        $total_ex_out_qty  = 0;
                        $gr_total_sweing_in_qty=0;
                        $gr_total_sweing_out_qty=0;
                        $gr_total_iron_qty=0;
                        $gr_total_re_iron_qty=0;
                        $gr_total_poly_qty=0;
                        $gr_total_finish_qty=0;
                        $gr_total_reject_qty=0;
                        $total_ex_val=0;
                        $total_ex_balance=0;
                        $total_ex_fob=0;
                        
                        foreach ($data_array as $w_com => $w_com_val) 
                        {
                            foreach ($w_com_val as $job_id => $job_data) 
                            {
                                foreach ($job_data as $po_id => $po_data) 
                                {
                                    foreach ($po_data as $item_id => $item_data) 
                                    {
                                        foreach ($item_data as $color_id => $v) 
                                        {  
                                            $sweing_in_qty = ($v['sweing_in'] + $v['sweing_in_out']);

                                            $sweing_out_qty = ($v['sweing_out_in'] + $v['sweing_out']);

                                            $iron_qty = ($v['iron_in'] + $v['iron_out']);
                                            $re_iron_qty = ($v['re_iron_qty_in'] + $v['re_iron_qty_out']);
                                            $poly_qty = ($v['poly_in'] + $v['poly_out']);
                                            $finish_qty =($v['finish_in'] + $v['finish_out']);
                                            $reject_qty =($v['reject_in'] + $v['reject_out']);
                                            $cutting_in=$qc_data_array[$w_com][$job_id][$po_id][$item_id][ $color_id]['cut_qty_in'];
                                            $cutting_out=$qc_data_array[$w_com][$job_id][$po_id][$item_id][ $color_id]['cut_qty_out'];

                                            $ex_in_qty= $ex_data_array[$w_com][$job_id][$po_id][$item_id][ $color_id]['ex_in'];
                                            $ex_out_qty= $ex_data_array[$w_com][$job_id][$po_id][$item_id][ $color_id]['ex_out'];
                                            // echo $w_com."**".$job_id."**".$po_id."**".$item_id."**".$color_id."<br>";

                                            $total_ex_qty = ($ex_in_qty + $ex_out_qty);

                                            $ex_val = ($ex_data_array[$w_com][$job_id][$po_id][$item_id][ $color_id]['ex_in_val'] + $ex_data_array[$w_com][$job_id][$po_id][$item_id][ $color_id]['ex_out_val'] );

                                            $ex_balance = ($sweing_out_qty - $total_ex_qty );

                                            $unit_price = ($ex_data_array[$w_com][$job_id][$po_id][$item_id][ $color_id]['unit_price_in'] + $ex_data_array[$w_com][$job_id][$po_id][$item_id][$color_id]['unit_price_out']);

                                            $ex_fob_val = ($ex_balance * $unit_price );
                                            
                                            $finish_febri_req_qty=(array_sum($fabric_costing_arr['knit']['finish'][$po_id][$item_id][$color_id]) + (array_sum($fabric_costing_arr['woven']['finish'][$po_id][$item_id][$color_id])));
                                            // echo $po_id."**".$item_id."**".$color_id."<br>";

                                            if ($j%2==1) $bgcolor="#E9F3FF";									
                                            else $bgcolor="#FFFFFF";	

                                          ?>
                                          
                                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('str_<? echo  $j; ?>','<? echo $bgcolor; ?>')" id="str_<? echo  $j; ?>">
                                                <td  width="30"><p><?=$j;?></p></td>
                                                <?
                                                if($v['production_source']==1)
                                                {
                                                    ?>
                                                    <td  width="200"><p><?=$company_library[$w_com];?></p></td>
                                                    <?
                                                }else
                                                {
                                                    ?>                                                      
                                                    <td  width="200"><p><?=$supllier_library[$w_com];?></p></td>
                                                    <?
                                                }                                                      
                                                ?>
                                                <td  width="80" align="center"><p><?=$v['job_no'];?></p></td>
                                                <td  width="130" align="center"><p><?=$v['po_number'];?></p></td>
                                                <td  width="120" align="left"><p><?=$v['shipment_date'];?></p></td>
                                                <td  width="120"><p><?=$buyer_library[$v['buyer_name']];?></p></td>
                                                <td  width="120"><p><?=$v['style_ref_no'];?></p></td>
                                                <td  width="80"><p><?=$v['file_no'];?></p></td>
                                                <td  width="100"><p><?=$v['grouping'];?></p></td>
                                                <td  width="130"><p><?=$item_library[$item_id];?></p></td>
                                                <td  width="90"><p><?=$color_library_arr[$color_id]?></p></td>
                                                <td  width="80" align="right"><p><?=number_format($v['order_quantity'],0);?></p></td>
                                                <td  width="100" align="right"><p><?=number_format($finish_febri_req_qty,2)?></p></td>
                                                <td  width="100" align="center"><p><?=$v['production_date'];?></p></td>
                                                <td  width="80" align="right"><p><?=number_format($cutting_in,0);?></p></td>
                                                <td  width="120" align="right"><p><?=number_format($cutting_out,0) ;?></p></td>
                                                <td  width="80" align="right"><p><?=number_format($v['print_qty_send'],0);?></p></td>
                                                <td  width="100" align="right"><p><?=number_format($v['print_qty_rcv'],0);?></p></td>
                                                <td  width="80" align="right"><p><?=number_format($v['emb_qty_send'],0);?></p></td>
                                                <td  width="80" align="right"><p><?=number_format($v['emb_qty_rcv'],0);?></p></td>
                                                <td  width="80" align="right"><p><?=number_format($v['wash_send'],0);?></p></td>
                                                <td  width="80" align="right"><p><?=number_format($v['wash_received'],0);?></p></td>
                                                <td  width="120" align="right"><p><?=number_format($v['special_send'],0);?></p></td>
                                                <td  width="120" align="right"><p><?=number_format($v['special_rcv'],0);?></p></td>
                                                <td  width="120" align="right"><p><?=number_format($v['sweing_in'],0);?></p></td>
                                                <td  width="140" align="right"><p><?=number_format($v['sweing_in_out'],0) ;?></p></td>
                                                <td  width="120" align="right"><p><?=number_format( $sweing_in_qty,0);?></p></td>
                                                <td  width="80" align="right"><p><?=number_format($v['sweing_out_in'],0) ;?></p></td>
                                                <td  width="150" align="right"><p><?=number_format($v['sweing_out'],0);?></p></td>
                                                <td  width="120" align="right"><p><?=number_format($sweing_out_qty,0) ;?></p></td>
                                                <td  width="110" align="right"><p><?=number_format($v['iron_in'],0) ;?></p></td>
                                                <td  width="140" align="right"><p><?=number_format($v['iron_out'],0);?></p></td>
                                                <td  width="100" align="right"><p><?=number_format($iron_qty,0);?></p></td>
                                                <td  width="100" align="right"><p><?=number_format($re_iron_qty,0)?></p></td>
                                                <td  width="120" align="right"><p><?=number_format($v['poly_in'],0);?></p></td>
                                                <td  width="140" align="right"><p><?=number_format($v['poly_out'],0);?></p></td>
                                                <td  width="100" align="right"><p><?=number_format($poly_qty,0);?></p></td>
                                                <td  width="130" align="right"><p><?=number_format($v['finish_in'],0);?></p></td>
                                                <td  width="140" align="right"><p><?=number_format($v['finish_out'],0);?></p></td>
                                                <td  width="100" align="right"><p><?=number_format($finish_qty,0);?></p></td>
                                                <td  width="100" align="right"><p><?=number_format($reject_qty,0);?></p></td>
                                                <td  width="140" align="right"><p><?=number_format($ex_in_qty,2);?></p></td>
                                                <td  width="100" align="right"><p><?=number_format($ex_out_qty,2);?></p></td>
                                                <td  width="120" align="right"><p><?=number_format($total_ex_qty,2);?></p></td>
                                                <td  width="120" align="right"><p><?=number_format($ex_val,2);?></p></td>
                                                <td  width="100" align="right"><p><?=number_format($ex_balance,2);?></p></td>
                                                <td  width="140" align="right"><p><?=number_format($ex_fob_val,2) ;?></p></td>
            
                                            </tr>                                               
                                            <?
                                            $j++;
                                            $total_cut_qty_in += $cutting_in; 
                                            $total_cut_qty_out += $cutting_out;
                                            $total_print_qty_send += $v['print_qty_send'];
                                            $total_print_qty_rcv += $v['print_qty_rcv'];
                                            $total_emb_qty_send  += $v['emb_qty_send'];
                                            $total_emb_qty_rcv += $v['emb_qty_rcv'];
                                            $total_wash_send += $v['wash_send'];
                                            $total_wash_rcv += $v['wash_received'];;
                                            $total_special_send += $v['special_send'];
                                            $total_special_rcv += $v['special_rcv'];;
                                            $total_sweing_in += $v['sweing_in'];
                                            $total_sweing_in_out += $v['sweing_in_out'];
                                            $total_sweing_out_in += $v['sweing_out_in'];
                                            $total_sweing_out += $v['sweing_out'];
                                            $total_iron_qty_in += $v['iron_in'];
                                            $total_iron_qty_out += $v['iron_out'] ;
                                            $total_poly_in += $v['poly_in'];
                                            $total_poly_out += $v['poly_out'];
                                            $total_finish_in += $v['finish_in'];
                                            $total_finish_out += $v['finish_out'];
                                            $total_ex_in_qty += $ex_in_qty;
                                            $total_ex_out_qty  += $ex_out_qty;
                                            $gr_total_sweing_in_qty += $sweing_in_qty;
                                            $gr_total_sweing_out_qty += $sweing_out_qty;
                                            $gr_total_iron_qty += $iron_qty;
                                            $gr_total_re_iron_qty += $re_iron_qty;
                                            $gr_total_poly_qty += $poly_qty;
                                            $gr_total_finish_qty += $finish_qty;
                                            $gr_total_reject_qty += $reject_qty;
                                            $gr_total_ex_qty += $total_ex_qty;
                                            $total_ex_val += $ex_val;
                                            $total_ex_balance += $ex_balance;
                                            $total_ex_fob += $ex_fob_val;

                                        }                                 
                                    }                                   
                                }
                            }                            
                        }	
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th  width="30"><p></p></th>
                            <th  width="200"><p></p></th>
                            <th  width="80"><p></p></th>
                            <th  width="130"><p></p></th>
                            <th  width="120"><p></p></th>
                            <th  width="120"><p></p></th>
                            <th  width="120"><p></p></th>
                            <th  width="80"><p></p></th>
                            <th  width="100"><p></p></th>
                            <th  width="130"><p></p></th>
                            <th  width="90"><p></p></th>
                            <th  width="80"><p></p></th>
                            <th  width="100"><p></p></th>
                            <th  width="100" ><p>Total</p></th>
                            <th  width="80"><p><?=$total_cut_qty_in;?></p></th>
                            <th  width="120"><p><?=$total_cut_qty_out;?></p></th>
                            <th  width="80"><p><?=$total_print_qty_send;?></p></th>
                            <th  width="100"><p><?=$total_print_qty_rcv;?></p></th>
                            <th  width="80"><p><?=$total_emb_qty_send;?></p></th>
                            <th  width="80"><p><?=$total_emb_qty_rcv;?></p></th>
                            <th  width="80"><p><?=$total_wash_send;?></p></th>
                            <th  width="80"><p><?=$total_wash_rcv;?></p></th>
                            <th  width="120"><p><?=$total_special_send;?></p></th>
                            <th  width="120"><p><?=$total_special_rcv;?></p></th>
                            <th  width="120"><p><?=$total_sweing_in;?></p></th>
                            <th  width="140"><p><?=$total_sweing_in_out;?></p></th>
                            <th  width="120"><p><?=$gr_total_sweing_in_qty;?></p></th>
                            <th  width="80"><p><?=$total_sweing_out_in;?></p></th>
                            <th  width="150"><p><?=$total_sweing_out;?></p></th>
                            <th  width="120"><p><?=$gr_total_sweing_out_qty;?></p></th>
                            <th  width="110"><p><?=$total_iron_qty_in;?></p></th>
                            <th  width="140"><p><?=$total_iron_qty_out;?></p></th>
                            <th  width="100"><p><?=$gr_total_iron_qty;?></p></th>
                            <th  width="100"><p><?=$gr_total_re_iron_qty;?></p></th>
                            <th  width="120"><p><?=$total_poly_in;?></p></th>
                            <th  width="140"><p><?=$total_poly_out;?></p></th>
                            <th  width="100"><p><?=$gr_total_poly_qty;?></p></th>
                            <th  width="130"><p><?=$total_finish_in;?></p></th>
                            <th  width="140"><p><?=$total_finish_out;?></p></th>
                            <th  width="100"><p><?=$gr_total_finish_qty;?></p></th>
                            <th  width="100"><p><?=$gr_total_reject_qty;?></p></th>
                            <th  width="140"><p><?=number_format($total_ex_in_qty,2);?></p></th>
                            <th  width="100"><p><?=number_format($total_ex_out_qty,2);?></p></th>
                            <th  width="120"><p><?=number_format($gr_total_ex_qty,2)?></p></th>
                            <th  width="120"><p><?=number_format($total_ex_val,2)?></p></th>
                            <th  width="100"><p><?=number_format($total_ex_balance,2)?></p></th>
                            <th  width="140"><p><?=number_format($total_ex_fob,2)?></p></th>

                        </tr>
                        
                    </tfoot>
                </table> 
              
                
            </div>
                <?

    }
    // -------------------------------------SHOW END------------------------------------------------//


    // -------------------------------------Cutting Start------------------------------------------------//
    if($report_type==2) //Cutting
	{       
        $sql="SELECT a.ID as JOB_ID,a.COMPANY_NAME,a.BUYER_NAME,a.STYLE_REF_NO,a.LOCATION_NAME,a.STYLE_OWNER,a.JOB_NO,a.JOB_NO_PREFIX_NUM,a.BRAND_ID,b.ID as PO_ID,b.PO_NUMBER,b.GROUPING,b.FILE_NO,c.ITEM_NUMBER_ID,b.PUB_SHIPMENT_DATE,c.COLOR_NUMBER_ID,c.ORDER_QUANTITY,d.PRODUCTION_SOURCE,d.SERVING_COMPANY,d.EMBEL_NAME,d.PRODUCTION_DATE,d.PRODUCTION_TYPE,e.PRODUCTION_QNTY,e.REJECT_QTY,e.RE_PRODUCTION_QTY 
        FROM WO_PO_DETAILS_MASTER a,WO_PO_BREAK_DOWN b,WO_PO_COLOR_SIZE_BREAKDOWN c,PRO_GARMENTS_PRODUCTION_MST d,PRO_GARMENTS_PRODUCTION_DTLS e  
        Where a.id=b.job_id and a.id=c.job_id and b.id=c.PO_BREAK_DOWN_ID and b.ID=d.PO_BREAK_DOWN_ID and d.id=e.MST_ID and c.id=e.COLOR_SIZE_BREAK_DOWN_ID and d.PRODUCTION_TYPE in(1) and e.PRODUCTION_TYPE in(1)  and d.PRODUCTION_SOURCE in (1,3) and d.GARMENTS_NATURE in(2,3) and e.PRODUCTION_QNTY!=0 and a.STATUS_ACTIVE=1 and a.IS_DELETED=0  and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and c.STATUS_ACTIVE=1 and c.IS_DELETED=0 and d.STATUS_ACTIVE=1 and d.IS_DELETED=0 and e.STATUS_ACTIVE=1 and e.IS_DELETED=0 $sql_cond order by a.id,b.id,c.id";

        // echo $sql;die;
        $sql_result=sql_select($sql);

        if(count($sql_result)==0)
        {
            echo " <div align='center'><h3 style='color:red'> No Data Found. </h3></div>";die;
        }

        $data_array=array();
        $buyer_array=array();
        $po_id_arr==array();
        
        foreach ($sql_result as  $val) 
        {
            // ----------------------------------------------------Main PART START------------------------------------//
            $po_id_arr[$val['PO_ID']]=$val['PO_ID'];

            $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['job_no']=$val['JOB_NO_PREFIX_NUM'];

            $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['po_number']=$val['PO_NUMBER'];
            $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['shipment_date']=$val['PUB_SHIPMENT_DATE'];

            $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['buyer_name']=$val['BUYER_NAME'];

            $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['style_ref_no']=$val['STYLE_REF_NO'];
            $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['file_no']=$val['FILE_NO'];

            $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['grouping']=$val['GROUPING'];

            $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['order_quantity']+=$val['ORDER_QUANTITY'];

            $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['production_date']=$val['PRODUCTION_DATE'];

            $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['production_source']=$val['PRODUCTION_SOURCE'];
	    }

        $poIds = implode(",",$po_id_arr);
        $condition= new condition();     
        $condition->po_id_in($poIds);     
        $condition->init();
        $fabric= new fabric($condition);
        // echo $fabric->getQuery();die;
        $fabric_costing_arr = $fabric->getQtyArray_by_orderGmtsitemAndGmtscolor_knitAndwoven_greyAndfinish();

        $cutt_qc_dtls=(" SELECT a.company_id,a.cutting_qc_date,a.production_source,a.location_id,a.serving_company,b.qc_pass_qty as qc_pass_qty ,b.order_id as po_id ,b.color_id,b.item_id,c.id as job_id,c.buyer_name,c.style_ref_no,c.style_owner,a.job_no,c.brand_id,d.po_number,d.grouping from pro_gmts_cutting_qc_mst a,pro_gmts_cutting_qc_dtls b,wo_po_details_master c,wo_po_break_down d  where b.mst_id=a.id and a.job_no=c.job_no and c.id=d.job_id and b.order_id=d.id and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 $cutt_qc_cond order by a.cutting_qc_date ");

        //  echo $cutt_qc_dtls;die;
        $cutt_qc_result=sql_select($cutt_qc_dtls);
        $qc_data_array=array();
        $qc_buyer_array=array();
        foreach ($cutt_qc_result  as $value) 
        {
            if( $value['PRODUCTION_SOURCE'] ==1  &&  $value['QC_PASS_QTY']!=0)//Cutting inhouse
            {		
                $qc_data_array[$value['SERVING_COMPANY']][$value['JOB_ID']][$value['PO_ID']][$value['ITEM_ID']][$value['COLOR_ID']]['cut_qty_in']+=$value['QC_PASS_QTY'];
            }    

            if( $value['PRODUCTION_SOURCE'] ==2  &&  $value['QC_PASS_QTY']!=0)//Cutting inhouse
            {		
                $qc_data_array[$value['SERVING_COMPANY']][$value['JOB_ID']][$value['PO_ID']][$value['ITEM_ID']][$value['COLOR_ID']]['cut_qty_out']+=$val['QC_PASS_QTY'];
                
            }

             //======================================================= Buyer Part =====================================================//
            if( $value['PRODUCTION_SOURCE'] ==1  &&  $value['QC_PASS_QTY']!=0)//Cutting in
            {		
                $qc_buyer_array[$value['BUYER_NAME']]['cut_in']+=$value['QC_PASS_QTY'];
            }    

            if( $value['PRODUCTION_SOURCE'] ==2  &&  $value['cut_in']!=0)//Cutting out
            {		
                $qc_buyer_array[$value['BUYER_NAME']]['cut_out']+=$val['QC_PASS_QTY'];
                
            }
           
        }

		?>        
            <div style="float:left; width:2920px">
                <table width="350" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
                    <thead>
                        <div style="width:350px; float:center; background-color:#7DF9FF"><strong>Production-Regular Order Summary</strong></div>
                        <tr>                       
                            <th width="30"><p>Sl.<p></th>
                            <th width="100"><p>Buyer Name</p></th>
                            <th width="100"><p>Cut Qty.(In-House)</p></th>
                            <th width="100"><p>Cut Qty.(Out-bound)</p></th>                       
                        </tr>
                    </thead>
                    <tbody>

                        <?
                            $i=1;
                            $ttl_cut_qty_in=0; 
                            $ttl_cut_qty_out=0;
                            foreach ($qc_buyer_array as $buyer_id => $v) 
                            {
                                $cutting_in_buyer =$qc_buyer_array[$buyer_id]['cut_in'];
                                $cutting_out_buyer =$qc_data_array[$buyer_id]['cut_out'];
                                if ($i%2==0) $bgcolor="#E9F3FF";									
                                else $bgcolor="#FFFFFF";	
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('etr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="etr_<? echo $i; ?>">
                                    <td width="30"><p><?=$i?><p></td>
                                    <td width="100"><p><?=$buyer_library[$buyer_id]?></p></td>
                                    <td width="100" align="right"><p><?=number_format($cutting_in_buyer,0);?></p></td>
                                    <td width="100" align="right"><p><?= number_format($cutting_out_buyer,0);?></p></td>     
                                    <?
                                    $i++;
                                    $ttl_cut_qty_in += $cutting_in_buyer; 
                                    $ttl_cut_qty_out +=$cutting_out_buyer;
                                    ?>
                              </tr>                             
                              <?

                            }
                            
                            ?>                      
                    </tbody>
                    <tfoot>
                        <tr>
                            <th width="30"><p><p></th>
                            <th width="100"><p>Total</p></th>
                            <th width="100"><p><?=$ttl_cut_qty_in;?></p></th>
                            <th width="100"><p><?=$ttl_cut_qty_out;?></p></th>                                 
                        </tr>
                    </tfoot>
                </table>

                <br>
                <br>
                <div id="scroll_body">           
                    <table width="1400" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
                        <thead>
                           
                                <div style="width:1400px; float:center; background-color:#98FB98"><strong>Production-Regular Order Details</strong></div>
                           
                            <tr>
                                <th  width="30"><p>SL</p></th>
                                <th  width="200"><p>Working Factory</p></th>
                                <th  width="100"><p>Job No</p></th>
                                <th  width="130"><p>Order No</p></th>
                                <th  width="100"><p>Ship Date</p></th>
                                <th  width="120"><p>Buyer Name</p></th>
                                <th  width="120"><p>Style Name</p></th>
                                <th  width="80"><p>File No</p></th>
                                <th  width="100"><p>Internal Ref</p></th>
                                <th  width="150"><p>Item Name</p></th>
                                <th  width="90"><p>Color</p></th>
                                <th  width="80"><p>Order Qty.</p></th>
                                <th  width="120"><p>Required Fabric</p></th>
                                <th  width="120"><p>Production Date</p></th>
                                <th  width="80"><p>Cutting</p></th>
                                <th  width="120"><p>Cutting(Out-bound)</p></th>
                                

                            </tr>
                        </thead>
                        <tbody>
                            <?
                            $j=1;
                            $total_cut_qty_in=0; 
                            $total_cut_qty_out=0;

                            foreach ($data_array as $w_com => $w_com_val) 
                            {
                                foreach ($w_com_val as $job_id => $job_data) 
                                {
                                    foreach ($job_data as $po_id => $po_data) 
                                    {
                                        foreach ($po_data as $item_id => $item_data) 
                                        {
                                            foreach ($item_data as $color_id => $v) 
                                            {  

                                                $finish_febri_req_qty=(array_sum($fabric_costing_arr['knit']['finish'][$po_id][$item_id][$color_id]) + (array_sum($fabric_costing_arr['woven']['finish'][$po_id][$item_id][$color_id])));

                                                $cutting_in=$qc_data_array[$w_com][$job_id][$po_id][$item_id][ $color_id]['cut_qty_in'];
                                                $cutting_out=$qc_data_array[$w_com][$job_id][$po_id][$item_id][ $color_id]['cut_qty_out'];

                                                if ($j%2==0) $bgcolor="#E9F3FF";									
                                                else $bgcolor="#FFFFFF";	
                                                ?>

                                                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('str_<? echo  $j; ?>','<? echo $bgcolor; ?>')" id="str_<? echo  $j; ?>">
                                                    <td  width="30"><p><?=$j?></p></td>
                                                    <?
                                                    if($v['production_source']==1)
                                                    {
                                                        ?>
                                                        <td  width="200"><p><?=$company_library[$w_com];?></p></td>
                                                        <?
                                                    }else
                                                    {
                                                        ?>                                                      
                                                        <td  width="200"><p><?=$supllier_library[$w_com];?></p></td>
                                                        <?
                                                    }                                                      
                                                    ?>
                                                    <td  width="100" align="center"><p><?=$v['job_no'];?></p></td>
                                                    <td  width="130" align="center"><p><?=$v['po_number'];?></p></td>
                                                    <td  width="100" align="left"><p><?=$v['shipment_date'];?></p></td>
                                                    <td  width="120"><p><?=$buyer_library[$v['buyer_name']];?></p></td>
                                                    <td  width="120" align="center"><p><?=$v['style_ref_no'];?></p></td>
                                                    <td  width="80" align="center"><p><?=$v['file_no'];?></p></td>
                                                    <td  width="100" align="center"><p><?=$v['grouping'];?></p></td>
                                                    <td  width="150"><p><?=$item_library[$item_id];?></p></td>
                                                    <td  width="90"><p><?=$color_library_arr[$color_id]?></p></td>
                                                    <td  width="80" align="right"><p><?=number_format($v['order_quantity'],0) ;?></p></td>
                                                    <td  width="120" align="right"><p><?=number_format($finish_febri_req_qty,2)?></p></td>
                                                    <td  width="120" align="center"><p><?=$v['production_date'];?></p></td>
                                                    <td  width="80" align="right"><a href="##" onClick="openmypage_cutting_popup('<? echo $w_com; ?>','<? echo $job_id; ?>','<? echo $po_id;?>','<? echo $item_id; ?>','<? echo $color_id; ?>','<? echo $txt_date_from; ?>','<? echo $txt_date_to; ?>','<? echo $v['cutting_qc_date']; ?>','Cutting Info','cutting_popup');"><p><?= number_format($cutting_in,0);?></p></td>
                                                   
                                                  <td  width="120" align="right"><p><?=number_format($cutting_out,0);?></p></td>
                                                
                                                </tr>
                                                <?
                                                $j++;
                                                $total_cut_qty_in += $cutting_in; 
                                                $total_cut_qty_out += $cutting_out;
                                            }   
                                        }
                                    }
                                }
                            }      
                            ?>                        
                                                        
                        </tbody>
                        <tfoot>
                            <tr>

                                <th  width="30"><p></p></th>
                                <th  width="200"><p></p></th>
                                <th  width="100"><p></p></th>
                                <th  width="130"><p></p></th>
                                <th  width="100"><p></p></th>
                                <th  width="120"><p></p></th>
                                <th  width="120"><p></p></th>
                                <th  width="80"><p></p></th>
                                <th  width="100"><p></p></th>
                                <th  width="150"><p></p></th>
                                <th  width="90"><p></p></th>
                                <th  width="80"><p></p></th>
                                <th  width="120"><p></p></th>
                                <th  width="120"><p>Total</p></th>
                                <th  width="80"><p><?=$total_cut_qty_in;?></p></th>
                                <th  width="120"><p><?=$total_cut_qty_out;?></p></th>
                            </tr>
                            <tr>

                                <th  width="30"><p></p></th>
                                <th  width="200"><p></p></th>
                                <th  width="100"><p></p></th>
                                <th  width="130"><p></p></th>
                                <th  width="100"><p></p></th>
                                <th  width="120"><p></p></th>
                                <th  width="120"><p></p></th>
                                <th  width="80"><p></p></th>
                                <th  width="100"><p></p></th>
                                <th  width="150"><p></p></th>
                                <th  width="90"><p></p></th>
                                <th  width="80"><p></p></th>
                                <th  width="120"><p></p></th>
                                <th  width="120"><p> Grand Total</p></th>
                                <th width="120" colspan="2" align="center"><p><?=($total_cut_qty_in + $total_cut_qty_out);?></p></th>
                            </tr>
                            
                        </tfoot>
                    </table> 
                    
                </div>
            </div>
        <?

    }
    // -------------------------------------Cutting END------------------------------------------------//


    // -------------------------------------Sewing Start------------------------------------------------//
    if($report_type==3) //Sewing 
	{
        
                            //---------------------------Main Query----------------------//

        $sql="SELECT a.ID as JOB_ID,a.COMPANY_NAME,a.BUYER_NAME,a.STYLE_REF_NO,a.LOCATION_NAME,a.STYLE_OWNER,a.JOB_NO,a.JOB_NO_PREFIX_NUM,a.BRAND_ID,b.ID as PO_ID,b.PO_NUMBER,b.GROUPING,b.FILE_NO,c.ITEM_NUMBER_ID,b.PUB_SHIPMENT_DATE,c.COLOR_NUMBER_ID,c.ORDER_QUANTITY,d.PRODUCTION_SOURCE,d.SERVING_COMPANY,d.EMBEL_NAME,d.PRODUCTION_DATE,d.PRODUCTION_TYPE,e.PRODUCTION_QNTY
        FROM WO_PO_DETAILS_MASTER a,WO_PO_BREAK_DOWN b,WO_PO_COLOR_SIZE_BREAKDOWN c,PRO_GARMENTS_PRODUCTION_MST d,PRO_GARMENTS_PRODUCTION_DTLS e  
        Where a.id=b.job_id and a.id=c.job_id and b.id=c.PO_BREAK_DOWN_ID and b.ID=d.PO_BREAK_DOWN_ID and d.id=e.MST_ID and c.id=e.COLOR_SIZE_BREAK_DOWN_ID and d.PRODUCTION_TYPE in(5) and e.PRODUCTION_TYPE in(5)  and d.PRODUCTION_SOURCE in (1,3) and d.GARMENTS_NATURE in(2,3) and e.PRODUCTION_QNTY!=0 and a.STATUS_ACTIVE=1 and a.IS_DELETED=0  and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and c.STATUS_ACTIVE=1 and c.IS_DELETED=0 and d.STATUS_ACTIVE=1 and d.IS_DELETED=0 and e.STATUS_ACTIVE=1 and e.IS_DELETED=0 $sql_cond order by a.id,b.id,c.id";

        // echo $sql;die;
        $sql_result=sql_select($sql);

        if(count($sql_result)==0)
        {
            echo " <div align='center'><h3 style='color:red'> No Data Found. </h3></div>";die;
        }

        $data_array=array();
        $buyer_array=array();
        
        foreach ($sql_result as  $val) 
        {
            // ----------------------------------------------------Main PART START------------------------------------//

            $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['job_no']=$val['JOB_NO_PREFIX_NUM'];

            $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['po_number']=$val['PO_NUMBER'];
            $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['shipment_date']=$val['PUB_SHIPMENT_DATE'];

            $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['buyer_name']=$val['BUYER_NAME'];

            $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['style_ref_no']=$val['STYLE_REF_NO'];
            $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['file_no']=$val['FILE_NO'];

            $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['grouping']=$val['GROUPING'];

            $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['order_quantity']+=$val['ORDER_QUANTITY'];

            $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['production_date']=$val['PRODUCTION_DATE'];

            $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['production_source']=$val['PRODUCTION_SOURCE'];


            if($val['PRODUCTION_TYPE'] ==5 && $val['PRODUCTION_SOURCE'] ==1  )//sewing_output inhouse
            {		
                $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['sweing_out_in']+=$val['PRODUCTION_QNTY'];
            }

            if($val['PRODUCTION_TYPE'] ==5 && $val['PRODUCTION_SOURCE'] ==3 )//sewing_output outbound
            {		
                $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['sweing_out']+=$val['PRODUCTION_QNTY'];
            }

            // ----------------------------------------------------Main PART END------------------------------------//


            // ----------------------------------------------------BUYER PART START------------------------------------//


            if($val['PRODUCTION_TYPE'] ==5 && $val['PRODUCTION_SOURCE'] ==1 )//sewing_output inhouse
            {		
                $buyer_array[$val['BUYER_NAME']]['sweing_out_in']+=$val['PRODUCTION_QNTY'];
            }

            if($val['PRODUCTION_TYPE'] ==5 && $val['PRODUCTION_SOURCE'] ==3 )//sewing_output outbound
            {		
                $buyer_array[$val['BUYER_NAME']]['sweing_out']+=$val['PRODUCTION_QNTY'];
            }

            
            

            // ----------------------------------------------------BUYER PART End------------------------------------//


        }
 
        
		?>  
            <div style="float:left; width:2920px" >
                <table width="550" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
                    <thead>
                        <div style="width:550px; float:center; background-color:#ADD8E6"><strong>Production-Regular Order Summary</strong></div>
                        <tr>
                        
                            <th width="30"><p>Sl.<p></th>
                            <th width="120"><p>Buyer Name</p></th>
                            <th width="120"><p>Sewing Quantity (In-House)</p></th>
                            <th width="120"><p>Sewing Quantity(Out-bound)</p></th>                      
                        </tr>
                    </thead>
                    <tbody>
                        <?
                            $i=1;
                            $ttl_sweing_out_in += $r['sweing_out_in'];
                            $ttl_sweing_out += $r['sweing_out'];
                           foreach ($buyer_array as $buyer_key => $r) 
                            {
                                 if ($i%2==0) $bgcolor="#E9F3FF";									
                                else $bgcolor="#FFFFFF";	
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                    <td width="30"><p><?=$i?><p></td>
                                    <td width="120"><p><?=$buyer_library[$buyer_key]?></p></td>
                                    <td width="120" align="right"><p><?=number_format($r['sweing_out_in'],0) ;?></p></td>
                                 
                                    <td width="120" align="right"><p><?=number_format($r['sweing_out'],0); ?></p></td>    
                                    <?   
                                    $i++;
                                    $ttl_sweing_out_in += $r['sweing_out_in'];
                                    $ttl_sweing_out += $r['sweing_out'];              
                                    ?>              
                              </tr>
                              <?
                            }                        
                            
                           ?>                        
                    </tbody>
                    <tfoot>
                        <tr>
                            <th width="30"><p><p></th>
                            <th width="120"><p>Total</p></th>
                            <th width="150" align="right"><p><?=$ttl_sweing_out_in?></p></th>
                            <th width="150" align="right"><p><?=$ttl_sweing_out?></p></th>
                           

                        </tr>

                    </tfoot>
                </table>

                <br>
                <br>
                <div id="scroll_body">           
                    <table width="1550" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
                        <thead>
                            <div style="width:1550px; float:center; background-color:#A7C7E7"><strong>Production-Regular Order Details</strong></div>
                            <tr>
                                <th  width="30"><p>SL</p></th>
                                <th  width="200"><p>Working Factory</p></th>
                                <th  width="100"><p>Job No</p></th>
                                <th  width="130"><p>Order No</p></th>
                                <th  width="100"><p>Ship Date</p></th>
                                <th  width="120"><p>Buyer Name</p></th>
                                <th  width="120"><p>Style Name</p></th>
                                <th  width="80"><p>File No</p></th>
                                <th  width="100"><p>Internal Ref</p></th>                            
                                <th  width="150"><p>Item Name</p></th>
                                <th  width="90"><p>Color</p></th>
                                <th  width="80"><p>Order Qty.</p></th>
                               
                                <th  width="120"><p>Production Date</p></th>
                                <th width="180"><p>Sewing Quantity (In-House)</p></th>
                                <th width="180"><p>Sewing Quantity(Out-bound)</p></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?
                                $j=1;
                                $total_sweing_out_in=0; 
                                $total_sweing_out=0;

                                foreach ($data_array as $w_com => $w_com_val) 
                                {
                                    foreach ($w_com_val as $job_id => $job_data) 
                                    {
                                        foreach ($job_data as $po_id => $po_data) 
                                        {
                                            foreach ($po_data as $item_id => $item_data) 
                                            {
                                                foreach ($item_data as $color_id => $v) 
                                                {  
                                                    if ($j%2==0) $bgcolor="#E9F3FF";									
                                                    else $bgcolor="#FFFFFF";	
                                                    ?>

                                                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('str_<? echo  $j; ?>','<? echo $bgcolor; ?>')" id="str_<? echo  $j; ?>">
                                                        <td  width="30"><p><?=$j?></p></td>
                                                        <?
                                                        if($v['production_source']==1)
                                                        {
                                                            ?>                                                          
                                                            <td  width="200"><p><?=$company_library[$w_com];?></p></td>
                                                            <?
                                                        }else
                                                        {
                                                            ?>                                                           
                                                            <td  width="200"><p><?=$supllier_library[$w_com];?></p></td>
                                                            <?
                                                        }                                                      
                                                        ?>
                                                        <td  width="100" align="center"><p><?=$v['job_no'];?></p></td>
                                                        <td  width="130" align="center"><p><?=$v['po_number'];?></p></td>
                                                        <td  width="100" align="left"><p><?=$v['shipment_date'];?></p></td>
                                                        <td  width="120"><p><?=$buyer_library[$v['buyer_name']];?></p></td>
                                                        <td  width="120" align="center"><p><?=$v['style_ref_no'];?></p></td>
                                                        <td  width="80" align="center"><p><?=$v['file_no'];?></p></td>
                                                        <td  width="100" align="center"><p><?=$v['grouping'];?></p></td>
                                                        <td  width="150"><p><?=$item_library[$item_id];?></p></td>
                                                        <td  width="90"><p><?=$color_library_arr[$color_id]?></p></td>
                                                        <td  width="80" align="right"><p><?=number_format($v['order_quantity'],0) ;?></p></td>
                                                       
                                                        <td  width="120" align="center"><p><?=$v['production_date'];?></p></td>
                                                        <td  width="80" align="right"><a href="##" onClick="openmypage_sweing_popup('<? echo $w_com; ?>','<? echo $job_id; ?>','<? echo $po_id;?>','<? echo $item_id; ?>','<? echo $color_id; ?>','<? echo $txt_date_from; ?>','<? echo $txt_date_to; ?>','<? echo $v['production_date']; ?>',' Sweing Info','sewingQnty_popup');"><p><?=number_format($v['sweing_out_in'],0); ?></p></td>
                                                        <td  width="180" align="right"><p><?=number_format($v['sweing_out'],0);?></p></td>
                                                    
                                                    </tr>
                                                    <?
                                                    $j++;
                                                    $total_sweing_out_in += $v['sweing_out_in']; 
                                                    $total_sweing_out += $v['sweing_out'];
                                                }   
                                            }
                                        }
                                    }
                                }      
                                ?>                        
                        </tbody>
                        <tfoot>
                            <tr>
                                <th  width="30"><p></p></th>
                                <th  width="200"><p></p></th>
                                <th  width="100"><p></p></th>
                                <th  width="130"><p></p></th>
                                <th  width="100"><p></p></th>
                                <th  width="120"><p></p></th>
                                <th  width="120"><p></p></th>
                                <th  width="80"><p></p></th>
                                <th  width="100"><p></p></th>                            
                                <th  width="150"><p></p></th>
                                <th  width="90"><p></p></th>
                                <th  width="80"><p></p></th>
                                
                                <th  width="120"><p>Total</p></th>
                                <th width="180"><p><?=$total_sweing_out_in;?></p></th>
                                <th width="180"><p><?=$total_sweing_out;?></p></th>

                            </tr>
                            <tr>
                                <th  width="30"><p></p></th>
                                <th  width="200"><p></p></th>
                                <th  width="100"><p></p></th>
                                <th  width="130"><p></p></th>
                                <th  width="100"><p></p></th>
                                <th  width="120"><p></p></th>
                                <th  width="120"><p></p></th>
                                <th  width="80"><p></p></th>
                                <th  width="100"><p></p></th>                            
                                <th  width="150"><p></p></th>
                                <th  width="90"><p></p></th>
                                <th  width="80"><p></p></th>                              
                                <th  width="120"><p> Grand Total</p></th>
                                <th width="180" colspan="2" align="center"><p><?=($total_sweing_out_in + $total_sweing_out);?></p></th>
                               
                            </tr>
                            
                        </tfoot>
                   </table> 
                  
            </div>
           
         <?
    }

    // -------------------------------------Sewing END------------------------------------------------//


     // -------------------------------------Finishing Start------------------------------------------------//

    if($report_type==4) //Finishing
	{
        
                  //---------------------------Main Query----------------------//

        $sql="SELECT a.ID as JOB_ID,a.COMPANY_NAME,a.BUYER_NAME,a.STYLE_REF_NO,a.LOCATION_NAME,a.STYLE_OWNER,a.JOB_NO,a.JOB_NO_PREFIX_NUM,a.BRAND_ID,b.ID as PO_ID,b.PO_NUMBER,b.GROUPING,b.FILE_NO,c.ITEM_NUMBER_ID,b.PUB_SHIPMENT_DATE,c.COLOR_NUMBER_ID,c.ORDER_QUANTITY,d.PRODUCTION_SOURCE,d.SERVING_COMPANY,d.EMBEL_NAME,d.PRODUCTION_DATE,d.PRODUCTION_TYPE,e.PRODUCTION_QNTY,e.REJECT_QTY,e.RE_PRODUCTION_QTY 
        FROM WO_PO_DETAILS_MASTER a,WO_PO_BREAK_DOWN b,WO_PO_COLOR_SIZE_BREAKDOWN c,PRO_GARMENTS_PRODUCTION_MST d,PRO_GARMENTS_PRODUCTION_DTLS e  
        Where a.id=b.job_id and a.id=c.job_id and b.id=c.PO_BREAK_DOWN_ID and b.ID=d.PO_BREAK_DOWN_ID and d.id=e.MST_ID and c.id=e.COLOR_SIZE_BREAK_DOWN_ID and d.PRODUCTION_TYPE in(8) and e.PRODUCTION_TYPE in(8)  and d.PRODUCTION_SOURCE in (1,3) and d.GARMENTS_NATURE in(2,3) and a.STATUS_ACTIVE=1 and a.IS_DELETED=0  and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and c.STATUS_ACTIVE=1 and c.IS_DELETED=0 and d.STATUS_ACTIVE=1 and d.IS_DELETED=0 and e.STATUS_ACTIVE=1 and e.IS_DELETED=0 $sql_cond order by a.id,b.id,c.id";

        // echo $sql;die;
        $sql_result=sql_select($sql);

        if(count($sql_result)==0)
        {
            echo " <div align='center'><h3 style='color:red'> No Data Found. </h3></div>";die;
        }

        $data_array=array();
        $buyer_array=array();
        
        foreach ($sql_result as  $val) 
        {
            // ----------------------------------------------------Main PART START------------------------------------//

            $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['job_no']=$val['JOB_NO_PREFIX_NUM'];

            $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['po_number']=$val['PO_NUMBER'];
            $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['shipment_date']=$val['PUB_SHIPMENT_DATE'];

            $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['buyer_name']=$val['BUYER_NAME'];

            $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['style_ref_no']=$val['STYLE_REF_NO'];
            $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['file_no']=$val['FILE_NO'];

            $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['grouping']=$val['GROUPING'];

            $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['order_quantity']+=$val['ORDER_QUANTITY'];

            $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['production_source']=$val['PRODUCTION_SOURCE'];

            $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['production_date']=$val['PRODUCTION_DATE'];

            
                if($val['PRODUCTION_TYPE'] ==8 && $val['PRODUCTION_SOURCE'] ==1)//Finish Qty outbound
                {		
                $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['finish_in']+=$val['PRODUCTION_QNTY'];
                }

                if($val['PRODUCTION_TYPE'] ==8 && $val['PRODUCTION_SOURCE'] ==3)//Finish Qty outbound
                {		
                    $data_array[$val['SERVING_COMPANY']][$val['JOB_ID']][$val['PO_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']]['finish_out']+=$val['PRODUCTION_QNTY'];
                }

            // ----------------------------------------------------Main PART END------------------------------------//
                // print_r($data_array);die;

            // ----------------------------------------------------BUYER PART START------------------------------------//

            
            if($val['PRODUCTION_TYPE'] ==8 && $val['PRODUCTION_SOURCE'] ==1)//Finish Qty inhouse
            {		
            $buyer_array[$val['BUYER_NAME']]['finish_in']+=$val['PRODUCTION_QNTY'];
            }

            if($val['PRODUCTION_TYPE'] ==8 && $val['PRODUCTION_SOURCE'] ==3)//Finish Qty outbound
            {		
            $buyer_array[$val['BUYER_NAME']]['finish_out']+=$val['PRODUCTION_QNTY'];
            }

           

            // ----------------------------------------------------BUYER PART End------------------------------------//


        }
  
        
		?>  
            <div style="float:left; width:2920px" >
                <table width="550" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
                    <thead>
                        <div style="width:550px;  background-color:#FCF"><strong>Production-Regular Order Summary</strong></div>
                        <tr>
                            
                            <th width="30"><p>Sl.<p></th>
                            <th width="120"><p>Buyer Name</p></th>
                            <th width="120"><p>Finishing Qty(In-House)</p></th>
                            <th width="120"><p>Finishing Qty(Out-bound)</p></th>
                            
                        </tr>
                    </thead>
                    <tbody>
                          <?
                            $i=1;
                            $ttl_finish_in = 0;
                            $ttl_sweing_out = 0;
                           foreach ($buyer_array as $buyer_key => $r) 
                            {
                            if ($i%2==0) $bgcolor="#E9F3FF";									
                                else $bgcolor="#FFFFFF";	
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                    <td width="30"><p><?=$i?><p></td>
                                    <td width="120"><p><?=$buyer_library[$buyer_key]?></p></td>
                                    <td width="120" align="right"><p><?= number_format($r['finish_in'],0);?></p></td>
                                    <td width="120" align="right"><p><?=number_format($r['finish_out'],0);?></p></td>     
                                    <?      
                                     $i++;
                                     $ttl_finish_in += $r['finish_in'];
                                     $ttl_finish_out += $r['finish_out'];
                                     ?>                        
                              </tr>
                              <?
                            }

                            
                           ?>        
                    </tbody>
                    <tfoot>
                        <tr>
                            <th width="30"><p><p></th>
                            <th width="120"><p>Total</p></th>
                            <th width="120"><p><?=$ttl_finish_in?></p></th>
                            <th width="120"><p><?= $ttl_finish_out?></p></th>
                           

                        </tr>

                    </tfoot>
                </table>

                <br>
                <br>
                <div id="scroll_body">           
                    <table width="1400" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
                        <thead>
                            
                            <div style="width:1400px; background-color:#FCF"><strong>Production-Regular Order Details</strong></div>
                            <tr>
                                <th  width="30"><p>SL</p></th>
                                <th  width="200"><p>Working Factory</p></th>
                                <th  width="100"><p>Job No</p></th>
                                <th  width="130"><p>Order No</p></th>
                                <th  width="100"><p>Ship Date</p></th>
                                <th  width="120"><p>Buyer Name</p></th>
                                <th  width="120"><p>Style Name</p></th>
                                <th  width="80"><p>File No</p></th>
                                <th  width="100"><p>Internal Ref</p></th>
                                <th  width="150"><p>Item Name</p></th>
                                <th  width="90"><p>Color</p></th>
                                <th  width="80"><p>Order Qty.</p></th>
                              
                                <th  width="100"><p>Production Date</p></th>
                                <th width="150"><p>Finishing Qty (In-House)</p></th>
                                <th width="150"><p>Finishing Qty (Out-bound)</p></th>
                                

                            </tr>
                        </thead>
                        <tbody>
                            <?
                            $j=1;
                            $total_finish_in=0; 
                            $total_finish_out=0;

                            foreach ($data_array as $w_com => $w_com_val) 
                            {
                                foreach ($w_com_val as $job_id => $job_data) 
                                {
                                    foreach ($job_data as $po_id => $po_data) 
                                    {
                                        foreach ($po_data as $item_id => $item_data) 
                                        {
                                            foreach ($item_data as $color_id => $v) 
                                            {  
                                                if ($j%2==0) $bgcolor="#E9F3FF";									
                                                else $bgcolor="#FFFFFF";	
                                                ?>

                                                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('str_<? echo  $j; ?>','<? echo $bgcolor; ?>')" id="str_<? echo  $j; ?>">
                                                    <td  width="30"><p><?=$j?></p></td>
                                                    <?
                                                        if($v['production_source']==1)
                                                        {
                                                            ?>
                                                            
                                                            <td  width="200"><p><?=$company_library[$w_com];?></p></td>
                                                            <?
                                                        }else
                                                        {
                                                            ?>
                                                            
                                                            <td  width="200"><p><?=$supllier_library[$w_com];?></p></td>
                                                            <?
                                                        }
                                                        
                                                    ?>
                                                    <td  width="100" align="center"><p><?=$v['job_no'];?></p></td>
                                                    <td  width="130" align="center"><p><?=$v['po_number'];?></p></td>
                                                    <td  width="100" align="left"><p><?=$v['shipment_date'];?></p></td>
                                                    <td  width="120"><p><?=$buyer_library[$v['buyer_name']];?></p></td>
                                                    <td  width="120" align="center"><p><?=$v['style_ref_no'];?></p></td>
                                                    <td  width="80" align="center"><p><?=$v['file_no'];?></p></td>
                                                    <td  width="100" align="center"><p><?=$v['grouping'];?></p></td>
                                                    <td  width="150"><p><?=$item_library[$item_id];?></p></td>
                                                    <td  width="90"><p><?=$color_library_arr[$color_id]?></p></td>
                                                    <td  width="80" align="right"><p><?=number_format($v['order_quantity'],0);?></p></td>
                                                   
                                                    <td  width="120" align="center"><p><?=$v['production_date'];?></p></td>
                                                    <td  width="150" align="right"><p><?=number_format($v['finish_in'],0);?></p></td>
                                                    <td  width="150" align="right"><p><?=number_format($v['finish_out']);?></p></td>
                                                
                                                </tr>
                                                <?
                                                $j++;
                                                $total_finish_in += $v['finish_in'];
                                                $total_finish_out += $v['finish_out'];
                                            }   
                                        }
                                    }
                                }
                            }      
                            ?>                        
                        </tbody>
                        <tfoot>
                            <tr>
                                <th  width="30"><p></p></th>
                                <th  width="200"><p></p></th>
                                <th  width="100"><p></p></th>
                                <th  width="130"><p></p></th>
                                <th  width="100"><p></p></th>
                                <th  width="120"><p></p></th>
                                <th  width="120"><p></p></th>
                                <th  width="80"><p></p></th>
                                <th  width="100"><p></p></th>
                                <th  width="150"><p></p></th>
                                <th  width="90"><p></p></th>
                                <th  width="80"><p></p></th>
                              
                                <th  width="100"><p>Total</p></th>
                                <th width="150"><p><?=$total_finish_in;?></p></th>
                                <th width="150"><p><?=$total_finish_out;?></p></th>
                            </tr>

                            <tr>
                                <th  width="30"><p></p></th>
                                <th  width="200"><p></p></th>
                                <th  width="100"><p></p></th>
                                <th  width="130"><p></p></th>
                                <th  width="100"><p></p></th>
                                <th  width="120"><p></p></th>
                                <th  width="120"><p></p></th>
                                <th  width="80"><p></p></th>
                                <th  width="100"><p></p></th>
                                <th  width="150"><p></p></th>
                                <th  width="90"><p></p></th>
                                <th  width="80"><p></p></th>                            
                                <th  width="100"><p>Grand Total</p></th>
                                <th width="150" colspan="2" align="center"><p><?=($total_finish_in + $total_finish_out);?></p></th>
                            </tr>
                            
                        </tfoot>
                    </table> 
                    
                </div>
            </div>
                <?

    }

    // -------------------------------------Finishing END------------------------------------------------//


    // -------------------------------------EMBELLISHMENT Start------------------------------------------------//
    if($report_type==5) //EMBELLISHMENT
	{
        if($cbo_company_name)$emb_cond.=" and a.company_name='$cbo_company_name'";
        if($cbo_style_owner_name)$emb_cond.=" and a.style_owner='$cbo_style_owner_name'";
        if($cbo_source)$emb_cond.=" and d.production_source='$cbo_source'";
        if($cbo_wo_company_name)$emb_cond.=" and d.serving_company='$cbo_wo_company_name'";
        if($cbo_location)$emb_cond.=" and a.location_name='$cbo_location'";
        if($cbo_buyer_name)$emb_cond.=" and a.buyer_name='$cbo_buyer_name'";
        if($hidd_job_id)$emb_cond.=" and a.id='$hidd_job_id'";
        if($txt_style_ref)$emb_cond.=" and a.style_ref_no like '%$txt_style_ref%'";
        if($txt_order_no)$emb_cond.=" and b.po_number like '%$txt_order_no%'";
        if($hidd_po_id)$emb_cond.=" and b.id='$hidd_po_id'";	
        if($txt_int_ref)$emb_cond.=" and b.grouping='$txt_int_ref'";

        if($txt_date_from !="" && $txt_date_to !="" )
        {
		    $date_cond.=("SELECT  a.po_break_down_id from  pro_garments_production_mst a,pro_garments_production_dtls b where a.production_date between '$txt_date_from' and '$txt_date_to' and a.production_type in(2,3) and b.production_type in(2,3) and a.embel_name in (1,2,3) and a.id=b.mst_id and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.status_active=1 order by a.po_break_down_id");   
	    }	         
        //echo $date_cond;die;
        $po_id_array=array();
        foreach (sql_select($date_cond) as $v) 
        {
            $po_id_array[$v['PO_BREAK_DOWN_ID']]=$v['PO_BREAK_DOWN_ID'];
        }
        // echo"<pre>";print_r($po_id_array);die;

        $con = connect();
			execute_query("DELETE from GBL_TEMP_ENGINE where user_id=$user_id and entry_form =103 and ref_from in(1)");
			oci_commit($con);

			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 103, 1,$po_id_array, $empty_arr);
        
        $sql="SELECT a.ID as JOB_ID,a.COMPANY_NAME,a.BUYER_NAME,a.STYLE_REF_NO,a.LOCATION_NAME,a.STYLE_OWNER,a.JOB_NO,a.JOB_NO_PREFIX_NUM,a.BRAND_ID,b.ID as PO_ID,b.PO_NUMBER,b.GROUPING,b.FILE_NO,c.ITEM_NUMBER_ID,b.PUB_SHIPMENT_DATE,c.COLOR_NUMBER_ID,c.ORDER_QUANTITY,d.PRODUCTION_SOURCE,d.SERVING_COMPANY,d.EMBEL_NAME,d.PRODUCTION_DATE,d.PRODUCTION_TYPE,e.PRODUCTION_QNTY,

        (CASE WHEN d.production_type =2 and d.embel_name=1 and d.production_date ='$txt_date_to' THEN e.production_qnty  END)
		AS today_print_send_qty,
		(CASE WHEN d.production_type =2 and d.embel_name=1 and d.production_date <='$txt_date_to'  THEN e.production_qnty END)
		  AS total_print_send_qty,
        (CASE WHEN d.production_type =3 and d.embel_name=1 and d.production_date ='$txt_date_to' THEN e.production_qnty  END)
		 AS today_print_rcv_qty,
		(CASE WHEN d.production_type =3 and d.embel_name=1 and d.production_date <='$txt_date_to'  THEN e.production_qnty  END)
		  AS total_print_rcv_qty,  

        (CASE WHEN d.production_type =2 and d.embel_name=2 and d.production_date ='$txt_date_to' THEN e.production_qnty  END)
		AS today_emb_send_qty,
		(CASE WHEN d.production_type =2 and d.embel_name=2 and d.production_date <='$txt_date_to'  THEN e.production_qnty  END)
		  AS total_emb_send_qty,
        (CASE WHEN d.production_type =3 and d.embel_name=2 and d.production_date ='$txt_date_to' THEN e.production_qnty  END)
		 AS today_emb_rcv_qty,
		(CASE WHEN d.production_type =3 and d.embel_name=2 and d.production_date <='$txt_date_to'  THEN e.production_qnty  END)
		  AS total_emb_rcv_qty ,

        (CASE WHEN d.production_type =2 and d.embel_name=3 and d.production_date ='$txt_date_to' THEN e.production_qnty  END)
		AS today_wash_send_qty,
		(CASE WHEN d.production_type =2 and d.embel_name=3 and d.production_date <='$txt_date_to'  THEN e.production_qnty  END)
		  AS total_wash_send_qty,
        (CASE WHEN d.production_type =3 and d.embel_name=3 and d.production_date ='$txt_date_to' THEN e.production_qnty  END)
		 AS today_wash_rcv_qty,
		(CASE WHEN d.production_type =3 and d.embel_name=3 and d.production_date <='$txt_date_to'  THEN e.production_qnty  END)
		  AS total_wash_rcv_qty
        FROM WO_PO_DETAILS_MASTER a,WO_PO_BREAK_DOWN b,WO_PO_COLOR_SIZE_BREAKDOWN c,PRO_GARMENTS_PRODUCTION_MST d,PRO_GARMENTS_PRODUCTION_DTLS e,  GBL_TEMP_ENGINE f
        Where a.id=b.job_id and a.id=c.job_id and b.id=c.PO_BREAK_DOWN_ID and b.ID=d.PO_BREAK_DOWN_ID and d.id=e.MST_ID and c.id=e.COLOR_SIZE_BREAK_DOWN_ID and d.po_break_down_id=f.ref_val and f.user_id=$user_id  and f.entry_form =103  and e.production_qnty!=0 and d.GARMENTS_NATURE in(2,3) and a.STATUS_ACTIVE=1 and a.IS_DELETED=0  and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and c.STATUS_ACTIVE=1 and c.IS_DELETED=0 and d.STATUS_ACTIVE=1 and d.IS_DELETED=0 and e.STATUS_ACTIVE=1 and e.IS_DELETED=0 $emb_cond  order by a.id,b.id,c.id";
        // echo $sql;die;
        $sql_result=sql_select($sql);
        $emb_arr=array();
        $buyer_array=array();
        $w_com_array=array();
        foreach ($sql_result as $row)
         {     
                $emb_arr[$row['SERVING_COMPANY']][$row['JOB_ID']][$row['PO_ID']][$row['ITEM_NUMBER_ID']]['job_no']=$row['JOB_NO_PREFIX_NUM'];

                $emb_arr[$row['SERVING_COMPANY']][$row['JOB_ID']][$row['PO_ID']][$row['ITEM_NUMBER_ID']]['po_number']=$row['PO_NUMBER'];

                $emb_arr[$row['SERVING_COMPANY']][$row['JOB_ID']][$row['PO_ID']][$row['ITEM_NUMBER_ID']]['shipment_date']=$row['PUB_SHIPMENT_DATE'];
        
                $emb_arr[$row['SERVING_COMPANY']][$row['JOB_ID']][$row['PO_ID']][$row['ITEM_NUMBER_ID']]['buyer_name']=$row['BUYER_NAME'];
        
                $emb_arr[$row['SERVING_COMPANY']][$row['JOB_ID']][$row['PO_ID']][$row['ITEM_NUMBER_ID']]['style_ref_no']=$row['STYLE_REF_NO'];
                
                $emb_arr[$row['SERVING_COMPANY']][$row['JOB_ID']][$row['PO_ID']][$row['ITEM_NUMBER_ID']]['grouping']=$row['GROUPING'];
              

                $emb_arr[$row['SERVING_COMPANY']][$row['JOB_ID']][$row['PO_ID']][$row['ITEM_NUMBER_ID']]['production_source']=$row['PRODUCTION_SOURCE'];
                
                $emb_arr[$row['SERVING_COMPANY']][$row['JOB_ID']][$row['PO_ID']][$row['ITEM_NUMBER_ID']]['today_print_send_qty']+=$row['TODAY_PRINT_SEND_QTY']; 
                
                $emb_arr[$row['SERVING_COMPANY']][$row['JOB_ID']][$row['PO_ID']][$row['ITEM_NUMBER_ID']]['total_print_send_qty']+=$row['TOTAL_PRINT_SEND_QTY'];
            
                
                $emb_arr[$row['SERVING_COMPANY']][$row['JOB_ID']][$row['PO_ID']][$row['ITEM_NUMBER_ID']]['today_print_rcv_qty']+=$row['TODAY_PRINT_RCV_QTY'];
            
            
                $emb_arr[$row['SERVING_COMPANY']][$row['JOB_ID']][$row['PO_ID']][$row['ITEM_NUMBER_ID']]['total_print_rcv_qty']+=$row['TOTal_PRINT_RCV_QTY'];

            
                $emb_arr[$row['SERVING_COMPANY']][$row['JOB_ID']][$row['PO_ID']][$row['ITEM_NUMBER_ID']]['today_emb_send_qty']+=$row['TODAY_EMB_SEND_QTY'];
            
                
            
                $emb_arr[$row['SERVING_COMPANY']][$row['JOB_ID']][$row['PO_ID']][$row['ITEM_NUMBER_ID']]['total_emb_send_qty']+=$row['TOTAL_EMB_SEND_QTY'];

            
                $emb_arr[$row['SERVING_COMPANY']][$row['JOB_ID']][$row['PO_ID']][$row['ITEM_NUMBER_ID']]['today_emb_rcv_qty']+=$row['TODAY_EMB_RCV_QTY'];
            

                $emb_arr[$row['SERVING_COMPANY']][$row['JOB_ID']][$row['PO_ID']][$row['ITEM_NUMBER_ID']]['total_emb_rcv_qty']+=$row['TOTAL_EMB_RCV_QTY'];

                $emb_arr[$row['SERVING_COMPANY']][$row['JOB_ID']][$row['PO_ID']][$row['ITEM_NUMBER_ID']]['today_wash_send_qty']+=$row['TODAY_WASH_SEND_QTY'];

                $emb_arr[$row['SERVING_COMPANY']][$row['JOB_ID']][$row['PO_ID']][$row['ITEM_NUMBER_ID']]['total_wash_send_qty']+=$row['TOTAL_WASH_SEND_QTY'];

            
                $emb_arr[$row['SERVING_COMPANY']][$row['JOB_ID']][$row['PO_ID']][$row['ITEM_NUMBER_ID']]['today_wash_rcv_qty']+=$row['TODAY_WASH_RCV_QTY'];
            
                $emb_arr[$row['SERVING_COMPANY']][$row['JOB_ID']][$row['PO_ID']][$row['ITEM_NUMBER_ID']]['total_wash_rcv_qty']+=$row['TOTAL_WASH_RCV_QTY'];

                $emb_arr[$row['SERVING_COMPANY']][$row['JOB_ID']][$row['PO_ID']][$row['ITEM_NUMBER_ID']]['total_embilshment']+=($row['TODAY_PRINT_SEND_QTY']  + $row['TOTAL_PRINT_SEND_QTY'] + $row['TODAY_PRINT_RCV_QTY']+ $row['TOTal_PRINT_RCV_QTY']+ $row['TODAY_EMB_SEND_QTY']+ $row['TODAY_EMB_SEND_QTY']+ $row['TODAY_EMB_RCV_QTY']+ $row['TOTAL_EMB_RCV_QTY'] + $row['TODAY_WASH_SEND_QTY']+ $row['TOTAL_WASH_SEND_QTY']+ $row['TODAY_WASH_RCV_QTY']+ $row['TOTAL_WASH_RCV_QTY']  );

                      
          

               // ----------------------------------------------------BUYER PART START------------------------------------//

            if($row['PRODUCTION_TYPE'] ==2 && $row['EMBEL_NAME'] ==1 )//Print send
            {		
                $buyer_array[$row['BUYER_NAME']]['print_qty_send']+=$row['PRODUCTION_QNTY'];
            }
    
            if($row['PRODUCTION_TYPE'] ==3 && $row['EMBEL_NAME'] ==1 )//Print rcv
            {		
                $buyer_array[$row['BUYER_NAME']]['print_qty_rcv']+=$row['PRODUCTION_QNTY'];
            }
    
            if($row['PRODUCTION_TYPE'] ==2 && $row['EMBEL_NAME'] ==2 )//emb send
            {		
                $buyer_array[$row['BUYER_NAME']]['emb_qty_send']+=$row['PRODUCTION_QNTY'];
            }
    
            if($row['PRODUCTION_TYPE'] ==3 && $row['EMBEL_NAME'] ==2 )//emb rcv
            {		
                $buyer_array[$row['BUYER_NAME']]['emb_qty_rcv'] +=$row['PRODUCTION_QNTY'];
            }
    
            if($row['PRODUCTION_TYPE'] ==2 && $row['EMBEL_NAME'] ==3 )//wash send
            {		
                $buyer_array[$row['BUYER_NAME']]['wash_send']+=$row['PRODUCTION_QNTY'];
            }
    
            if ($row['PRODUCTION_TYPE'] ==3 && $row['EMBEL_NAME'] ==3) //Wash Received
            {
                $buyer_array[$row['BUYER_NAME']]['wash_received'] += $row['PRODUCTION_QNTY'];
            }
            // echo"<pre>";print_r($buyer_array);die;
            
            // ----------------------------------------------------BUYER PART End------------------------------------//

            
                // -----------------------------COMPANY WISE SUMMERY FOR EMBELLISHMENT BUTTON START------------------------------------//

                // $w_com_array[$row['SERVING_COMPANY']]['production_source']=$row['PRODUCTION_SOURCE'];

                if($row['PRODUCTION_TYPE'] ==2 && $row['EMBEL_NAME'] ==1 )//Print send
                {		
                     $w_com_array[$row['SERVING_COMPANY']]['print_qty_send']+=$row['PRODUCTION_QNTY'];
                }

                if($row['PRODUCTION_TYPE'] ==3 && $row['EMBEL_NAME'] ==1 )//Print rcv
                {		
                    $w_com_array[$row['SERVING_COMPANY']]['print_qty_rcv']+=$row['PRODUCTION_QNTY'];
                }

                if($row['PRODUCTION_TYPE'] ==2 && $row['EMBEL_NAME'] ==2 )//emb send
                {		
                    $w_com_array[$row['SERVING_COMPANY']]['emb_qty_send']+=$row['PRODUCTION_QNTY'];
                }

                if($row['PRODUCTION_TYPE'] ==3 && $row['EMBEL_NAME'] ==2 )//emb rcv
                {		
                    $w_com_array[$row['SERVING_COMPANY']]['emb_qty_rcv']+=$row['PRODUCTION_QNTY'];
                }

                if($row['PRODUCTION_TYPE'] ==2 && $row['EMBEL_NAME'] ==3 )//wash send
                {		
                    $w_com_array[$row['SERVING_COMPANY']]['wash_send']+=$row['PRODUCTION_QNTY'];
                }

                if ($row['PRODUCTION_TYPE'] ==3 && $row['EMBEL_NAME'] ==3) //Wash Received
                {
                    $w_com_array[$row['SERVING_COMPANY']]['wash_received'] += $row['PRODUCTION_QNTY'];
                }
                // -----------------------------COMPANY WISE SUMMERY END------------------------------------//
           
    
         }
        // echo"<pre>";print_r($w_com_array);die;
        // echo"<pre>";print_r($emb_arr);die;
       
        
		?>    
        
                <div style="float:left; width:620px" id="scroll_body">
                    <table width="600" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
                        <thead>
                             <div style="width:600px; float:left; background-color:#87CEEB"><strong>Buyer Wise Summary</strong></div>
                            <tr>
                                
                                <th width="30"><p>Sl.<p></th>
                                <th width="120"><p>Buyer Name</p></th>
                                <th width="100"><p>Sent to Wash</p></th>
                                <th width="120"><p>Rev.Wash</p></th>                       
                                <th width="120"><p>Sent to Print<p></th>
                                <th width="120"><p>Rcv.from Print</p></th>
                                <th width="120"><p>Sent to Emb.</p></th>
                                <th width="120"><p>Rcv from Emb.</p></th>
                                
                                
                            </tr>
                            </thead>
                            <tbody>
                                <?
                                    $i=1;
                                    $com_ttl_print_qty_send= 0;
                                    $com_print_qty_rcv= 0;
                                    $com_emb_qty_send =0;
                                    $com_emb_qty_rcv= 0;
                                    $com_wash_send=0;
                                    $com_wash_rcv=0;
                                    foreach($buyer_array as $buyer_key => $r)
                                    {
                                        if ($i%2==0) $bgcolor="#E9F3FF";									
                                        else $bgcolor="#FFFFFF";	
                                        ?>
                                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                                <td width="30"><p><?=$i?><p></td>
                                                <td width="120"><p><?=$buyer_library[$buyer_key]?></p></td>
                                                <td width="100" align="right"><p><?=number_format($r['wash_send'],0);?></p></td>
                                                <td width="120" align="right"><p><?=number_format($r['wash_received'],0);?></p></td>                       
                                                <td width="120" align="right"><p><?=number_format($r['print_qty_send'],0);?><p></td>
                                                <td width="120" align="right"><p><?=number_format($r['print_qty_rcv'],0) ;?></p></td>
                                                <td width="120" align="right"><p><?=number_format($r['emb_qty_send'],0) ;?></p></td>
                                                <td width="120" align="right"><p><?=number_format($r['emb_qty_rcv'],0);?></p></td>
                                            </tr>

                                        <?
                                        $i++;
                                        $com_ttl_print_qty_send += $r['print_qty_send'];
                                        $com_print_qty_rcv += $r['print_qty_rcv'];
                                        $com_emb_qty_send  += $r['emb_qty_send'];
                                        $com_emb_qty_rcv += $r['emb_qty_rcv'];
                                        $com_wash_send +=$r['wash_send'];
                                        $com_wash_rcv +=$r['wash_received'];;

                                    }    
                                ?>                              
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th width="30"><p><p></th>
                                    <th width="120"><p>Total</p></th>
                                    <th width="100"><p><?=$com_wash_send;?></p></th>
                                    <th width="120"><p><?=$com_wash_rcv;?></p></th>                       
                                    <th width="120"><p><?=$com_ttl_print_qty_send;?><p></th>
                                    <th width="120"><p><?=$com_print_qty_rcv;?></p></th>
                                    <th width="120"><p><?=$com_emb_qty_send;?></p></th>
                                    <th width="120"><p><?=$com_emb_qty_rcv;?></p></th>
                                

                                </tr>

                            </tfoot>
                    </table>
                </div>
                <div style="float:left; width:620px;">
                    <table width="800" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
                        <thead>
                    
                            <div style="width:800px; float:left; background-color:#9FE2BF"><strong>Emb. Company Wise Summery</strong></div>
                            <tr>
                                
                                <th width="30"><p>Sl.<p></th>
                                <th width="200"><p>Embl.Company</p></th>
                                <th width="100"><p>Sent to Wash</p></th>
                                <th width="120"><p>Rev.Wash</p></th>                       
                                <th width="120"><p>Sent to Print<p></th>
                                <th width="120"><p>Rcv.from Print</p></th>
                                <th width="120"><p>Sent to Emb.</p></th>
                                <th width="120"><p>Rcv from Emb.</p></th>
                                
                                
                            </tr>
                        </thead>
                        <tbody>
                            <?
                                 $k=1;
                                 
                                foreach($w_com_array as $com_key => $row)
                                {
                                    if ($k%2==0) $bgcolor="#E9F3FF";									
                                    else $bgcolor="#FFFFFF";	
                                    ?>
                                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('etr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="etr_<? echo $k; ?>">
                                            <td width="30"><p><?=$k?><p></td>
                                            <?
                                            if($row['production_source']==1)
                                            {
                                                ?>
                                                
                                                <td  width="200"><p><?=$company_library[$com_key];?></p></td>
                                                <?
                                            }else
                                            {
                                                ?>
                                                
                                                <td  width="200"><p><?=$supllier_library[$com_key];?></p></td>
                                                <?
                                            }
                                                        
                                            ?>
                                            <td width="100" align="right"><p><?=$row['wash_send'];?></p></td>
                                            <td width="120" align="right"><p><?=$row['wash_received'];?></p></td>                       
                                            <td width="120" align="right"><p><?=$row['print_qty_send'];?><p></td>
                                            <td width="120" align="right"><p><?=$row['print_qty_rcv'];?></p></td>
                                            <td width="120" align="right"><p><?=$row['emb_qty_send'];?></p></td>
                                            <td width="120" align="right"><p><?=$row['emb_qty_rcv'];?></p></td>
                                        </tr>

                                    <?
                                    $k++;
                                     $ttl_print_qty_send += $row['print_qty_send'];
                                     $ttl_print_qty_rcv += $row['print_qty_rcv'];
                                     $ttl_emb_qty_send  += $row['emb_qty_send'];
                                     $ttl_emb_qty_rcv += $row['emb_qty_rcv'];
                                     $ttl_wash_send +=$row['wash_send'];
                                     $ttl_wash_rcv +=$row['wash_received'];;

                                }    
                            ?>
                            
                        </tbody>
                        <tfoot>
                            <tr>
                                <th width="30"><p><p></th>
                                <th width="200"><p>Total</p></th>
                                <th width="100"><p><?=$ttl_wash_send;?></p></th>
                                <th width="120"><p><?=$ttl_wash_rcv;?></p></th>                       
                                <th width="120"><p><?=$ttl_print_qty_send;?><p></th>
                                <th width="120"><p><?=$ttl_print_qty_rcv;?></p></th>
                                <th width="120"><p><?=$ttl_emb_qty_send;?></p></th>
                                <th width="120"><p><?=$ttl_emb_qty_rcv;?></p></th>
                            

                            </tr>

                        </tfoot>
                    </table>
                    <div style="margin-top: 20px;"></div>
            </div>
          
            <div>           
                <table width="2050" cellspacing="0" border="1" class="rpt_table" rules="all">
                    <thead>
                        <div style="width:2050px; float:left; background-color:#0096FF"><strong>Production-Regular Order Details</strong></div>
                        <tr>
                            <th  width="30"><p>SL</p></th>
                            <th  width="200"><p>Emb. Company</p></th>
                            <th  width="80"><p>Job No</p></th>
                            <th  width="130"><p>Order No</p></th>
                            <th  width="80"><p>Ship Date</p></th>
                            <th  width="120"><p>Buyer Name</p></th>
                            <th  width="120"><p>Style Name</p></th>
                            <th  width="100"><p>Internal Ref</p></th>                       
                            <th  width="130"><p>Item Name</p></th>
                            <th  width="120"><p>Today Send  Wash</p></th>
                            <th  width="140"><p>Total Send Wash</p></th>
                            <th  width="110"><p>Today Wash Rcv.</p></th>
                            <th  width="100"><p>Total Wash Rcv.</p></th>
                            <th  width="140"><p>Today Send to Print</p></th>
                            <th  width="120"><p>Total Send to Print</p></th>
                            <th  width="120"><p>Today Print Rcv.</p></th>
                            <th  width="120"><p>Total Print Rcv.</p></th>
                            <th  width="140"><p>Today Send to Emb.</p></th>
                            <th  width="120"><p>Total Send to Emb.</p></th>
                            <th  width="120"><p>Today Emb Rcv.</p></th>
                            <th  width="120"><p>Total Emb Rcv.</p></th>

                            

                        </tr>
                    </thead>
                                   
                    <tbody>
                         <?
                            $j=1;
                            $today_wash_send_qty=0;
                            $total_wash_send_qty=0;
                            $today_wash_rcv_qty=0;
                            $total_wash_rcv_qty=0;

                            $today_print_send_qty=0;
                            $total_print_send_qty=0;
                            $today_print_rcv_qty=0;
                            $total_print_rcv_qty=0;

                            $today_emb_send_qty=0;
                            $total_emb_send_qty=0;
                            $today_emb_rcv_qty=0;
                            $total_emb_rcv_qty=0;
                            

                            foreach ($emb_arr as $w_id => $w_data) 
                            {
                                foreach ($w_data as $job_id => $job_data) 
                                {
                                    foreach ($job_data as $po_id => $po_data) 
                                    {
                                        foreach ($po_data as $item_id => $v) 
                                        {
                                            
                                            if ($j%2==0) $bgcolor="#E9F3FF";									
                                            else $bgcolor="#FFFFFF";	
                                            if($v['total_embilshment'] > 0)
                                            { 
                                                ?>
                                                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('str_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="str_<? echo $j; ?>">

                                                    <td width="30"><p><?=$j?><p></td>
                                                    <?
                                                    if($v['production_source']==1)
                                                    {
                                                        ?>
                                                        
                                                        <td  width="120"><p><?=$company_library[$w_id];?></p></td>
                                                        <?
                                                    }else
                                                    {
                                                        ?>
                                                        
                                                        <td  width="120"><p><?=$supllier_library[$w_id];?></p></td>
                                                        <?
                                                    }
                                                                
                                                    ?>
                                                    <td  width="80" align="center"><p><?=$v['job_no'];?></p></td>
                                                    <td  width="130" align="center"><p><?=$v['po_number'];?></p></td>
                                                    <td  width="80"><p><?=$v['shipment_date'];?></p></td>
                                                    <td  width="120"><p><?=$buyer_library[$v['buyer_name']];?></p></td>
                                                    <td  width="120"><p><?=$v['style_ref_no'];?></p></td>
                                                    <td  width="100"><p><?=$v['grouping'];?></p></td>                       
                                                    <td  width="130"><p><?=$item_library[$item_id];?></p></td>
                                                    <td  width="120"  align="right"><p><?=$v['today_wash_send_qty'];?></p></td>
                                                    <td  width="140" align="right"><p><?=$v['total_wash_send_qty'];?></p></td>
                                                    <td  width="110" align="right"><p><?=$v['today_wash_rcv_qty'];?></p></td>
                                                   <td  width="100" align="right"><p><?=$v['total_wash_rcv_qty'];?></p></td>

                                                    <td  width="140" align="right"><p><?=$v['today_print_send_qty'];?></p></td>
                                                    <td  width="120" align="right"><p><?=$v['total_print_send_qty'];?></p></td>
                                                    <td  width="120" align="right"><p><?=$v['today_print_rcv_qty'];?></p></td>
                                                    <td  width="120" align="right"><p><?=$v['total_print_rcv_qty'];?></p></td>

                                                    <td  width="140" align="right"><p><?=$v['today_emb_send_qty'];?></p></td>
                                                    <td  width="120" align="right"><p><?=$v['total_emb_send_qty'];?></td>
                                                    <td  width="120" align="right"><p><?=$v['today_emb_rcv_qty'];?></p></td>
                                                    <td  width="120" align="right"><p><?=$v['total_emb_rcv_qty'];?></p></td>

                                                </tr>
                                                <?
                                            }
                                            ?>                                        
                                                
                                            <?
                                            $j++;
                                            $today_wash_send_qty += $v['today_wash_send_qty'];
                                            $total_wash_send_qty += $v['total_wash_send_qty'];
                                            $today_wash_rcv_qty += $v['today_wash_rcv_qty'];
                                            $total_wash_rcv_qty += $v['total_wash_rcv_qty'];
                
                                            $today_print_send_qty += $v['today_print_send_qty'];
                                            $total_print_send_qty += $v['total_print_send_qty'];
                                            $today_print_rcv_qty += $v['today_print_rcv_qty'];
                                            $total_print_rcv_qty += $v['total_print_rcv_qty'];
                
                                            $today_emb_send_qty += $v['today_emb_send_qty'];
                                            $total_emb_send_qty += $v['total_emb_send_qty'];
                                            $today_emb_rcv_qty += $v['today_emb_rcv_qty'];
                                            $total_emb_rcv_qty += $v['total_emb_rcv_qty'];
                                            
                                        }  
                                        
                                    }  
                                }
                            }    
                        ?>
                        
                    </tbody>
                    <tfoot>
                        <tr>
                            <th  width="30"><p></p></th>
                            <th  width="130"><p></p></th>
                            <th  width="80"><p></p></th>
                            <th  width="130"><p></p></th>
                            <th  width="80"><p></p></th>
                            <th  width="120"><p></p></th>
                            <th  width="120"><p></p></th>
                            <th  width="100"><p></p></th>                       
                            <th  width="130"><p>Total</p></th>
                            <th  width="120"><p><?=$today_wash_send_qty;?></p></th>
                            <th  width="140"><p><?=$total_wash_send_qty;?></p></th>
                            <th  width="110"><p><?=$today_wash_rcv_qty;?></p></th>
                            <th  width="100"><p><?=$total_wash_rcv_qty;?></p></th>

                            <th  width="140"><p><?=$today_print_send_qty;?></p></th>
                            <th  width="120"><p><?=$total_print_send_qty;?></p></th>
                            <th  width="120"><p><?=$today_print_rcv_qty;?></p></th>
                            <th  width="120"><p><?=$total_print_rcv_qty;?></p></th>

                            <th  width="140"><p><?=$today_emb_send_qty;?></p></th>
                            <th  width="120"><p><?=$total_emb_send_qty;?></p></th>
                            <th  width="120"><p><?=$today_emb_rcv_qty;?></p></th>
                            <th  width="120"><p><?=$total_emb_rcv_qty;?></p></th>


                        </tr>
                        
                    </tfoot>
                </table> 
            </div>
        </div>      
            
            
                <?

    }
	// -------------------------------------EMBELLISHMENT END------------------------------------------------//

    $html = ob_get_contents();
    ob_clean();
    foreach (glob("$user_id*.xls") as $filename) {
        @unlink($filename);
    }
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html****$filename****$type";

	exit();   
}		



if($action=='cutting_popup')
{
	echo load_html_head_contents("Style Owner Wise Production Report", "../../../", 1, 1,$unicode,'','');
    //print_r($_REQUEST);die;
 	extract($_REQUEST);
	// echo "<pre>";print_r($_REQUEST);die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  ); 
    $floor_library=return_library_array( "select id,FLOOR_NAME from  LIB_PROD_FLOOR", "id", "FLOOR_NAME"  ); 
	$order_no=return_field_value( "po_number",'wo_po_break_down a' ,"a.id= $po_id"); 
    $txt_date_from=str_replace("'","", $txt_date_from);
	$txt_date_to=str_replace("'","", $txt_date_to);
    $date_cond="";
    if($txt_date_from !="" && $txt_date_to !="" )
    {
		$date_cond=" and a.cutting_qc_date between'$txt_date_from' and '$txt_date_to'";
        
	}	


	$cutting_sql=("SELECT a.serving_company,a.cutting_qc_date,b.country_id,b.qc_pass_qty, b.color_id, b.size_id,a.floor_id ,b.order_id,b.item_id
	from pro_gmts_cutting_qc_mst a,  pro_gmts_cutting_qc_dtls b
	where   a.serving_company=$w_com and b.item_id=$item_id  and b.order_id='$po_id' and b.color_id=$color_id and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond 
	order by b.color_id,b.country_id");
    // echo $cutting_sql;die;
    $sql_result=sql_select($cutting_sql);
     //echo $cutting_sql;die;
   $cutting_array=array();
   $size_Array=array();
   $po_id_array=array();
	foreach($sql_result as $row)
	{
        $po_id_array[$row['PO_ID']]=$row['PO_NUMBER'];
		$cutting_array[$row['FLOOR_ID']][$row['COUNTRY_ID']][$row['COLOR_ID']][$row['SIZE_ID']] +=$row['QC_PASS_QTY'];

        $size_Array[$row['SIZE_ID']]=$row['SIZE_ID'];
	}
	// echo "<pre>";print_r($po_id_arrayy);die;

	$col_width=60*count($size_Array);
	$table_width=530+$col_width;
    ?>
	<script>

    function print_window()
    {
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
    '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
        d.close();
    }

    function window_close()
    {
        parent.emailwindow.hide();
    }

    </script>	

	<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
        <input type="button" value="Print" onClick="print_window()" style="width:100px; margin-left:400px;"  class="formbutton" />
        <br />
	    <div  id="report_container">
	    
	        <table cellpadding="0" cellspacing="0" border="1" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
	        	<tr>
	            	<td style="font-size:16px; font-weight:bold;">
                   
                        Order No : <? echo  $order_no; ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        
                    </td>
                   
                    <td style="font-size:16px; font-weight:bold;">Date : <? echo change_date_format($production_date); ?> 
	                </td>
	            </tr>
	        </table>
	        
	        <table cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all"bor width="<? echo $table_width; ?>">
	            <thead>
	                <tr>
	                	<th width="40" rowspan="2">SI</th>
	                    <th width="100" rowspan="2">Color</th>
	                    <th width="100" rowspan="2">Country Name</th>
                        <th width="100" rowspan="2">Floor Name</th>
	                	<th width="<? echo $col_width; ?>" colspan="<? echo count($size_Array); ?>">Size</th>
	                    <th width="80" rowspan="2" >Total</th>
	                </tr>
	                <tr>
	                <?
					$grand_tot_in=0;
					foreach($size_Array as $size_id)
					{
						?>
	                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
	                    <?
					}
					?>
	                </tr>
	                
	            </thead>
	            <tbody>
	            <?
				$i=1;
				$k=1;
				// echo "<pre>";
				// print_r($details_data);
				foreach($cutting_array as $floor_id=>$floor_data)
				{
					foreach($floor_data as $country_id=>$country_data)
					{
                        foreach($country_data as $color_id=>$v)
					    {                           
                                ?>
                                    <tr>
                                        <td align="center"><? echo $i;  ?></td>                                   
                                        <td align="left" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>    
                                        <td ><p><? echo $country_library[$country_id];  ?></p></td>
                                        <td ><p><? echo $floor_library[$floor_id];  ?></p></td>
                                        <?
                                        $color_total_in=0;
                                        foreach($size_Array as $size_id)
                                        {
                                            ?>
                                             <td align="right"><p>
                                                    <?
                                                        echo number_format($cutting_array[$floor_id][$country_id][$color_id][$size_id],0);
                                                        $total_qty +=$cutting_array[$floor_id][$country_id][$color_id][$size_id];
                                                         $line_floor_size_in [$size_id]+=$cutting_array[$floor_id][$country_id][$color_id][$size_id];
                                                         ?>
                                                    </p>
                                                </td>     
                                            <?                         	                      
                                        }
                                        $floor_color_total_in +=$total_qty;       
                                            ?>
                                      
                                    <td align="right"><p><? echo  number_format( $total_qty,0);  $grand_tot_in+=$total_qty;?></p></td>
                                
                            </tr>
                            <?
                            $i++;
                        }
                          ?>
					
                        <tr bgcolor="#CCCCCC">
                            <td >&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td align="right" style="font-weight:bold">Floor Total:</td>
                            <?
                            foreach($size_Array as $size_id)
                            {
                                ?>
                                <td align="right"><? echo number_format($line_floor_size_in[$size_id],0); ?></td>
                                <?
                            }
                            ?>
                            <td align="right"><? echo number_format($floor_color_total_in,0); ?></td>
                        </tr>
                        <?
                    }                            
                            
				}
				?>
	            </tbody>
                <tfoot>
                    <tr bgcolor="#dccdcd">
                            <td >&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td align="right" style="font-weight:bold">Grand Total:</td>
                            <?
                            foreach($size_Array as $size_id)
                            {
                                ?>
                                <td align="right"><? echo number_format($line_floor_size_in[$size_id],0); ?></td>
                                <?
                            }
                            ?>
                            <td align="right"><? echo number_format($grand_tot_in,0); ?></td>
                    </tr>
                
                </tfoot>
	            
	        </table>
	    </div>
	</fieldset>
	<?	
}


if($action=="sewingQnty_popup")
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	// var_dump($_REQUEST);
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"); 
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"); 
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
	$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name");
	$sewing_line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	
	$sizearr_order=return_library_array("select size_number_id, size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");
	//var_dump();die;
	
	$po_details_sql=sql_select("SELECT a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id,b.grouping,b.po_number from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.id=$po_id ");
    //echo $po_details_sql;die;
	// echo"<pre>";
	// print_r($po_details_sql);die;
	$serving_company_sql=sql_select("select a.company_id from pro_garments_production_mst a, wo_po_break_down b where a.po_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.id=$po_id group by a.company_id");
	$serving_company_id=$serving_company_sql[0][csf("company_id")];
	// echo"<pre>";
	// print_r($serving_company_id);die;
	// echo $serving_company_id;die;
	//For Show Date Location and Floor 
	$ex_item_id=explode("__",$gmts_item_id);
	$gmt_item_id=$ex_item_id[0];
	$serving_comp_id=$ex_item_id[1];

    $txt_date_from=str_replace("'","", $txt_date_from);
    $txt_date_to=str_replace("'","", $txt_date_to);
    $date_cond="";
    if($txt_date_from !="" && $txt_date_to !="" )
    {
        $date_cond=" and a.production_date between'$txt_date_from' and '$txt_date_to'";
        
    }	
	
	$sql= "SELECT a.challan_no, a.floor_id, a.sewing_line, a.country_id, a.PRODUCTION_SOURCE, a.serving_company, c.color_number_id, c.size_number_id, a.prod_reso_allo ,b.PRODUCTION_QNTY
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c 
	where a.production_type=5 and a.serving_company=$w_com and a.item_number_id=$item_id  and a.po_break_down_id='$po_id' and c.color_number_id=$color_id and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond ";

    // echo $sql;die;
    $sql_result=sql_select($sql);
    $sweing_array=array();
    $size_Array=array();

    foreach($sql_result as $row)
    {
       
        $sweing_array[$row['FLOOR_ID']][$row['SEWING_LINE']][$row['CHALLAN_NO']][$row['PRODUCTION_SOURCE']][$row['COUNTRY_ID']][$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']] +=$row['PRODUCTION_QNTY'];

        $size_Array[$row['SIZE_NUMBER_ID']]=$row['SIZE_NUMBER_ID'];

         $summery_data_arr[$row['COLOR_NUMBER_ID']][$row['SIZE_NUMBER_ID']] +=$row['PRODUCTION_QNTY'];

    }

    // echo"<pre>";
	// print_r($sweing_array);die;

	 $nameArray = sql_select("select id, auto_update from variable_settings_production where company_name='$serving_company_id' and variable_list=23 and status_active=1 and is_deleted=0");
         $prod_reso_allocation = $nameArray[0][csf('auto_update')];
    // $prod_reso_allocation = 1;
	
	// echo $sql; //and a.production_date='$production_date'
	$result=sql_select($sql);
	
	
	$col_width=60*count($size_Array);
	//$table_width=630+$col_width;
	if($prod_source==3) $table_width=850+$col_width; else $table_width=630+$col_width;
	$summer_table_width=330+$col_width;
	?>	
	<script>

		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
		}
		
		function window_close()
		{
			parent.emailwindow.hide();
		}
		
	</script>	
	<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
			<input type="button" value="Print" onClick="print_window()" style="width:100px; margin-left:400px;"  class="formbutton" /><br />
	        <div  id="report_container">
	    
	        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
	        	<tr>
	            	<td style="font-size:14px; font-weight:bold;">
	                Buyer Name : <? echo $buyer_library[$po_details_sql[0][csf("buyer_name")]]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Job No : <? echo $po_details_sql[0][csf("job_no")]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Internal Ref : <? echo $po_details_sql[0][csf("grouping")]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Style No : <? echo $po_details_sql[0][csf("style_ref_no")]; ?>&nbsp;&nbsp;&nbsp;&nbsp; Garments Item : 
					<?
	                    $item_data=""; 
	                    $garments_item_arr=array_unique(explode(",",$po_details_sql[0][csf("gmts_item_id")])); 
	                    foreach($garments_item_arr as $item_id)
	                    {
	                        if($item_data!="") $item_data .=", ";
	                        $item_data .=$garments_item[$item_id];
	                    }
	                    echo $item_data;
	                ?>
	                <br />
	                Order No : <? echo $po_details_sql[0][csf("po_number")]; ?>&nbsp;&nbsp;&nbsp;&nbsp;Date : <? echo change_date_format($production_date); ?> 
	                <br />
	                Summary
	                </td>
	            </tr>
	        </table>
	        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $summer_table_width; ?>" style="margin-bottom:20px;">
	            <thead>
	                <tr>
	                	<th width="40" rowspan="2">SI</th>
	                    <th width="100" rowspan="2">Color</th>
	                	<th width="<? echo $col_width; ?>" colspan="<? echo count($size_Array); ?>">Size</th>
	                    <th width="80" rowspan="2" >Total</th>
	                </tr>
	                <tr>
	                <?
					foreach($size_Array as $size_id)
					{
						?>
	                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
	                    <?
					}
					?>
	                </tr>
	            </thead>
	            <tbody>
	            <?
				$i=1;
				//var_dump($result);die;
				foreach($summery_data_arr as $color_id=>$row)
				{
					?>
	                <tr>
	                    <td valign="middle" align="center"><? echo $i;  ?></td>
	                    <td valign="middle" ><? echo $colorarr[$color_id];  ?></td>
	                    <?
						$summry_color_total_in =0;
	                    foreach($size_Array as $size_id)
	                    {
	                        ?>
	                        <td valign="middle" align="right"><? echo number_format($row[$size_id],0) ; $summry_color_total_in+= $row[$size_id]; $summry_color_size_in [$size_id]+=$row[$size_id];?></td>
	                        <?
	                    }
	                    ?>
	                    <td valign="middle" align="right"><? echo  number_format( $summry_color_total_in,0); $grand_tot_in+=$summry_color_total_in; ?></td>
	                </tr>
	                <?
					$i++;
				}
				?>
	            </tbody>
	            <tfoot>
	                <th colspan="2">Total :</th>
	                <?
					foreach($size_Array as $size_id)
					{
						?>
	                	<th ><? echo $summry_color_size_in[$size_id]; ?></th>
	                    <?
					}
					?>
	                <th><? echo $grand_tot_in; ?></th>
	            </tfoot>
	        </table>
	        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px;">
	        	<tr>
	            	<td style="font-size:14px; font-weight:bold;">Details</td>
	            </tr>
	        </table>
	        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
	            <thead>
	                <tr>
	                	<th width="40" rowspan="2">SI</th>
	                    <th width="100" rowspan="2">Country Name</th>
	                    <th width="80" rowspan="2">Source</th>	                    
	                    <th width="70" rowspan="2">Challan</th>                    			
						<th width="90" rowspan="2">Sewing Floor</th>
						<th width="70" rowspan="2">Sewing Line</th>
	                    <th width="100" rowspan="2">Color</th>
	                	<th width="<? echo $col_width; ?>" colspan="<? echo count($size_Array); ?>">Size</th>
	                    <th width="80" rowspan="2" >Total</th>
	                </tr>
	                <tr>
	                <?
					$grand_tot_in=0;
					foreach($size_Array as $size_id)
					{
						?>
	                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
	                    <?
					}
					?>
	                </tr>
	                
	            </thead>
	            <tbody>
                    <?
                    $i=1;
                    foreach ($sweing_array as $floor_id => $floor_data)
                    {
                        foreach ($floor_data as $line_id => $line_data)
                        {
                            foreach ($line_data as $chalan_id => $chalan_data)
                            {
                                foreach ($chalan_data as $pro_source => $pro_source_data)
                                {
                                    foreach ($pro_source_data as $country_id=> $country_data)
                                    {   
                                        foreach ($country_data as $color_id=> $row)
                                        {
                                            ?>
                                                <tr>
                                                    <td valign="middle" align="center"><? echo $i;  ?></td>
                                                    <td valign="middle" ><p><? echo $country_library[$country_id];?></p></td>
                                                    <td valign="middle" ><p><? echo $knitting_source[$pro_source]; ?><p></td>
                                                    <td valign="middle" ><p><? echo $chalan_id;  ?></p></td>
                                                    <td valign="middle" ><p><? echo $floor_library[$floor_id];  ?></p></td>
                                                    <td valign="middle" align="center"><p><? echo $sewing_line_library[$line_id];  ?></p></td>
                                                    <td valign="middle" ><p><? echo $colorarr[$color_id];  ?></p></td>
                                                    <?
                                                    $color_total_in=0;
                                                    
                                                    foreach($size_Array as $size_id)
                                                    {
                                                        $sewing_qty=0;
                                                        ?>
                                                        <td valign="middle" align="right"><p>
                                                        <?
                                                            $sewing_qty=$sweing_array[$floor_id][$line_id][$chalan_id][$pro_source][$country_id][ $color_id][$size_id];
                                                            echo number_format($sewing_qty,0);
                                                            $color_total_in +=$sewing_qty;
                                                            $color_size_in[$size_id] +=$sewing_qty;
                                                            $line_color_total_in +=$sewing_qty;
                                                            $line_color_size_in[$size_id] +=$sewing_qty;
                                                        ?>
                                                        </p></td>
                                                        <?
                                                    }
                                                    ?>
                                                    <td valign="middle" align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
                                                </tr>
                                            <?
                                            $i++;
                                        }    
                                         ?>
                                            <tr bgcolor="#CCCCCC">
                                                <td valign="middle" align="center"></td>
                                                <td valign="middle" ></td>
                                                <td valign="middle" ></td>
                                                <td valign="middle" ></td>
                                                <td valign="middle" ></td>
                                                <td valign="middle" ></td>
                                                <td align="right"><strong>Floor Total :</strong></td>
                            
                                                <?
                                                foreach($size_Array as $size_id)
                                                {
                                                    ?>
                                                    <td align="right"><? echo number_format($line_color_size_in[$size_id],0); ?></td>
                                                    <?
                                                }
                                                ?>
                                                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
                                            </tr>
                                         <?
                                    }
                                
                                }
                            
                            }
                           
                        }
                       
                    }
	                
				
				?>
	            
	            </tbody>
	            <tfoot>
                    <th valign="middle" align="center"></th>
                    <th valign="middle" ></th>
                    <th valign="middle" ></th>
                    <th valign="middle" ></th>
                    <th valign="middle" ></th>
                    <th valign="middle" ></th>
                    <th  align="right"><strong>Grand Total :</strong></th>

                <?
					foreach($size_Array as $size_id)
					{
						?>
	                	<th ><? echo $color_size_in[$size_id]; ?></th>
	                    <?
					}
					?>
	                <th ><? echo $grand_tot_in; ?></th>
	            </tfoot>
	        </table>
	        </div>
	    </fieldset>
	<?
	exit();	
}
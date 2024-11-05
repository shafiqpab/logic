<?php
include('../../../includes/common.php');  
session_start();

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$company_library = return_library_array("select id,company_name from lib_company", "id", "company_name");
$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
$color_arr = return_library_array("select id,color_name from lib_color", "id", "color_name");
$count_arr = return_library_array("Select id, yarn_count from  lib_yarn_count where  status_active=1", 'id', 'yarn_count');
$brand_arr = return_library_array("Select id, brand_name from  lib_brand where  status_active=1", 'id', 'brand_name');
$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');

if($action=="process_popup")
{
echo load_html_head_contents("Process Popup","../../../", 1, 1, $unicode);
extract($_REQUEST);
?>
<script>
    var selected_id = new Array();
	var  selected_name = new Array();
     function check_all_data() 
     {
            $("#tbl_list_search tr").each(function(){
                var valTP=$(this).attr("id");
                $("#"+valTP).click();

                var row_num = $('#tbl_list_search tbody tr').length;
        });
        }
		
        function toggle( x, origColor ) 
		{
            var newColor = 'yellow';
            if ( x.style )
			{
                x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
            }
        }

        function js_set_value( str ) 
		{
 			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
             if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 )
			 {
                selected_id.push( $('#txt_individual_id' + str).val() );
                selected_name.push( $('#txt_individual_name' + str).val() );

            }
            else 
			{
                for( var i = 0; i < selected_id.length; i++ ) 
				{
                    if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
                }
                selected_id.splice( i, 1 );
                selected_name.splice( i, 1 );
            }
            var id = '';
            var name='';
            for( var i = 0; i < selected_id.length; i++ ) 
			{
                id += selected_id[i] + ',';
                name += selected_name[i] + ',';
            }
            id = id.substr( 0, id.length - 1 );
            name = name.substr( 0, name.length - 1 );

            $('#txt_selected_id').val( id );
            $('#txt_selected_name').val( name );
        }
</script>
</head>
<body>
<div align="center">
<form>
<input type="hidden" id="txt_selected_id" name="txt_selected_id" value="<? echo $txtprocess;?> " />
<input type="hidden" id="txt_selected_name" name="txt_selected_name" value="<? echo $txtprocessname;?> " />
<table width="250" cellspacing="0" class="rpt_table" border="0"   rules="all">
    <thead>
        <tr>
            <th width="50">Sl</th>
            <th>Process Name</th>
        </tr>
    </thead>
    </table>
    <table width="250" cellspacing="0" class="rpt_table" border="0" id="tbl_list_search" rules="all">
    <tbody>
    <?
    $i=1;
    $selected_process = '';
    foreach($yarn_dyeing_process as $key => $value)
    {

        if ($i%2==0)
            $bgcolor="#E9F3FF";
        else
            $bgcolor="#FFFFFF";
            if($yd_process==1)
			{
                if($key==1 || $key==2 || $key==3 || $key==4 || $key==5)
				{
                    $selected_process .=$key.',';
                }
            }
            else  if($yd_process==2)
			{
                if($key==2 || $key==3 || $key==4)
				{
                    $selected_process .=$key.',';
                }
            }
            else if($yd_process==3)
			{
                if($key==4 || $key==5 || $key==6)
				{
                    $selected_process .=$key.',';
                }
            }
			else if($yd_process==4)
			{
                if($key==1 || $key==2 || $key==3 || $key==4 || $key==5)
				{
                    $selected_process .=$key.',';
                }
            }
        ?>
            <tr bgcolor="<? echo $bgcolor;  ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $key;?>" onClick="js_set_value(<? echo $key;?>)">
                <td width="50" align="center"><? echo $i ?></td>
                <td align="center">
                <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $key; ?>" value="<? echo $key; ?>"/>
                <input type="hidden" name="txt_individual_name" id="txt_individual_name<?php echo $key; ?>" value="<? echo $value; ?>"/> 
                <? echo $value; ?> 
                </td>
            </tr>
        <?
        $i++;
    }
    ?>
    </tbody>
</table>
<br>
<table width="250" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
</form>
</div>
</body>
<script>
var txt_branch='<? echo $txtprocess; ?>'
var txt_branch_name='<? echo $txtprocessname; ?>'
var selected_process = '<?php echo $selected_process;?>'
selected_process=selected_process.split(",");
if(txt_branch !="")
{
  selected_id=txt_branch.split(",");
  selected_name=txt_branch_name.split(",");
}
if(txt_branch==''){
    for(var i=0; i<selected_process.length;i++)
    {
        js_set_value(selected_process[i]);
    }
}
else{
    for(var i=0; i<selected_id.length;i++)
    {
       if(selected_id[i] !="")
       {
        toggle( document.getElementById( 'search' + selected_id[i] ), '#FFFFCC' );
       }
    }
}
</script>
<script>setFilterGrid('tbl_list_search',-1);</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
    <?
}

if ($action == "check_conversion_rate") {
    $data = explode("**", $data);
    if ($db_type == 0) {
        $conversion_date = change_date_format($data[1], "Y-m-d", "-", 1);
    } else {
        $conversion_date = change_date_format($data[1], "d-M-y", "-", 1);
    }
    $exchange_rate = set_conversion_rate($data[0], $conversion_date, $data[2]);
    //$exchange_rate = set_conversion_rate($data[0], $conversion_date);
    echo $exchange_rate;
    exit();
}


if ($action=="load_drop_down_member")
{
    $data=explode("_",$data);
    $sql="select b.id,b.team_member_name  from lib_marketing_team a, lib_mkt_team_member_info b where a.id=b.team_id and   a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.project_type=10 and team_id='$data[0]'";
    echo create_drop_down( "cbo_team_member", 150, $sql,"id,team_member_name", 1, "-- Select Member --", $selected, "" );   
    exit();
}

if ($action=="load_drop_down_location")
{
    $data=explode("_",$data);
    if($data[1]==1) $dropdown_name="cbo_location_name";
    else $dropdown_name="cbo_party_location";
    
    echo create_drop_down( $dropdown_name, 150, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );   
    exit();
}

if ($action=="load_drop_down_count")
{
    $data=explode("_",$data);

    $within_group=$data[0];
	$check_type=$data[1];
	$is_sales_check=$data[2];

    if ($within_group==2) 
	{
       $sql="select distinct(b.id) as id,b.yarn_count from lib_yarn_count b where b.status_active=1 and b.is_deleted=0";
   }
   else
   {
		if($is_sales_check==1)
		{
			$sql="select distinct(b.id) as id,b.yarn_count from fabric_sales_order_yarn_dtls a, lib_yarn_count b where  a.yarn_count_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		
		}
		else
		{
			$sql="select distinct(b.id) as id,b.yarn_count from lib_yarn_count b where b.status_active=1 and b.is_deleted=0";
		}
    }
    
    echo create_drop_down( "cboCount_1", 80, $sql,"id,yarn_count", 1, "-- Select --","","",0,'','','','','','',"cboCount[]");   
    exit();
}

if ($action=="load_drop_down_yarn_type")
{
    
     echo create_drop_down( "cboYarnType_1", 80, $yarn_type,"", 1, "-- Select --","","",0,'','','','','','',"cboYarnType[]");   
    exit();
}
if ($action=="load_drop_down_composition")
{
    
     echo create_drop_down( "cboComposition_1", 80, $composition,"", 1, "-- Select --","","",0,'','','','','','',"cboComposition[]");;  
    exit();
}


if ($action=="load_drop_down_buyer")
{
    $data=explode("_",$data);
    //company+'_'+1
    if($data[1]==1) $load_function="fnc_load_party(2,document.getElementById('cbo_within_group').value);re_set_shade_rate();";
    else $load_function="";
    //$company_cond 
    if($data[1]==1)
    {
        //echo  "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name";
        echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name","id,company_name", 1, "-- Select Company --", $data[2], "$load_function");
    }
    else
    {
        echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "re_set_shade_rate();" );
    }   
    exit();  
} 



if ($action=="job_popup")
{
    echo load_html_head_contents("Job Popup Info","../../../", 1, 1, $unicode,'','');
    ?>
    <script>
        function js_set_value(id)
        { 
            $("#hidden_mst_id").val(id);
            document.getElementById('selected_job').value=id;
            parent.emailwindow.hide();
        }
        
        function fnc_load_party_popup(type,within_group)
        {
            var company = $('#cbo_company_name').val();
            var party_name = $('#cbo_party_name').val();
            var location_name = $('#cbo_location_name').val();
            load_drop_down( 'yd_order_entry_controller', company+'_'+within_group, 'load_drop_down_buyer', 'buyer_td' );
        }
		
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('YD Job No');
			else if(val==2) $('#search_by_td').html('W/O No');
			else if(val==3) $('#search_by_td').html('Buyer Style');
			else if(val==4) $('#search_by_td').html('Buyer Job');
		}
    </script>
</head>
<body>
<div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="940" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead> 
                <tr>
                    <th colspan="8"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr>
                <tr>                 
                    <th width="140" class="must_entry_caption">Company Name</th>
                    <th width="80">Within Group</th>                           
                    <th width="100">Party Name</th>
                    <th width="100">Search By</th>
                    <th width="60" id="search_by_td">YD Job No</th>
                     <th width="60" style="display:none">YD Worder No</th>
                    <th width="100">Year</th>
                    <th width="180">Date Range</th>                            
                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                </tr>           
            </thead>
            <tbody>
                <tr class="general">
                    <td><input type="hidden" id="selected_job"><? $data=explode("_",$data); ?>
                     <!--  echo $data;-->
                        <? 
                        echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $data[0], "fnc_load_party_popup(1,document.getElementById('cbo_within_group').value);",1); ?>
                    </td>
                    <td>
                        <?php echo create_drop_down( "cbo_within_group", 80, $yes_no,"", 1, "-- Select --", '', "fnc_load_party_popup(1,this.value);" ); ?>
                    </td>
                    <td id="buyer_td">
                        <?
                        
                        echo create_drop_down( "cbo_party_name", 100,$blank_array,"", 1, "-- Select Party --",'', "" );      
                        ?>
                    </td>
                    <td>
                        <?
                            $search_by_arr=array(1=>"YD Job No",2=>"W/O No",3=>"Buyer Style",4=>"Buyer Job");
                            echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                        ?>
                    </td>
                    <td align="center">
                        <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:80px" placeholder="" />
                    </td>
                    <td align="center" style="display:none">
                        <input type="text" name="txt_search_order" id="txt_search_order" class="text_boxes" style="width:80px" placeholder="" />
                    </td>
                    <td align="center"><? echo create_drop_down( "cbo_year_selection", 100, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                    </td>
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_search_order').value+'_'+'<? echo $data[4];?>'+'_'+document.getElementById('cbo_string_search_type').value, 'create_yd_search_list_view', 'search_div', 'yd_order_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:70px;" /></td>
                    </tr>
                    <tr>
                        <td colspan="8" align="center" valign="middle">
                            <? echo load_month_buttons();  ?>
                            <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                        </td>
                    </tr>  
                </tbody>
            </table>    
            </form>
        </div>
        <div id="search_div"></div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if ($action == "create_yd_search_list_view")
{
    $contact_person = return_library_array("select id, contact_person from lib_supplier", 'id', 'contact_person');
    $ex_data    = explode("_", $data);
    $company    = $ex_data[0];
    $party      = $ex_data[1];
    $fromDate   = $ex_data[2];
    $toDate     = $ex_data[3];
    //$yd_job_no     = $ex_data[5];
    $withinGroup = $ex_data[6];
    $yd_year = $ex_data[7];
    $yd_order = $ex_data[8];
	$search_type 			=trim(str_replace("'","",$ex_data[10]));
	
	
	$search_by 				=trim(str_replace("'","",$ex_data[4]));
	$search_str 			=trim(str_replace("'","",$ex_data[5]));
	//echo $search_type ;
    $sql_cond='';
    if($company!=0) $sql_cond.=" and a.company_id=$company"; 
    else { echo "Please Select Company First."; die; }

    if($withinGroup != 0) $sql_cond.= " and a.within_group=$withinGroup";
    if($yd_job_no != '') $sql_cond.= " and a.job_no_prefix_num=$yd_job_no";
    
    if($party != 0) $sql_cond.= " and a.party_id='$party'";
    if($yd_order != '') $sql_cond= " and a.order_no LIKE '%$yd_order%'";
    if($db_type==0){ 
        if ($fromDate!="" &&  $toDate!="") $sql_cond .= "and a.receive_date between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
        $ins_year_cond="year(a.insert_date)";
    }else{
        if ($fromDate!="" &&  $toDate!="") $sql_cond .= "and a.receive_date between '".change_date_format($fromDate, "", "",1)."' and '".change_date_format($toDate, "", "",1)."'";
        $ins_year_cond="TO_CHAR(a.insert_date,'YYYY')"; 
    }
    if($yd_year != 0) $sql_cond.= " and $ins_year_cond=$yd_year";
	
	 
	 
	 
	 
	 if($search_type==1)
    {
        if($search_str!="")
        {
            if($search_by==1) $condition="and a.yd_job='$search_str'";
			else if($search_by==2) $condition="and a.order_no='$search_str'";
			else if ($search_by==3) $condition=" and b.style_ref = '$search_str' ";
			else if ($search_by==4) $condition=" and b.sales_order_no = '$search_str' ";
        }
        
    }
    else if($search_type==2)
    {
        if($search_str!="")
        {
            if($search_by==1) $condition="and a.yd_job like '$search_str%'";
			else if($search_by==2) $condition="and a.order_no like '$search_str%'";
			else if ($search_by==3) $condition=" and b.style_ref like  '$search_str%' ";
			else if ($search_by==4) $condition=" and b.sales_order_no like  '$search_str%' ";
        }
        
    }
    else if($search_type==3)
    {
        if($search_str!="")
        {
            if($search_by==1) $condition="and a.yd_job like '%$search_str'";
			else if($search_by==2) $condition="and a.order_no like '%$search_str'";
			else if ($search_by==3) $condition=" and b.style_ref like  '%$search_str' ";
			else if ($search_by==4) $condition=" and b.sales_order_no like  '%$search_str' ";
        }
        
    }
    else if($search_type==4 || $search_type==0)
    {
        if($search_str!="")
        {
            if($search_by==1) $condition="and a.yd_job like '%$search_str%'";
			else if($search_by==2) $condition="and a.order_no like '%$search_str%'";
			else if ($search_by==3) $condition=" and b.style_ref like  '%$search_str%' ";
			else if ($search_by==4) $condition=" and b.sales_order_no like  '%$search_str%' ";
        }
        
    }

	 
	
	
    $sql= "select a.id, a.yd_job, a.job_no_prefix_num,a.within_group, $ins_year_cond as year, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date ,a.booking_without_order, a.booking_type,a.order_type, 
   a.yd_process, a.yd_type,a.pro_type,b.style_ref,b.count_type,b.sales_order_no from yd_ord_mst a,yd_ord_dtls b where a.id=b.mst_id and a.entry_form=374 and a.status_active =1 and a.is_deleted =0 $sql_cond $condition group by  a.id, a.yd_job, a.job_no_prefix_num,a.within_group, a.insert_date, a.party_id, a.location_id, a.receive_date, a.order_no, a.delivery_date ,a.booking_without_order, a.booking_type,a.order_type, 
   a.yd_process, a.yd_type,a.pro_type,b.style_ref,b.count_type,b.sales_order_no order by a.id DESC";
    $data_array=sql_select($sql);  
    ?>
    <div style="width:1350px;"  align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1300" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="120">Job No/Sales order no</th>
                <th width="80">Job Suffix</th>
                <th width="100">WO No.</th>
                <th width="100">Buyer Style</th>
                <th width="100">Buyer Job</th> 
                <th width="60">Within Group</th>
                <th width="100">Party</th> 
                <th width="80">Prod. Type</th>
                <th width="80">Order Type</th>
                <th width="80">Y/D Type</th>
                <th width="100">Y/D Process</th>
                <th width="80">Count Type</th>
                <th width="80">Ord. Receive Date</th>
                <th>Delivery Date</th>
            </thead>
        </table>
        <div style="width:1350px;  overflow-y:scroll; max-height:300px;" id="buyer_list_view">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1300" class="rpt_table"
            id="tbl_list_search">
            <?
            $i = 1;
            foreach ($data_array as $selectResult)
            {
                if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                $within_group=$selectResult[csf('within_group')];
                if($within_group==1)
                {
                    $com_buyer=$company_library[$selectResult[csf('party_id')]];
                }
                else
                {
                    $com_buyer=$buyer_arr[$selectResult[csf('party_id')]];
                }
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
                    id="search<? echo $i; ?>" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>+'_'+'<? echo $selectResult[csf('yd_job')]; ?>'+'_'+'<? echo $selectResult[csf('within_group')]; ?>'+'_'+'<? echo $selectResult[csf('order_no')]; ?>'); ">
                    <td width="30" align="center"><p><? echo $i; ?></p></td>
                    <td width="120" align="center"><p> <? echo $selectResult[csf('yd_job')]; ?></p></td>
                    <td width="80" align="center"><p> <? echo $selectResult[csf('job_no_prefix_num')]; ?></p></td>
                    <td width="100" align="center"><p> <? echo $selectResult[csf('order_no')]; ?></p></td>
                    <td width="100" align="center"><p> <? echo $selectResult[csf('style_ref')]; ?></p></td>
                    <td width="100" align="center"><p> <? echo $selectResult[csf('sales_order_no')]; ?></p></td>
                    <td width="60" align="center"><p> <? echo $yes_no[$selectResult[csf('within_group')]]; ?></p></td>
                    <td width="100" align="center"><p> <? echo $com_buyer; ?></p></td>
                    <td width="80" align="center"><p> <? echo $w_pro_type_arr[$selectResult[csf('pro_type')]]; ?></p></td>
                    <td width="80" align="center"><p> <? echo $w_order_type_arr[$selectResult[csf('order_type')]]; ?></p></td>
                    <td width="80" align="center"><p> <? echo $yd_type_arr[$selectResult[csf('yd_type')]]; ?></p></td>
                    <td width="100" align="center"><p> <? echo $yd_process_arr[$selectResult[csf('yd_process')]]; ?></p></td>
                    <td width="80" align="center"><p> <? echo $count_type_arr[$selectResult[csf('count_type')]]; ?></p></td>
                    <td width="80"><p><? echo change_date_format($selectResult[csf('receive_date')]); ?></p></td>
                    <td><p><? echo change_date_format($selectResult[csf('delivery_date')]); ?></p></td>
                </tr>
                <?
                $i++;
            }
            ?>
        </table>
        </div>
    </div>
    <? exit();
}

if ($action == "order_popup") 
{
    echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);
    extract($_REQUEST);

    /*if ($db_type == 0) $select_field_grp = "group by a.id order by supplier_name";
    else if ($db_type == 2) $select_field_grp = "group by a.id,a.supplier_name order by supplier_name";

    $current_date = date('d-m-Y');
    $previous_day = date("d-m-Y", strtotime(date("d-m-Y") . '-60 days'));*/
    ?>

    <script>
        /*function set_checkvalue() {
            if (document.getElementById('chk_job_wo_po').value == 0)
                document.getElementById('chk_job_wo_po').value = 1;
            else
                document.getElementById('chk_job_wo_po').value = 0;
        }*/

        function js_set_value(id) {
            $("#hidden_sys_number").val(id);
            //$("#hidden_id").val(id);
            parent.emailwindow.hide();
        }
        var permission = '<? echo $permission; ?>';
    </script>
</head>
<body>
    <div style="width:900px;" align="center">
        <form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
            <table width="800" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                <thead>
                    <tr>
                        <th colspan="2"></th>
                        <th>
                            <?
                            echo create_drop_down("cbo_search_category", 130, $string_search_type, '', 1, "-- Search Catagory --");
                            ?>
                        </th>
                        <th colspan="3"></th>
                        <th colspan="2" style="text-align:right; display: none"><input type="checkbox" value="0" onClick="set_checkvalue();" id="chk_job_wo_po">WO Without Job
                        </th>
                    </tr>
                    <tr>
                        <th width="170">Supplier Name</th>
                        <th width="100">Booking No</th>
                        <th width="100">Job No/Sales Order No</th>
                        <th width="200">Date Range</th>
                        <th align="center"></th>
                        <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('searchorderfrm_1','search_div','','','','');"/>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="general">
                        <td>
                            <?
                            if($cbo_within_group==1)
                            {
                                echo create_drop_down( "cbo_supplier_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "",0);
                            }
                            else
                            {
                                echo create_drop_down("cbo_supplier_name", 140, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_tag_company c where a.id=c.supplier_id and c.tag_company=$company and a.status_active =1 and a.id in(select supplier_id from lib_supplier_party_type where party_type=2) and a.id in(select supplier_id from lib_supplier_party_type where party_type=21) group by a.id,a.supplier_name order by supplier_name", "id,supplier_name", 1, "-- Select Supplier --", $selected, "", 0);
                            }
                            ?>
                        </td>
                        <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:100px"></td>
                        <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:100px"></td>
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date"/>
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date"/>
                        </td>
                        <td align="center">
                            <input type="checkbox" name="chkIsSales" id="chkIsSales"/> <label for="chkIsSales">Is sales order </label>
                        </td>
                        <td >
                            <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $company; ?>'+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('chkIsSales').checked, 'create_sys_search_list_view', 'search_div', 'yd_order_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;"/>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" valign="middle" colspan="6">
                            <? echo load_month_buttons(1); ?>
                            <input type="hidden" id="hidden_sys_number" value="hidden_sys_number"/>
                            <input type="hidden" id="hidden_id" value="hidden_id"/>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div id="search_div"></div>
            </form>
        </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if ($action == "create_sys_search_list_view_old")
{
    $contact_person = return_library_array("select id, contact_person from lib_supplier", 'id', 'contact_person');
    $ex_data = explode("_", $data);
    $supplier = $ex_data[0];
    $fromDate = $ex_data[1];
    $toDate = $ex_data[2];
    $company = $ex_data[3];
    //$buyer_val=$ex_data[4];
    $chk_job_wo_po = trim($ex_data[8]);

    if ($supplier != 0) $supplier = "and a.supplier_id='$supplier'"; else  $supplier = "";
    if ($company != 0) $company = " and a.company_id='$company'"; else  $company = "";
    if ($buyer_val != 0) $buyer_cond = "and d.buyer_name='$buyer_val'"; else  $buyer_cond = "";
    if ($db_type == 0) {
        $booking_year_cond = " and SUBSTRING_INDEX(a.insert_date, '-', 1)=$ex_data[7]";
        $year_cond = " and SUBSTRING_INDEX(d.insert_date, '-', 1)=$ex_data[7]";
        if ($fromDate != 0 && $toDate != 0) $sql_cond = "and a.booking_date  between '" . change_date_format($fromDate, 'yyyy-mm-dd') . "' and '" . change_date_format($toDate, 'yyyy-mm-dd') . "'";
    }
    if ($db_type == 2) {
        $booking_year_cond = " and to_char(a.insert_date,'YYYY')=$ex_data[7]";
        $year_cond = " and to_char(d.insert_date,'YYYY')=$ex_data[7]";
        if ($fromDate != 0 && $toDate != 0) $sql_cond = "and a.booking_date  between '" . change_date_format($fromDate, 'mm-dd-yyyy', '/', 1) . "' and '" . change_date_format($toDate, 'mm-dd-yyyy', '/', 1) . "'";
    }

    if ($ex_data[4] == 4 || $ex_data[4] == 0) {
        if (str_replace("'", "", $ex_data[6]) != "") $job_cond = " and d.job_no_prefix_num like '%$ex_data[6]%' $year_cond "; else  $job_cond = "";
        if (str_replace("'", "", $ex_data[5]) != "") $booking_cond = " and a.yarn_dyeing_prefix_num like '%$ex_data[5]%'  $booking_year_cond  "; else $booking_cond = "";
    }
    if ($ex_data[4] == 1) {
        if (str_replace("'", "", $ex_data[6]) != "") $job_cond = " and d.job_no_prefix_num ='$ex_data[6]' "; else  $job_cond = "";
        if (str_replace("'", "", $ex_data[5]) != "") $booking_cond = " and a.yarn_dyeing_prefix_num ='$ex_data[5]'   "; else $booking_cond = "";
    }
    if ($ex_data[4] == 2) {
        if (str_replace("'", "", $ex_data[6]) != "") $job_cond = " and d.job_no_prefix_num like '$ex_data[6]%'  $year_cond"; else  $job_cond = "";
        if (str_replace("'", "", $ex_data[5]) != "") $booking_cond = " and a.yarn_dyeing_prefix_num like '$ex_data[5]%'  $booking_year_cond  "; else $booking_cond = "";
    }
    if ($ex_data[4] == 3) {
        if (str_replace("'", "", $ex_data[6]) != "") $job_cond = " and d.job_no_prefix_num like '%$ex_data[6]'  $year_cond"; else  $job_cond = "";
        if (str_replace("'", "", $ex_data[5]) != "") $booking_cond = " and a.yarn_dyeing_prefix_num like '%$ex_data[5]'  $booking_year_cond  "; else $booking_cond = "";
    }

    if ($db_type == 0) $select_year = "year(a.insert_date) as year"; else $select_year = "to_char(a.insert_date,'YYYY') as year";
    if ($chk_job_wo_po == 1) {
        $sql = "select a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode, a.source, a.attention, $select_year, 0 as job_no_id, null as job_no, 0 as buyer_name, null as po_number  
        from wo_yarn_dyeing_mst a
        where a.status_active=1 and a.is_deleted=0 and a.entry_form=135 and a.id not in(select mst_id from wo_yarn_dyeing_dtls where job_no_id>0 and entry_form=135  and status_active=1 and  is_deleted=0) $company $supplier  $sql_cond  $booking_cond order by a.id DESC";
    } else {

        $sql = "select a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,TO_CHAR(a.insert_date,'YYYY') as year, LISTAGG(CAST(b.job_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.job_no) as job_no, LISTAGG(CAST(b.job_no_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.job_no_id) as job_no_id
            from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b 
            where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.entry_form=41 and b.entry_form=41 $company $supplier $sql_cond  $buyer_cond $job_cond $booking_cond
            group by a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,a.insert_date order by a.id DESC";


       
       /*if ($db_type == 0) {
            $sql = "select a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,year(a.insert_date) as year,group_concat(distinct b.job_no_id) as job_no_id, group_concat(distinct b.job_no) as job_no,d.buyer_name, group_concat(distinct e.po_number) as po_number,d.within_group  
            from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b, fabric_sales_order_mst d
            where a.id=b.mst_id and b.job_no_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.entry_form=135 and b.entry_form=135 $company $supplier $sql_cond  $buyer_cond $job_cond $booking_cond
            group by a.id order by a.id DESC";
        } //LISTAGG(CAST(b.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as tr_id
        else if ($db_type == 2) {
            $sql = "select a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,TO_CHAR(a.insert_date,'YYYY') as year, LISTAGG(CAST(b.job_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.job_no) as job_no, LISTAGG(CAST(b.job_no_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.job_no_id) as job_no_id,d.job_no as sales_job,d.buyer_id,d.within_group
            from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b, fabric_sales_order_mst d
            where a.id=b.mst_id and b.job_no_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and a.entry_form=135 and b.entry_form=135  $company $supplier  $sql_cond  $buyer_cond  $job_cond $booking_cond
            group by a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,a.insert_date,d.job_no,d.buyer_id,d.within_group order by a.id DESC";
        }*/
        //echo $sql;
        $nameArray = sql_select($sql);
        $all_job_id = "";
        foreach ($nameArray as $row) {
            $all_job_id .= $row[csf("job_no_id")] . ",";
            $sales_no_arr[$row[csf("job_no_id")]] = $row[csf("job_no_id")];
        }
        //echo $all_job_id;die;
        $all_job_id = array_chunk(array_unique(explode(",", chop($all_job_id, ","))), 999);

        $po_sql = "select p.mst_id as mst_id, b.id, b.po_number from wo_yarn_dyeing_dtls p, fabric_sales_order_mst a, wo_po_break_down b where p.job_no_id=a.id and a.job_no=b.job_no_mst";
        $p = 1;
        foreach ($all_job_id as $job_id) {
            //$po_sql
            if ($p == 1) $po_sql .= " and (a.id in(" . implode(',', $job_id) . ")"; else $po_sql .= " or a.id in(" . implode(',', $job_id) . ")";
            $p++;
        }
        $po_sql .= ")";

        //echo $po_sql;die;

        $po_result = sql_select($po_sql);
        $po_data = array();
        foreach ($po_result as $row) {
            $po_data[$row[csf("mst_id")]] .= $row[csf("po_number")] . ",";
        }
    }
    $supplier_arr=return_library_array( "select id,supplier_name from lib_supplier where is_deleted=0 and status_active=1", "id", "supplier_name");
    ?>
    <div style="width:930px;" align="center">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="930" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="60">Booking no Prefix</th>
                <th width="40">Year</th>
                <th width="150">Booking No</th>
                <th width="200">Sales Order No</th>
                <th width="150">Supplier Name</th>
                <th width="100">Booking Date</th>
                <th>Delevary Date</th>
            </thead>
        </table>
        <div style="width:930px; margin-left:3px; overflow-y:scroll; max-height:300px;" id="buyer_list_view">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="910" class="rpt_table"
            id="tbl_list_search">
            <?

            $i = 1;
            //$nameArray = sql_select($sql);
                //var_dump($nameArray);die;
            foreach ($nameArray as $selectResult)
            {
                $job_no = implode(",", array_unique(explode(",", $selectResult[csf("job_no")])));
                $job_no_id = implode(",", array_unique(explode(",", $selectResult[csf("job_no_id")])));
                if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
                    id="search<? echo $i; ?>" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>+'_'+'<? echo $selectResult[csf('ydw_no')]; ?>'+'_'+'<? echo $selectResult[csf('within_group')]; ?>'); ">

                    <td width="30" align="center"><p><? echo $i; ?></p></td>
                    <td width="60" align="center"><p> <? echo $selectResult[csf('yarn_dyeing_prefix_num')]; ?></p></td>
                    <td width="40" align="center"><p> <? echo $selectResult[csf('year')]; ?></p></td>
                    <td width="150"><p><? echo $selectResult[csf("ydw_no")]; ?></p></td>
                    <td width="200" style="word-break:break-all"> <? echo $job_no ;//$selectResult[csf('sales_job')]; ?></td>
                    <td width="150" style="word-break:break-all"><? echo $supplier_arr[$selectResult[csf('supplier_id')]]; ?></td>
                    <td width="100"><p><? echo change_date_format($selectResult[csf('booking_date')]); ?></p></td>
                    <td><p><? echo change_date_format($selectResult[csf('delivery_date')]); ?></p></td>
                </tr>
                <?
                $i++;
            }
            ?>
        </table>
        </div>
    </div>
    <? exit();
}


if ($action == "create_sys_search_list_view")
{
    $contact_person = return_library_array("select id, contact_person from lib_supplier", 'id', 'contact_person');
    $ex_data = explode("_", $data);
    $supplier = $ex_data[0];
    $fromDate = $ex_data[1];
    $toDate = $ex_data[2];
    $company = $ex_data[3];
    //$buyer_val=$ex_data[4];
    $chk_job_wo_po = trim($ex_data[8]);
    $is_sales = $ex_data[9];
	
	
	//echo $is_sales; die;
    
    if ($supplier != 0) $supplier = "and a.company_id='$supplier'"; else  $supplier = "";
    if ($company != 0) $company = " and a.supplier_id='$company'"; else  $company = "";
    if ($buyer_val != 0) $buyer_cond = "and d.buyer_name='$buyer_val'"; else  $buyer_cond = "";
    if ($db_type == 0) {
        $booking_year_cond = " and SUBSTRING_INDEX(a.insert_date, '-', 1)=$ex_data[7]";
        $year_cond = " and SUBSTRING_INDEX(d.insert_date, '-', 1)=$ex_data[7]";
        if ($fromDate != 0 && $toDate != 0) $sql_cond = "and a.booking_date  between '" . change_date_format($fromDate, 'yyyy-mm-dd') . "' and '" . change_date_format($toDate, 'yyyy-mm-dd') . "'";
    }
    if ($db_type == 2) {
        $booking_year_cond = " and to_char(a.insert_date,'YYYY')=$ex_data[7]";
        $year_cond = " and to_char(d.insert_date,'YYYY')=$ex_data[7]";
        if ($fromDate != 0 && $toDate != 0) $sql_cond = "and a.booking_date  between '" . change_date_format($fromDate, 'mm-dd-yyyy', '/', 1) . "' and '" . change_date_format($toDate, 'mm-dd-yyyy', '/', 1) . "'";
    }

    if ($ex_data[4] == 4 || $ex_data[4] == 0) {
        if (str_replace("'", "", $ex_data[6]) != "") $job_cond = " and d.job_no_prefix_num like '%$ex_data[6]%' $year_cond "; else  $job_cond = "";
        if (str_replace("'", "", $ex_data[5]) != "") $booking_cond = " and a.yarn_dyeing_prefix_num like '%$ex_data[5]%'  $booking_year_cond  "; else $booking_cond = "";
    }
    if ($ex_data[4] == 1) {
        if (str_replace("'", "", $ex_data[6]) != "") $job_cond = " and d.job_no_prefix_num ='$ex_data[6]' "; else  $job_cond = "";
        if (str_replace("'", "", $ex_data[5]) != "") $booking_cond = " and a.yarn_dyeing_prefix_num ='$ex_data[5]'   "; else $booking_cond = "";
    }
    if ($ex_data[4] == 2) {
        if (str_replace("'", "", $ex_data[6]) != "") $job_cond = " and d.job_no_prefix_num like '$ex_data[6]%'  $year_cond"; else  $job_cond = "";
        if (str_replace("'", "", $ex_data[5]) != "") $booking_cond = " and a.yarn_dyeing_prefix_num like '$ex_data[5]%'  $booking_year_cond  "; else $booking_cond = "";
    }
    if ($ex_data[4] == 3) {
        if (str_replace("'", "", $ex_data[6]) != "") $job_cond = " and d.job_no_prefix_num like '%$ex_data[6]'  $year_cond"; else  $job_cond = "";
        if (str_replace("'", "", $ex_data[5]) != "") $booking_cond = " and a.yarn_dyeing_prefix_num like '%$ex_data[5]'  $booking_year_cond  "; else $booking_cond = "";
    }

    if ($db_type == 0) $select_year = "year(a.insert_date) as year"; else $select_year = "to_char(a.insert_date,'YYYY') as year";

    if($is_sales=="true")
	{
        if ($chk_job_wo_po == 1) 
		{
            $sql = "select a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode, a.source, a.attention, $select_year, 0 as job_no_id, null as job_no, 0 as buyer_name, null as po_number  
            from wo_yarn_dyeing_mst a
            where a.status_active=1 and a.is_deleted=0 and a.entry_form=135 and a.status_active =1 and a.is_deleted =0 and a.id not in(select mst_id from wo_yarn_dyeing_dtls where job_no_id>0 and entry_form=135  and status_active=1 and  is_deleted=0) $company $supplier  $sql_cond  $booking_cond order by a.id DESC";
        } 
		else 
		{
            if ($db_type == 0) 
			{
                $sql = "select a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,year(a.insert_date) as year,group_concat(distinct b.job_no_id) as job_no_id, group_concat(distinct b.job_no) as job_no,d.buyer_name, group_concat(distinct e.po_number) as po_number,d.within_group  
                from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b, fabric_sales_order_mst d
                where a.id=b.mst_id and b.job_no_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted =0 and d.status_active =1 and d.is_deleted =0 and a.entry_form=135 and b.entry_form=135 $company $supplier  $sql_cond  $buyer_cond $job_cond $booking_cond
                group by a.id order by a.id DESC";
            }
            else if ($db_type == 2) 
			{
                $sql = "select a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,TO_CHAR(a.insert_date,'YYYY') as year, LISTAGG(CAST(b.job_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.job_no) as job_no, LISTAGG(CAST(b.job_no_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.job_no_id) as job_no_id,d.job_no as sales_job,d.buyer_id,d.within_group
                from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b, fabric_sales_order_mst d
                where a.id=b.mst_id and b.job_no_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted =0 and d.status_active =1 and d.is_deleted =0  and a.entry_form=135 and b.entry_form=135 $company $supplier  $sql_cond  $buyer_cond  $job_cond $booking_cond
                group by a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,a.insert_date,d.job_no,d.buyer_id,d.within_group order by a.id DESC";
            }
            $nameArray = sql_select($sql);
            $all_job_id = "";
            foreach ($nameArray as $row) {
                $all_job_id .= $row[csf("job_no_id")] . ",";
                $sales_no_arr[$row[csf("job_no_id")]] = $row[csf("job_no_id")];
            }
            //echo $all_job_id;die;
            $all_job_id = array_chunk(array_unique(explode(",", chop($all_job_id, ","))), 999);
    
            $po_sql = "select p.mst_id as mst_id, b.id, b.po_number from wo_yarn_dyeing_dtls p, fabric_sales_order_mst a, wo_po_break_down b where p.job_no_id=a.id and a.job_no=b.job_no_mst";
            $p = 1;
            foreach ($all_job_id as $job_id) 
			{
                //$po_sql
                if ($p == 1) $po_sql .= " and (a.id in(" . implode(',', $job_id) . ")"; else $po_sql .= " or a.id in(" . implode(',', $job_id) . ")";
                $p++;
            }
            $po_sql .= ")";
    
            //echo $po_sql;die;
    
            $po_result = sql_select($po_sql);
            $po_data = array();
            foreach ($po_result as $row) {
                $po_data[$row[csf("mst_id")]] .= $row[csf("po_number")] . ",";
            }
        }
    }
    else
	{
        $sql = "select a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,TO_CHAR(a.insert_date,'YYYY') as year, 
        LISTAGG(CAST(b.job_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.job_no) as job_no, LISTAGG(CAST(b.job_no_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.job_no_id) as job_no_id,b.job_no as sales_job,d.BUYER_NAME as buyer_id,null as within_group from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b, WO_PO_DETAILS_MASTER d where a.id=b.mst_id and b.job_no_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted =0 and a.entry_form=41 and b.entry_form=41 $company $supplier  $sql_cond  $buyer_cond  $job_cond $booking_cond group by a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,a.insert_date,b.job_no,d.BUYER_NAME order by a.id DESC";
    }
	
	
	if($is_sales=="true")
	{
		$is_sales_check=1;
	}
	else
	{
		$is_sales_check=0;
	}
    ?>
    <div style="width:930px;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="930" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="60">Booking no Prefix</th>
                <th width="40">Year</th>
                <th width="120">Booking No</th>
                <th width="200">Job No/Sales Order No</th>
                <th width="130">PO Company</th>
                <th width="130">Supplier Name</th>
                <th width="70">Booking Date</th>
                <th>Delevary Date</th>
            </thead>
        </table>
        <div style="width:930px; overflow-y:scroll; max-height:300px;" id="buyer_list_view">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="930" class="rpt_table"
            id="tbl_list_search">
            <?

            $i = 1;
            $nameArray = sql_select($sql);
            //var_dump($nameArray);die;
            foreach ($nameArray as $selectResult)
            {
                $job_no = implode(",", array_unique(explode(",", $selectResult[csf("job_no")])));
                $job_no_id = implode(",", array_unique(explode(",", $selectResult[csf("job_no_id")])));
                if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                $supplier_or_company="";
                if($selectResult[csf("pay_mode")]==3 || $selectResult[csf("pay_mode")]==5) $supplier_or_company=$company_library[$selectResult[csf("supplier_id")]];
                else $supplier_or_company=$supplier_arr[$selectResult[csf("supplier_id")]];

                if($is_sales=="true"){
                    $company_id = $company_library[$selectResult[csf('buyer_id')]];
                }
                else
                {
                    $company_id = $company_library[$selectResult[csf('company_id')]];
                }

                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
                    id="search<? echo $i; ?>" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>+'_'+'<? echo $selectResult[csf('ydw_no')]; ?>'+'_'+'<? echo $selectResult[csf('within_group')]; ?>'+'_'+'<? echo $is_sales_check; ?>'); ">

                    <td width="30" align="center"><p><? echo $i; ?></p></td>
                    <td width="60" align="center"><p> <? echo $selectResult[csf('yarn_dyeing_prefix_num')]; ?></p></td>
                    <td width="40" align="center"><p> <? echo $selectResult[csf('year')]; ?></p></td>
                    <td width="120"><p><? echo $selectResult[csf("ydw_no")]; ?></p></td>
                    <td width="200" style="word-break:break-all"> <? echo $selectResult[csf('sales_job')]; ?></td>
                    <td width="130"><p> <? echo $company_id; ?></p></td>
                    <td width="130" style="word-break:break-all"><? echo $supplier_or_company; ?></td>
                    <td width="70"><p><? echo change_date_format($selectResult[csf('booking_date')]); ?></p></td>
                    <td><p><? echo change_date_format($selectResult[csf('delivery_date')]); ?></p></td>
                </tr>
                <?
                $i++;
            }
            ?>
        </table>
        </div>
    </div>
    <? exit();
}

if ($action=="populate_master_from_data")
{
    //echo $action."nazim"; die;
    $data=explode('_',$data);
    /*$nameArray=sql_select( "select id, booking_type, booking_no, company_id, buyer_id, job_no, booking_date,currency_id from  wo_booking_mst where booking_type=6 and status_active=1 and is_deleted=0 and id='$data[0]'" );*/
    //echo "select id, ydw_no, supplier_id, company_id, currency, booking_without_order from  wo_yarn_dyeing_mst where and status_active=1 and is_deleted=0 and id='$data[0]'"; 
    $nameArray=sql_select( "select id, ydw_no, supplier_id, company_id, currency, booking_without_order, pay_mode from  wo_yarn_dyeing_mst where status_active=1 and is_deleted=0 and id='$data[0]' and  status_active =1 and  is_deleted =0" );
    $booking_type=1;
    foreach ($nameArray as $row) 
    {   
        echo "document.getElementById('txt_order_no').value         = '".$row[csf("ydw_no")]."';\n";  
        
        echo "document.getElementById('hid_order_id').value         = '".$row[csf("id")]."';\n";
        echo "document.getElementById('hid_is_without_order').value = '".$row[csf("booking_without_order")]."';\n";
        echo "document.getElementById('cbo_party_name').value       = '".$row[csf("company_id")]."';\n";
        echo "document.getElementById('cbo_currency').value         = '".$row[csf("currency")]."';\n";
        echo "document.getElementById('hid_booking_type').value     = '".$booking_type."';\n";

        if($row[csf("pay_mode")]==5 || $row[csf("pay_mode")]==3)
        {
            echo "load_drop_down( 'requires/yd_order_entry_controller', ".$row[csf("company_id")]."+'_'+2, 'load_drop_down_location', 'party_location_td' );\n";

            echo "$('#cbo_party_name').attr('disabled', false);\n";
        }

        
    }
    exit(); 
}

if ($action=="populate_job_master_from_data")
{
    //echo $action."nazim"; die;
    //$data=explode('_',$data);
    $sql="select a.id, a.entry_form, a.yd_job, a.job_no_prefix, a.job_no_prefix_num, a.company_id, a.location_id, a.within_group, a.party_id, a.party_location, a.currency_id, a.receive_date, a.delivery_date, a.rec_start_date, a.rec_end_date, a.order_id, a.order_no, a.booking_without_order, a.booking_type, a.remarks, a.check_box_id, a.exchange_rate, a.tag_pi_no, a.order_type,a.yd_type,a.yd_process, a.attention, a.team_leader, a.team_member, a.party_ref,a.pro_type,a.check_box_confirm,a.check_box_advance, a.advance_job, sum(b.order_quantity) as order_quantity from  yd_ord_mst a, yd_ord_dtls b where a.entry_form=374 and a.status_active=1 and a.is_deleted=0 and a.id='$data' and a.id=b.mst_id group by a.id, a.entry_form, a.yd_job, a.job_no_prefix, a.job_no_prefix_num, a.company_id, a.location_id, a.within_group, a.party_id, a.party_location, a.currency_id, a.receive_date, a.delivery_date, a.rec_start_date, a.rec_end_date, a.order_id, a.order_no, a.booking_without_order, a.booking_type, a.remarks, a.check_box_id, a.exchange_rate, a.tag_pi_no, a.order_type,a.yd_type,a.yd_process, a.attention, a.team_leader, a.team_member, a.party_ref,a.pro_type,a.check_box_confirm,a.check_box_advance, a.advance_job";
    //echo $sql;
    $nameArray=sql_select( $sql );

    //$data_array="(".$id.", 374, '".$new_job_no[0]."', '".$new_job_no[1]."', '".$new_job_no[2]."', '".$cbo_company_name."', '".$cbo_location_name."', '".$cbo_within_group."', '".$cbo_party_name."', '".$cbo_party_location."', '".$cbo_currency."', '".$txt_order_receive_date."', '".$txt_delivery_date."','".$txt_rec_start_date."','".$txt_rec_end_date."', '".$hid_order_id."', '".$txt_order_no."', '".$is_without_order."','".$hid_booking_type."', '".$txt_remarks."', ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."')";

    $company_id = $nameArray[0][csf("company_id")];
    $advance_job = $nameArray[0][csf("advance_job")];
    $tag_pi_no = $nameArray[0][csf("tag_pi_no")];

    $sql1=" select a.id, a.job_no_prefix_num, a.yd_job, a.order_no, a.pro_type, a.order_type, a.yd_type, a.yd_process, c.pi_number, sum(b.order_quantity) as  order_quantity from yd_ord_mst a, yd_ord_dtls b, com_export_pi_mst c where a.id=b.mst_id and a.company_id=$company_id and a.yd_job='$advance_job' and c.pi_number='$tag_pi_no' and a.status_active=1 and a.is_deleted=0 and a.entry_form=374 and a.order_type=2 and a.check_box_advance=1 and a.yd_job=c.YD_JOB group by a.id, a.job_no_prefix_num, a.yd_job, a.order_no, a.pro_type, a.order_type, a.yd_type, a.yd_process, c.pi_number order by id desc ";//$job_no_cond


    $sql_advance_order_res=sql_select($sql1);

    $pre_qty = 0;
    $advance_qty = 0;

    foreach($sql_advance_order_res as $data)
    {
        $advance_qty += $data[csf('order_quantity')];
    }

    $sql2="select a.advance_job, a.tag_pi_no, sum(b.order_quantity) as order_quantity from yd_ord_mst a, yd_ord_dtls b where a.id=b.mst_id and a.company_id=$company_id and a.advance_job='$advance_job' and a.tag_pi_no='$tag_pi_no' and a.status_active=1 and a.is_deleted=0 and a.entry_form=374 and a.check_box_confirm=1 and a.tag_pi_no is not null and a.advance_job is not null group by a.advance_job, a.tag_pi_no ";//$job_no_cond

    $sql_pre_order_res=sql_select($sql2);

    foreach($sql_pre_order_res as $data)
    {
        $pre_qty += $data[csf('order_quantity')];
    }

    foreach ($nameArray as $row)
    {   
        //document.getElementById('txt_check_box').checked
        $check_box_confirm=$row[csf("check_box_confirm")];
		$check_box_advance=$row[csf("check_box_advance")];
		
		if($check_box_confirm==1)
        {
            echo "$('#txt_check_box_confirm').attr('checked',true);\n";
            echo "fnc_wo_check_confirm(2);\n";
           // echo "$('#txt_check_box_confirm').attr('disabled','true')".";\n";
             
        }
		if($check_box_advance==1)
        {
            echo "$('#txt_check_box_advance').attr('checked',true);\n";
            echo "fnc_wo_check_advance(3);\n";
            //echo "$('#txt_check_box_advance').attr('disabled','true')".";\n";
        }
		
		
		
		$check_box_id=$row[csf("check_box_id")];
        if($check_box_id==1)
        {
            echo "$('#txt_check_box').attr('checked',true);\n";
            //echo "document.getElementById('txt_check_box').value=1;\n";
            echo "fnc_wo_check(1);\n";
            echo "$('#txt_check_box').attr('disabled','true')".";\n";
            echo "$('#cbo_within_group').attr('disabled','true')".";\n";
            echo "$('#txt_delivery_date').attr('disabled',false)".";\n";
        }
        else
        {
            //echo "$('#txt_delivery_date').attr('disabled',true)".";\n";   
        }
        echo "document.getElementById('update_id').value            = '".$row[csf("id")]."';\n";
        echo "document.getElementById('txt_job_no').value           = '".$row[csf("yd_job")]."';\n";
        echo "document.getElementById('hid_order_id').value         = '".$row[csf("order_id")]."';\n";
        echo "document.getElementById('txt_order_no').value         = '".$row[csf("order_no")]."';\n";

        echo "document.getElementById('cbo_company_name').value     = '".$row[csf("company_id")]."';\n";  
        echo "fnc_load_party(1,".$row[csf("within_group")].");\n";  
        echo "document.getElementById('cbo_location_name').value    = '".$row[csf("location_id")]."';\n";
        echo "document.getElementById('cbo_party_name').value       = '".$row[csf("party_id")]."';\n";
        echo "fnc_load_party(2,".$row[csf("within_group")].");\n";   
          
        echo "document.getElementById('cbo_within_group').value         = '".$row[csf("within_group")]."';\n";
        echo "fnc_load_wo(".$row[csf("within_group")].");\n"; 
        echo "document.getElementById('cbo_party_location').value   = '".$row[csf("party_location")]."';\n";

        echo "document.getElementById('txt_order_receive_date').value   = '".change_date_format($row[csf("receive_date")])."';\n"; 
        echo "document.getElementById('txt_delivery_date').value        = '".change_date_format($row[csf("delivery_date")])."';\n"; 
        echo "document.getElementById('txt_rec_start_date').value       = '".change_date_format($row[csf("rec_start_date")])."';\n"; 
        echo "document.getElementById('txt_rec_end_date').value         = '".change_date_format($row[csf("rec_end_date")])."';\n";

        echo "load_drop_down( 'requires/yd_order_entry_controller',".$row[csf("team_leader")]."+'_'+".$row[csf("company_id")].", 'load_drop_down_member', 'team_member_td');\n"; 

        
        echo "document.getElementById('txt_advance_job').value         = '".$row[csf("advance_job")]."';\n";
        echo "document.getElementById('cbo_currency').value         = '".$row[csf("currency_id")]."';\n";
        echo "document.getElementById('txt_exchange_rate').value         = '".$row[csf("exchange_rate")]."';\n";
        echo "document.getElementById('txt_tag_pi_no').value         = '".$row[csf("tag_pi_no")]."';\n";
        echo "document.getElementById('cbo_order_type').value         = '".$row[csf("order_type")]."';\n";
        echo "document.getElementById('cbo_yd_type').value         = '".$row[csf("yd_type")]."';\n";
        echo "document.getElementById('cbo_yd_process').value         = '".$row[csf("yd_process")]."';\n";
        echo "document.getElementById('attention').value         = '".$row[csf("attention")]."';\n";
        echo "document.getElementById('cbo_team_leader').value         = '".$row[csf("team_leader")]."';\n";
        echo "document.getElementById('cbo_team_member').value         = '".$row[csf("team_member")]."';\n";
        echo "document.getElementById('party_ref').value         = '".$row[csf("party_ref")]."';\n";
        echo "document.getElementById('txt_remarks').value         = '".$row[csf("remarks")]."';\n";
        echo "document.getElementById('hid_is_without_order').value = '".$row[csf("booking_without_order")]."';\n";
        echo "document.getElementById('hid_booking_type').value     = '".$row[csf("booking_type")]."';\n";
        echo "document.getElementById('cbo_pro_type').value     = '".$row[csf("pro_type")]."';\n";
        echo "$('#cbo_company_name').attr('disabled','true')".";\n";
        echo "$('#within_group').attr('disabled','true')".";\n";
       // echo "$('#txt_order_no').attr('disabled','true')".";\n";
        echo "$('#cbo_party_name').attr('disabled','true')".";\n";
        echo "$('#cbo_party_location').attr('disabled','true')".";\n";
        echo "$('#txt_order_receive_date').attr('disabled','true')".";\n";

        if($row[csf("advance_job")]!='')
        {
            echo "$('#txt_tag_pi_no').attr('disabled','true')".";\n";
            echo "$('#txt_advance_job').attr('disabled','true')".";\n";
        }

        $balance =$advance_qty-$pre_qty+$row[csf("order_quantity")];

        echo "document.getElementById('txt_tag_pi_balance_qty').value     = '".$balance."';\n";
    }
    exit(); 
}

if( $action=='order_dtls_list_view' ) 
{
    //echo $data; die;2_FAL-TOR-19-00092_1_3895
    $data=explode('_',$data);
    $operationMood=$data[0];
    $id=$data[1];
    $job_no=$data[2];
    $within_group=$data[3];
    $order_no=$data[4];

      $sql_check = "select id, check_box_id from yd_ord_mst where id='$id'";
     $sql_check_array=sql_select($sql_check);
     $check_box_id=$sql_check_array[0][csf('check_box_id')];
    //echo $check_box_id.'d';
    $color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
    //echo "<pre>";print_r($color_arr); die;
    $disable_data = '';
    $disable_data1 = 0;
    if($operationMood==1)
    {
       $sql = "select a.id, a.mst_id, a.job_no, a.product_id, a.job_no_id, a.count, a.yarn_description, a.yarn_color, a.color_range, a.uom, a.yarn_wo_qty, a.dyeing_charge, a.amount, a.no_of_bag, a.no_of_cone, a.min_require_cone, a.remarks, a.referance_no, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_type,b.available_qnty,b.lot,c.style_ref_no  from wo_yarn_dyeing_dtls a,product_details_master b,wo_po_details_master c  where a.product_id=b.id  and  a.job_no_id=c.id  and a.mst_id='$id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id";
    }
    else
    {
        $sql = "SELECT id, mst_id, job_no_mst, order_id, order_no, style_ref, sales_order_no, sales_order_id, product_id, process_id, lot, count_id, yarn_type_id, yarn_composition_id, item_color_id, yd_color_id, csp, no_bag, cone_per_bag, no_cone, avg_wgt, uom, order_quantity,  rate, amount, process_loss, total_order_quantity , buyer_buyer, count_type, adj_type, process_name,use_for,app_ref, shade,shade_dtls_id, shade_mst_id from yd_ord_dtls where mst_id='$id' and status_active=1 and is_deleted=0 order by id";

        $batch_sql = sql_select("select a.yd_job_id, b.yd_job from yd_batch_mst a, yd_ord_mst b where a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.yd_job_id=b.id and b.id=$id");

        if(count($batch_sql)>0)
        {
            $disable_data = "disabled";
            $disable_data1 = 1;
        }
    }
    //echo $sql;
    $data_array=sql_select($sql); $del_date_arr=array();
    //print_r($data_array);
    $ind=0;
    
    $readonlySts='readonly';
    $disableSts='disabled';
    if(count($data_array) > 0)
    {
        foreach($data_array as $row)
        {
            if($operationMood==1)
            {
                $styleRef=$row[csf("style_ref_no")];
                $saleOrderNo=$row[csf("job_no")];
                $saleOrderID=$row[csf("job_no_id")];
                $prodID=$row[csf("product_id")];
                $process='';
                $processName = '';
                $lot=$row[csf("lot")];
                $count=$row[csf("count")];
                $yarnType=$row[csf("yarn_type")];
                $yarn_composition=$row[csf("yarn_comp_type1st")];
                $itemColor=$row[csf("color_range")];
                $yarn_color=$row[csf("yarn_color")];
                $csp='';
                $no_of_bag=$row[csf("no_of_bag")];
                $no_of_cone=$row[csf("no_of_cone")];
                $coneBag='';
                $avg='';
                $useFor='';
                $appRef='';
                $uomId=$row[csf("uom")];
                $orderQty=$row[csf("yarn_wo_qty")];
                $amount=$row[csf("amount")];
                $rate=$amount/$orderQty;
                $processLoss=0;
                $totalQty=$orderQty;
                $hdnDtlsUpdateId='';
                $shade='';
                $shadeId = '';
                $shadeMstId = '';
            }
            else
            {
                //$styleRef=$row[csf("style_ref")];
                $buyerBuyer=$row[csf("buyer_buyer")];
                $countType=$row[csf("count_type")];
                $adjType=$row[csf("adj_type")];
                $styleRef=$row[csf("style_ref")];
                $saleOrderNo=$row[csf("sales_order_no")];
                $saleOrderID=$row[csf("sales_order_id")];
                $prodID=$row[csf("product_id")];
                $process=$row[csf("process_id")];
                $processName=$row[csf("process_name")];
                $lot=$row[csf("lot")];
                $count=$row[csf("count_id")];
                $yarnType=$row[csf("yarn_type_id")];
                $yarn_composition=$row[csf("yarn_composition_id")];
                $itemColor=$row[csf("item_color_id")];
                $yarn_color=$row[csf("yd_color_id")];
                $useFor=$row[csf("use_for")];
                $appRef=$row[csf("app_ref")];
                $csp=$row[csf("csp")];
                $no_of_bag=$row[csf("no_bag")];
                $no_of_cone=$row[csf("no_cone")];
                $coneBag=$row[csf("cone_per_bag")];
                $avg=$row[csf("avg_wgt")];
                $uomId=$row[csf("uom")];
                $orderQty=$row[csf("order_quantity")];
                $amount=$row[csf("amount")];
                $rate=$amount/$orderQty;
                $processLoss=$row[csf("process_loss")];
                $totalQty=$row[csf("total_order_quantity")];
                $hdnDtlsUpdateId=$row[csf("id")];
                $shade=$row[csf("shade")];
                $shadeId = $row[csf("shade_dtls_id")];
                $shadeMstId = $row[csf("shade_mst_id")];
            }
            $tblRow++;
            //$dtls_id=0; $order_uom=0; $wo_qnty=0; $disabled_conv=''; 
            //$yarn_descirption = $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")] . "%";  
            //echo $operationMood.'='.$styleRef;
            if($check_box_id==1)
            {
                $disableSts="";
            }
			
			if($within_group==1)
			{
				$disabled="disabled";
				if($countType!=""){
				$countType=$countType;
				}else{$countType=1;}
			}
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
                <td><input id="txtstyleRef_<? echo $tblRow; ?>" name="txtstyleRef[]" type="text"  class="text_boxes" style="width:80px" 
                value="<? echo $styleRef; ?>" <? echo $disableSts; ?> /></td>

                <td><input id="txtsaleOrder_<? echo $tblRow; ?>" name="txtsaleOrder[]" type="text" value="<? echo $saleOrderNo; ?>" class="text_boxes" style="width:80px" placeholder=""/>
                <input id="txtsaleOrderID_<? echo $tblRow; ?>" name="txtsaleOrderID[]" type="hidden" value="<? echo $saleOrderID; ?>" class="text_boxes_numeric" style="width:80px" placeholder=""/>
                <input id="txtProductID_<? echo $tblRow; ?>" name="txtProductID[]" type="hidden" value="<? echo $prodID; ?>" class="text_boxes_numeric" style="width:80px" placeholder=""/></td>
                <td><input id="buyerBuyer_<? echo $tblRow; ?>" name="txtbuyerBuyer[]" type="text" class="text_boxes" style="width:80px" value="<? echo $buyerBuyer; ?>" placeholder=""/>
                </td>

                <td>
                <input type="text" name="txtprocessname[]" id="txtprocessname_<? echo $tblRow; ?>" class="text_boxes" value="<?php echo $processName;?>" style="width:80px;" onDblClick="openmypage_process(this.id)" placeholder="Doble Click For Process"  readonly />
               <input type="hidden" name="txtprocess[]" id="txtprocess_<? echo $tblRow; ?>" class="text_boxes" style="width:80px;" value="<?php echo $process;?>" readonly />
                </td>
                <td><input id="txtlot_<? echo $tblRow; ?>" name="txtlot[]" type="text" class="text_boxes" style="width:80px" value="<? echo $lot; ?>" /></td>
                <td>
                    <?
                   // $count_type_arr = array(1 => "Single",2 => "Double");
                    echo create_drop_down( "txtcountType_".$tblRow, 100, $count_type_arr,'', 1, '--- Select---', $countType,"",0,'','','','','','',"txtcountType[]");
                    ?>
                </td>
                <td id="count_td">
                   <?
                       if ($within_group==2) 
                       {
                           $sql="select distinct(b.id) as id,b.yarn_count from lib_yarn_count b where b.status_active=1 and b.is_deleted=0";
                       }
                       else
                       {
                           // $sql="select distinct(b.id) as id,b.yarn_count from fabric_sales_order_yarn_dtls a, lib_yarn_count b where  a.yarn_count_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
							
							$sql="select distinct(b.id) as id,b.yarn_count from lib_yarn_count b where b.status_active=1 and b.is_deleted=0";
                       }

                    echo create_drop_down( "cboCount_".$tblRow, 80, $sql,"id,yarn_count", 1, "-- Select --",$count,"",$disable_data1,'','','','','','',"cboCount[]"); ?>
                </td>
                
                <td id="yarn_type_td">
                   <? echo create_drop_down( "cboYarnType_".$tblRow, 80, $yarn_type,"", 1, "-- Select --",$yarnType,"",$disable_data1,'','','','','','',"cboYarnType[]"); ?>
                </td>
                <td align="center" id="composition_td"><? echo create_drop_down( "cboComposition_".$tblRow, 80, $composition,"", 1, "-- Select --",$yarn_composition,"",$disable_data1,'','','','','','',"cboComposition[]"); ?></td> 
                <td>
                
                <? 
                    echo create_drop_down( "txtItemColor_".$tblRow, 90, $color_range,"", 1, "-- Select --",$itemColor,"check_shade_rate(this.id);",$disable_data1,'','','','','','',"txtItemColor[]"); ?>
                    
                    <input id="txtItemColorID_<? echo $tblRow; ?>" type="hidden"  name="txtItemColorID[]" class="text_boxes" style="width:70px" value="<? echo $itemColor; ?>"/>
                </td>
                <td id="color_td">
                    <input id="txtYarnColor_<? echo $tblRow; ?>" type="text"  name="txtYarnColor[]" class="text_boxes" style="width:70px" value="<? echo $color_arr[$yarn_color]; ?>"/>
                    <input id="txtYarnColorID_<? echo $tblRow; ?>" type="hidden"  name="txtYarnColorID[]" class="text_boxes" style="width:70px" <?php echo $disable_data; ?> value="<? echo $yarn_color; ?>"/>
                </td>
                <td>
                    <input id="txtShade_<? echo $tblRow; ?>" type="text"  name="txtShade[]" class="text_boxes_numeric" placeholder="write Or Browse" onChange="set_shade_rate(this.id);" onDblClick="fnc_shade_popup(this.id);" style="width:70px" <?php echo $disable_data; ?> value="<? echo $shade; ?>"/>
                    <input name="txtShadeId[]" id="txtShadeId_<? echo $tblRow; ?>" type="hidden" class="text_boxes_numeric" style="width:50px" placeholder="write Or Browse" value="<? echo $shadeId; ?>"/>
                    <input name="txtShadeMstId[]" id="txtShadeMstId_<? echo $tblRow; ?>" type="hidden" class="text_boxes_numeric" style="width:50px" placeholder="write Or Browse" value="<? echo $shadeMstId; ?>"/>
                </td>
                <td><input id="txtAppRef_<? echo $tblRow; ?>" name="txtAppRef[]" type="text" class="text_boxes" style="width:70px" value="<? echo $appRef; ?>" placeholder=""/></td>
                <td><input onFocus="add_auto_complete(this.id)" onBlur="fn_filed_check(this.id);use_for_copy_value(this.value,'txtUseFor_',this.id)" id="txtUseFor_<? echo $tblRow; ?>" name="txtUseFor[]" type="text" class="text_boxes" style="width:70px" value="<? echo $useFor; ?>" placeholder=""/></td>
                <td><input id="txtCSP_<? echo $tblRow; ?>" name="txtCSP[]" type="text" class="text_boxes_numeric" style="width:70px" value="<? echo $csp; ?>" /></td>
                <td><input name="txtnoBag[]" id="txtnoBag_<? echo $tblRow; ?>" type="text" class="text_boxes_numeric" style="width:70px" value="<? echo $no_of_bag; ?>"/></td>
                <td><input name="txtConeBag[]" id="txtConeBag_<? echo $tblRow; ?>" type="text" class="text_boxes_numeric" style="width:50px" value="<? echo $coneBag; ?>" /></td>
                <td><input name="txtNoCone[]" id="txtNoCone_<? echo $tblRow; ?>" type="text" class="text_boxes_numeric" style="width:50px" value="<? echo $no_of_cone; ?>"  /></td>
                <td><input name="txtAVG[]" id="txtAVG_<? echo $tblRow; ?>" type="text"   class="text_boxes_numeric" style="width:50px" value="<? echo $avg; ?>" /></td>
                <td><? echo create_drop_down( "cboUom_".$tblRow, 60, $unit_of_measurement,"", 1, "-- Select --",$uomId,"", 1,'','','','','','',"cboUom[]"); ?></td>
                <td><input name="txtOrderqty[]" onKeyUp="fnc_amount_cal(<?php echo $tblRow;?>)" id="txtOrderqty_<? echo $tblRow; ?>" type="text" style="width:50px"  class="text_boxes_numeric" value="<? echo $orderQty; ?>" onKeyUp="sum_total_qnty(<? echo $tblRow;?>);" <? echo $disabled; ?> /></td> 
                <td><input name="txtRate[]" id="txtRate_<? echo $tblRow; ?>" type="text" style="width:50px"  class="text_boxes_numeric"  value="<? echo number_format($rate,4,".",""); ?>" onKeyUp="fnc_amount_cal(<?php echo $tblRow;?>)" <? echo $disabled; ?> /></td> 
                <td><input name="txtAmount[]" id="txtAmount_<? echo $tblRow; ?>" type="text" style="width:50px"  class="text_boxes_numeric"  value="<? echo $amount; ?>" readonly /></td> 
                <td><input name="txtProcessLoss[]" onKeyUp="fnc_amount_cal(<? echo $tblRow; ?>)" id="txtProcessLoss_<? echo $tblRow; ?>" type="text"  class="text_boxes_numeric" style="width:50px" placeholder="%" value="<? echo $processLoss; ?>" onKeyUp="fnc_amount_cal(<? echo $tblRow;?>);"  /></td>

                <td>
                    <?
                   // $adj_type_arr = array(1 => "Increase",2 => "Decrease");
                    echo create_drop_down( "txtadjType_".$tblRow, 80, $adj_type_arr,'', 1, '--- Select---', $adjType, "fnc_amount_cal($tblRow);",0,'','','','','','',"txtadjType[]");
                    ?>        
                </td>
                <td><input readonly type="text" name="txtTotalqty[]" id="txtTotalqty_<? echo $tblRow; ?>" class="text_boxes_numeric" style="width:50px" value="<? echo $totalQty; ?>" readonly />
                    <input type="hidden" name="hdnDtlsUpdateId[]" id="hdnDtlsUpdateId_<? echo $tblRow; ?>" class="text_boxes_numeric" style="width:50px" value="<? echo $hdnDtlsUpdateId; ?>" readonly />
                </td>
                <td width="65">
                    <input type="button" id="increase_<? echo $tblRow; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fnc_addRow(
                    <? echo $tblRow.","."'tbl_dtls_yarn_dyeing'".","."'row_'" ;?>)" />
                    <input type="button" id="decrease_<? echo $tblRow; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fnc_deleteRow(<? echo $tblRow.","."'tbl_dtls_yarn_dyeing'".","."'row_'" ; ?>);" />
                </td>
            </tr> 
            <?
        }
    }
    else
    {
        ?>
        <tr id="row_1">
            <td><input id="txtstyleRef_1" name="txtstyleRef[]" type="text" class="text_boxes" style="width:80px"/></td>
            <td><input id="txtsaleOrder_1" name="txtsaleOrder[]" type="text" class="text_boxes" style="width:80px" placeholder=""/>
            <input id="txtsaleOrderID_1" name="txtsaleOrderID[]" type="hidden" class="text_boxes_numeric" style="width:80px" placeholder=""/>
            <input id="txtProductID_1" name="txtProductID[]" type="hidden" class="text_boxes_numeric" style="width:80px" placeholder=""/></td>
            <td><input id="buyerBuyer_1" name="txtbuyerBuyer[]" type="text" class="text_boxes" style="width:80px" placeholder=""/>
                </td>

                <td>
                <input type="text" name="txtprocessname[]" id="txtprocessname_1" class="text_boxes" value="" style="width:105px;" onDblClick="openmypage_process(this.id)" placeholder="Doble Click For Process"  readonly />  
               <input type="hidden" name="txtprocess[]" id="txtprocess_1" class="text_boxes" value="" style="width:105px;"  readonly />
                </td> 
            <td><input id="txtlot_1" name="txtlot[]" type="text" class="text_boxes" style="width:80px" placeholder=""/></td>
            <td>
                <?
                $count_type_arr = array(1 => "Single",2 => "Double");
                echo create_drop_down( "txtcountType_1", 100, $count_type_arr,'', 1, '--- Select---', 0, "",0,'','','','','','',"txtcountType[]");
                ?>
            </td>
            <td id="count_td"><input id="txtcount_1" name="txtcount[]" type="text" class="text_boxes_numeric" style="width:80px" placeholder=""/></td>
            <td id="yarn_type_td"><input id="txtydtype_1" name="txtydtype[]" type="text" class="text_boxes" style="width:60px" placeholder=""/></td>
            <td id="composition_td"><input id="txtydComposition_1" name="txtydComposition[]" type="text" class="text_boxes" style="width:60px" placeholder=""/></td>
            <td>  <? echo   create_drop_down( "txtItemColor_1", 90, $color_range,"", 1, "-- Select --",0,"check_shade_rate(this.id);",0,'','','','','','',"txtItemColor[]")   ?>
                <input name="txtItemColorID[]" id="txtItemColorID_1" type="hidden" class="text_boxes" style="width:50px" />
            </td>
            <td><input name="txtYarnColor[]" id="txtYarnColor_1" type="text" class="text_boxes" style="width:50px" placeholder="" readonly /><input name="txtYarnColorID[]" id="txtYarnColorID_1" type="hidden" class="text_boxes" style="width:50px" /></td>
            <td>
                <input name="txtShade[]" id="txtShade_1" type="text" class="text_boxes_numeric" style="width:50px" placeholder="write Or Browse" onChange="set_shade_rate(this.id);" onDblClick="fnc_shade_popup(this.id);" />
                <input name="txtShadeId[]" id="txtShadeId_1" type="hidden" class="text_boxes_numeric" style="width:50px" placeholder="write Or Browse"/>
            </td>
            <td><input id="txtAppRef_1" name="txtAppRef[]" type="text" class="text_boxes" style="width:70px" placeholder=""/></td>
            <td><input id="txtUseFor_1" name="txtUseFor[]" type="text" class="text_boxes" style="width:70px" placeholder=""/></td>
            <td><input id="txtCSP_1" name="txtCSP[]" type="text" class="text_boxes_numeric" style="width:70px" placeholder=""/></td>
            <td><input name="txtnoBag[]" id="txtnoBag_1" type="text" class="text_boxes_numeric" style="width:70px"  placeholder="" /></td>
            <td><input name="txtConeBag[]" id="txtConeBag_1" type="text" class="text_boxes_numeric" style="width:50px" placeholder="Write" /></td>
            <td><input name="txtNoCone[]" id="txtNoCone_1" class="text_boxes_numeric" type="text"  style="width:50px"  placeholder="Write" /></td>
            <td><input name="txtAVG[]" id="txtAVG_1" type="text"  class="text_boxes_numeric" style="width:50px" placeholder="Write" /></td>
            <td><? echo create_drop_down( "cboUom_1", 60, $unit_of_measurement,"", 1, "-- Select --",12,"", "","2,1,12,23",'','','','','',"cboUom[]" ); ?></td>
            <td><input name="txtOrderqty[]" onKeyUp="fnc_amount_cal(1)" id="txtOrderqty_1" type="text" style="width:50px"  class="text_boxes_numeric"  placeholder="" /></td> 
            <td><input name="txtRate[]" id="txtRate_1" type="text" style="width:50px"  class="text_boxes_numeric" onKeyUp="fnc_amount_cal(1)"  placeholder="" /></td> 
            <td><input name="txtAmount[]" id="txtAmount_1" type="text" style="width:50px"  class="text_boxes_numeric"  placeholder="" /></td> 
            <td><input name="txtProcessLoss[]" id="txtProcessLoss_1" type="text"  class="text_boxes_numeric" style="width:50px" placeholder="" onKeyUp="fnc_amount_cal(1);" /></td>
            <td>
                    <?
                    $adj_type_arr = array(1 => "Increase",2 => "Decrease");
                    echo create_drop_down( "txtadjType_1", 80, $adj_type_arr,'', 1, '--- Select---', 2, "fnc_amount_cal($tblRow);",0,'','','','','','',"txtadjType[]");
                    ?>        
                </td>
            <td><input readonly type="text" name="txtTotalqty[]" id="txtTotalqty_1" class="text_boxes_numeric" style="width:50px"  placeholder="" /><input type="hidden" name="hdnDtlsUpdateId[]" id="hdnDtlsUpdateId_1" class="text_boxes_numeric" style="width:50px"  readonly /></td>

            <!-- onClick="openmypage_avg_wt(1,'0',1)" placeholder="Browse" <td><input type="button" name="btnremarks_1" id="btnremarks_1" class="formbuttonplasminus" value="RMK" onClick="openmypage_remarks(1);" />
                <input type="hidden" name="txtremarks_1" id="txtremarks_1" class="text_boxes" />
            </td> -->
            <td width="65">
            <input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fnc_addRow(1,'tbl_dtls_yarn_dyeing','row_')" />
            <input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fnc_deleteRow(1,'tbl_dtls_yarn_dyeing','row_');" />
            </td>
        </tr>
        <?
    }

    exit();
}

if ($action=="save_update_delete")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process )); 
    /*echo '<pre>';
    print_r($cbo_company_name);die;*/
    $user_id=$_SESSION['logic_erp']['user_id'];
    
    if ($operation==0) // Insert Start Here
    {
        $con = connect();
        if($db_type==0)
        {
            mysql_query("BEGIN");
        }
        
        $receive_date=strtotime(str_replace("'",'',$txt_order_receive_date));
        $delivery_date=strtotime(str_replace("'",'',$txt_delivery_date));
        /* $current_date=strtotime(date("d-m-Y"));
        if($receive_date>$delivery_date)
        {
            echo "26**"; die;
        }
        else if($receive_date != $current_date)
        {
            echo "25**"; die;
        }*/

        
        if($db_type==0) $insert_date_con="and YEAR(insert_date)=".date('Y',time())."";
        else if($db_type==2) $insert_date_con="and TO_CHAR(insert_date,'YYYY')=".date('Y',time())."";
        
        $new_job_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'YDOE', date("Y",time()), 5, "select job_no_prefix,job_no_prefix_num from yd_ord_mst where entry_form=374 and company_id=$cbo_company_name $insert_date_con and status_active=1 and is_deleted=0 order by id desc ", "job_no_prefix", "job_no_prefix_num" ));
        /*if(str_replace("'",'',$txt_wo_no)==""){
            $txt_wo_no=$new_job_no[0];
        }else{
            $txt_wo_no=str_replace("'",'',$txt_wo_no);
        }*/

        if (is_duplicate_field( "order_no", "yd_ord_mst", "order_no='$txt_wo_no' and company_id=$cbo_company_name and status_active=1 and is_deleted=0" ) == 1)
        {
            echo "11**0"; die;
        }
        else
        {
            //echo "10**select order_no from subcon_ord_mst where order_no='$txt_order_no' and company_id=$cbo_company_name and status_active=1 and is_deleted=0 and id !=$update_id"; die;
            if($db_type==0){
                $txt_delivery_date=change_date_format(str_replace("'",'',$txt_delivery_date),'yyyy-mm-dd');
                $txt_rec_start_date=change_date_format(str_replace("'",'',$txt_rec_start_date),'yyyy-mm-dd');
                $txt_rec_end_date=change_date_format(str_replace("'",'',$txt_rec_end_date),'yyyy-mm-dd');
                $txt_order_receive_date=change_date_format(str_replace("'",'',$txt_order_receive_date),'yyyy-mm-dd');
            }else{
                $txt_delivery_date=change_date_format(str_replace("'",'',$txt_delivery_date), "", "",1);
                $txt_rec_start_date=change_date_format(str_replace("'",'',$txt_rec_start_date), "", "",1);
                $txt_rec_end_date=change_date_format(str_replace("'",'',$txt_rec_end_date), "", "",1);
                $txt_order_receive_date=change_date_format(str_replace("'",'',$txt_order_receive_date), "", "",1);
            }
            $id=return_next_id("id","yd_ord_mst",1);
            $id1=return_next_id( "id", "yd_ord_dtls",1);
            // $id3=return_next_id( "id", "subcon_ord_breakdown", 1 );
            $rID3=true;
            $field_array="id, entry_form, yd_job, job_no_prefix, job_no_prefix_num, company_id, location_id, within_group, party_id, party_location, currency_id, receive_date, delivery_date, rec_start_date, rec_end_date, order_id, order_no, booking_without_order, booking_type, remarks,check_box_id,exchange_rate,tag_pi_no,order_type,yd_type,yd_process,attention,team_leader,team_member,party_ref,pro_type,check_box_confirm, check_box_advance, advance_job,inserted_by, insert_date";


            $data_array="(".$id.", 374, '".$new_job_no[0]."', '".$new_job_no[1]."', '".$new_job_no[2]."', '".$cbo_company_name."', '".$cbo_location_name."', '".$cbo_within_group."', '".$cbo_party_name."', '".$cbo_party_location."', '".$cbo_currency."', '".$txt_order_receive_date."', '".$txt_delivery_date."','".$txt_rec_start_date."','".$txt_rec_end_date."', '".$hid_order_id."', '".$txt_order_no."', '".$is_without_order."','".$hid_booking_type."', '".$txt_remarks."', '".$txt_check_box."', '".$txt_exchange_rate."', '".$txt_tag_pi_no."', '".$cbo_order_type."', '".$cbo_yd_type."', '".$cbo_yd_process."', '".$attention."', '".$cbo_team_leader."', '".$cbo_team_member."', '".$party_ref."', '".$cbo_pro_type."','".$txt_check_box_confirm."','".$txt_check_box_advance."','".$txt_advance_job."', ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."')";

            $txt_job_no=$new_job_no[0];
            $field_array2="id, mst_id, job_no_mst, order_id, order_no, style_ref, sales_order_no, sales_order_id, product_id, process_id, process_name,lot, count_id, yarn_type_id, yarn_composition_id, item_color_id, yd_color_id, csp, no_bag, cone_per_bag, no_cone, avg_wgt, uom, order_quantity,  rate, amount, process_loss, total_order_quantity,count_type,buyer_buyer,adj_type,use_for,app_ref,shade,shade_dtls_id,shade_mst_id, inserted_by, insert_date";
            //$field_array3="id, mst_id, order_id, job_no_mst, book_con_dtls_id, description, color_id, size_id, qnty, rate, amount, booked_qty";

            // $size_library_arr=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
            //$add_commadtls=0; $data_array3="";
            $color_library_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
            $data_array2=""; $add_commaa=0;
            for($i=1; $i<=$total_row; $i++)
            {  
                $txtstyleRef            = "txtstyleRef_".$i;
                $txtsaleOrder           = "txtsaleOrder_".$i;
                $txtsaleOrderID         = "txtsaleOrderID_".$i;
                $txtProductID           = "txtProductID_".$i;
                $txtprocess             = "txtprocess_".$i;
                $txtlot                 = "txtlot_".$i;
                $cboCount               = "cboCount_".$i;
                $cboYarnType            = "cboYarnType_".$i;
                $cboComposition         = "cboComposition_".$i;

                $txtItemColor           = "txtItemColor_".$i;
                $txtYarnColor           = "txtYarnColor_".$i;
                $txtItemColorID         = "txtItemColorID_".$i;
                $txtYarnColorID         = "txtYarnColorID_".$i;
                $txtprocessname         = "txtprocessname_".$i;

                $txtCSP                 = "txtCSP_".$i;
                $txtnoBag               = "txtnoBag_".$i;          
                $txtConeBag             = "txtConeBag_".$i;
                $txtNoCone              = "txtNoCone_".$i;
                $txtAVG                 = "txtAVG_".$i;
                $cboUom                 = "cboUom_".$i;
                $txtOrderqty            = "txtOrderqty_".$i;
                $txtRate                = "txtRate_".$i;
                $txtAmount              = "txtAmount_".$i;
                $txtProcessLoss         = "txtProcessLoss_".$i;
                $txtTotalqty            = "txtTotalqty_".$i;
                $txtbuyerbuyer          = "txtbuyerbuyer_".$i;
                $txtcounttype           = "txtcounttype_".$i;
                $txtadjtype             = "txtadjtype_".$i;
                $useFor                 = "useFor_".$i;
                $appRef                 = "appRef_".$i;
                $txtShade               = "txtShade_".$i;
                $txtShadeId             = "txtShadeId_".$i;
                $txtShadeMstId          = "txtShadeMstId_".$i;
                /*$hdnDtlsUpdateId      = "hdnDtlsUpdateId_".$i;
                $hdnbookingDtlsId       = "hdnbookingDtlsId_".$i;
                $txtIsWithOrder         = "txtIsWithOrder_".$i;
                
                $orddelivery_date=strtotime(str_replace("'",'',$$txtOrderDeliveryDate));
                if($receive_date>$orddelivery_date){
                    echo "26**"; die;
                }
                if($db_type==0){
                    $orderDeliveryDate=change_date_format(str_replace("'",'',$$txtOrderDeliveryDate),'yyyy-mm-dd');
                } else {
                    $orderDeliveryDate=change_date_format(str_replace("'",'',$$txtOrderDeliveryDate), "", "",1);
                }if(str_replace("'",'',$$txtbuyerPoId)=="") $txtbuyerPoId=0; else $txtbuyerPoId=str_replace("'",'',$$txtbuyerPoId);*/

               /* if (str_replace("'", "", trim($$txtItemColor)) != "") {
                    if (!in_array(str_replace("'", "", trim($$txtItemColor)),$new_array_color)){
                        $color_id = return_id( str_replace("'", "", trim($$txtItemColor)), $color_arr, "lib_color", "id,color_name","374");
                        $new_array_color[$color_id]=str_replace("'", "", trim($$txtItemColor));
                    }
                    else $color_id =  array_search(str_replace("'", "", trim($$txtItemColor)), $new_array_color);
                } else $color_id = 0;*/

                if (str_replace("'", "", trim($$txtYarnColor)) != "") {
                    if (!in_array(str_replace("'", "", trim($$txtYarnColor)),$new_array_color)){
                        $yd_color_id = return_id( str_replace("'", "", trim($$txtYarnColor)), $color_arr, "lib_color", "id,color_name","374");
                        $new_array_color[$yd_color_id]=str_replace("'", "", trim($$txtYarnColor));
                    }
                    else $yd_color_id =  array_search(str_replace("'", "", trim($$txtYarnColor)), $new_array_color);
                } else $yd_color_id = 0;

                if ($add_commaa!=0) $data_array2 .=","; $add_comma=0;
                
                $data_array2 .="(".$id1.",".$id.",'".$new_job_no[0]."','".$hid_order_id."','".$txt_order_no."',".$$txtstyleRef.",".$$txtsaleOrder.",".$$txtsaleOrderID.",".$$txtProductID.",".$$txtprocess.",".$$txtprocessname.",".$$txtlot.",".$$cboCount.",".$$cboYarnType.",".$$cboComposition.",".$$txtItemColor.",".$yd_color_id.",".$$txtCSP.",".$$txtnoBag.",".$$txtConeBag.",".$$txtNoCone.",".$$txtAVG.",".$$cboUom.",".str_replace(",",'',$$txtOrderqty).",".$$txtRate.",".str_replace(",",'',$$txtAmount).",".str_replace(",",'',$$txtProcessLoss).",".str_replace(",",'',$$txtTotalqty).",".str_replace(",",'',$$txtcounttype).",".str_replace(",",'',$$txtbuyerbuyer).",".str_replace(",",'',$$txtadjtype).",".str_replace(",",'',$$useFor).",".str_replace(",",'',$$appRef).",".str_replace(",",'',$$txtShade).",".str_replace(",",'',$$txtShadeId).",".str_replace(",",'',$$txtShadeMstId).",'".$user_id."','".$pc_date_time."')";

                $id1=$id1+1; $add_commaa++;
                //echo "10**INSERT INTO subcon_ord_breakdown (".$field_array3.") VALUES ".$data_array3; die;            
            }

            // echo "10**INSERT INTO yd_ord_mst (".$field_array.") VALUES ".$data_array; die;
           //echo "10**INSERT INTO yd_ord_dtls (".$field_array2.") VALUES ".$data_array2; die;
           $flag=true;
            $rID=sql_insert("yd_ord_mst",$field_array,$data_array,1);
            if($rID==1) $flag=1; else $flag=0;
            if($flag==1){
                $rID2=sql_insert("yd_ord_dtls",$field_array2,$data_array2,1);
                if($rID2==1) $flag=1; else $flag=0;
            }
            /*if(str_replace("'","",$cbo_within_group)==1){
                if($flag==1){
                    $rIDBooking=execute_query( "update wo_booking_mst set lock_another_process=1 where booking_no ='".$txt_order_no."'",1);
                    if($rIDBooking==1) $flag=1; else $flag=0;
                }
            }*/
            //echo "10**=$rID=$rID2=$flag"; die;
            if($db_type==0){
                if($flag==1){
                    mysql_query("COMMIT");  
                    echo "0**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_order_no);
                }else{
                    mysql_query("ROLLBACK"); 
                    echo "10**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_order_no);
                }
            }else if($db_type==2){
                if($flag==1){
                    oci_commit($con);
                    echo "0**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_order_no);
                }else{
                    oci_rollback($con);
                    echo "10**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_order_no);
                }
            }
        }
        disconnect($con);
        die;
    }
    else if ($operation==1)   // Update Here
    {
        $con = connect();
        if($db_type==0)
        {
            mysql_query("BEGIN");
        }
        
        $receive_date=strtotime(str_replace("'",'',$txt_order_receive_date));
        $delivery_date=strtotime(str_replace("'",'',$txt_delivery_date));

        if($db_type==0)
		{
            $txt_delivery_date=change_date_format(str_replace("'",'',$txt_delivery_date),'yyyy-mm-dd');
            $txt_rec_start_date=change_date_format(str_replace("'",'',$txt_rec_start_date),'yyyy-mm-dd');
            $txt_rec_end_date=change_date_format(str_replace("'",'',$txt_rec_end_date),'yyyy-mm-dd');
            $txt_order_receive_date=change_date_format(str_replace("'",'',$txt_order_receive_date),'yyyy-mm-dd');
        }
		else
		{
            $txt_delivery_date=change_date_format(str_replace("'",'',$txt_delivery_date), "", "",1);
            $txt_rec_start_date=change_date_format(str_replace("'",'',$txt_rec_start_date), "", "",1);
            $txt_rec_end_date=change_date_format(str_replace("'",'',$txt_rec_end_date), "", "",1);
            $txt_order_receive_date=change_date_format(str_replace("'",'',$txt_order_receive_date), "", "",1);
        }



			$sql_pi= sql_select("select  a.pi_number,a.id from  com_export_pi_mst a,com_export_pi_dtls b  where a.id=b.pi_id and b.work_order_no='$txt_job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pi_number,a.id");
			$pi_data=array();
			foreach($sql_pi as $row)
			{
 				$all_pi_number.=$row[csf('pi_number')].",";
				$all_job_dtls_id.=$row[csf('id')].",";
 			}
 			$all_pi_number=chop($all_pi_number,",");
			$all_job_dtls_id=chop($all_job_dtls_id,",");
 			$all_pi_count=count($sql_pi)	;
			if($all_pi_count)
			{
				if($all_pi_count>0)
				{
					echo "20**Pi Found So Update/Delete Not Possible. "."PI No :" .$all_pi_number=str_replace("'","",$all_pi_number); disconnect($con); oci_rollback($con); disconnect($con); die;
				}
			}
	


		 


 		 /*	$next_process_pi=return_field_value( "work_order_no", "com_export_pi_mst a,com_export_pi_dtls b"," a.id=b.pi_id and work_order_no='$txt_job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
  			//echo "10**select work_order_no from com_export_pi_dtls where work_order_no='$txt_job_no' and status_active=1 and is_deleted=0 ".$next_process_bill; die;
			//$chk_next_process=0;  
 			if($next_process_pi!='' )
			{
				echo "20**".str_replace("'",'',$txt_job_no);
				disconnect($con);
				die;
			}*/
 


        $field_array="party_id*party_location*currency_id*receive_date*delivery_date*rec_start_date*rec_end_date*order_id*order_no*booking_without_order*booking_type*remarks*check_box_id*exchange_rate*tag_pi_no*order_type*yd_type*yd_process*attention*team_leader*team_member*party_ref*pro_type*check_box_confirm* check_box_advance*advance_job*updated_by*update_date";

        $data_array="'".$cbo_party_name."'*'".$cbo_party_location."'*'".$cbo_currency."'*'".$txt_order_receive_date."'*'".$txt_delivery_date."'*'".$txt_rec_start_date."'*'".$txt_rec_end_date."'*'".$hid_order_id."'*'".$txt_order_no."'*'".$is_without_order."'*'".$hid_booking_type."'*'".$txt_remarks."'*'".$txt_check_box."'*'".$txt_exchange_rate."'*'".$txt_tag_pi_no."'*'".$cbo_order_type."'*'".$cbo_yd_type."'*'".$cbo_yd_process."'*'".$attention."'*'".$cbo_team_leader."'*'".$cbo_team_member."'*'".$party_ref."'*'".$cbo_pro_type."'*'".$txt_check_box_confirm."'*'".$txt_check_box_advance."'*'".$txt_advance_job."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
            
       $field_array2="order_id*order_no*style_ref*sales_order_no*sales_order_id*product_id*process_id*process_name*lot*count_id*yarn_type_id*yarn_composition_id*item_color_id*yd_color_id*csp*no_bag*cone_per_bag*no_cone*avg_wgt*uom*order_quantity*rate*amount*process_loss*total_order_quantity*count_type*buyer_buyer*adj_type*use_for*app_ref*shade*shade_dtls_id*shade_mst_id*updated_by*update_date";

       $field_array3="id, mst_id, job_no_mst, order_id, order_no, style_ref, sales_order_no, sales_order_id, product_id, process_id,process_name, lot, count_id, yarn_type_id, yarn_composition_id, item_color_id, yd_color_id, csp, no_bag, cone_per_bag, no_cone, avg_wgt, uom, order_quantity,  rate, amount, process_loss, total_order_quantity,count_type,buyer_buyer,adj_type,use_for,app_ref,shade,shade_dtls_id,shade_mst_id, inserted_by, insert_date";
	   
	   
	
		$sql_receive = "select  b.receive_qty,b.job_dtls_id from  yd_material_mst a, yd_material_dtls b  where a.id=b.mst_id and a.embl_job_no='$txt_job_no' and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 order by  b.job_dtls_id";
		//echo "10**".$sql_receive;die;
		$sql_receive_result = sql_select($sql_receive);
		$receive_data=array();
		foreach($sql_receive_result as $row)
		{
			$receive_data[$row[csf('job_dtls_id')]]["receive_qty"]+=$row[csf('receive_qty')];;
 		}


 //echo "10**".print_r($receive_data);die;
       $id1=return_next_id( "id", "yd_ord_dtls",1);
       
        $data_array2=array(); $add_commaa=0; $receive_qty=0;$detailsOrderqty =0;
        for($i=1; $i<=$total_row; $i++)
        {  


            $txtstyleRef            = "txtstyleRef_".$i;
            $txtsaleOrder           = "txtsaleOrder_".$i;
            $txtsaleOrderID         = "txtsaleOrderID_".$i;
            $txtProductID           = "txtProductID_".$i;
            $txtprocess             = "txtprocess_".$i;
            $txtlot                 = "txtlot_".$i;
            $cboCount               = "cboCount_".$i;
            $cboYarnType            = "cboYarnType_".$i;
            $cboComposition         = "cboComposition_".$i;

            $txtItemColor           = "txtItemColor_".$i;
            $txtYarnColor           = "txtYarnColor_".$i;
            $txtItemColorID         = "txtItemColorID_".$i;
            $txtYarnColorID         = "txtYarnColorID_".$i;
            $txtprocessname         = "txtprocessname_".$i;

            $txtCSP                 = "txtCSP_".$i;
            $txtnoBag               = "txtnoBag_".$i;          
            $txtConeBag             = "txtConeBag_".$i;
            $txtNoCone              = "txtNoCone_".$i;
            $txtAVG                 = "txtAVG_".$i;
            $cboUom                 = "cboUom_".$i;
            $txtOrderqty            = "txtOrderqty_".$i;
            $txtRate                = "txtRate_".$i;
            $txtAmount              = "txtAmount_".$i;
            $txtProcessLoss         = "txtProcessLoss_".$i;
            $txtTotalqty            = "txtTotalqty_".$i;
            $txtbuyerbuyer          = "txtbuyerbuyer_".$i;
            $txtcounttype           = "txtcounttype_".$i;
            $txtadjtype             = "txtadjtype_".$i;
            $hdnDtlsUpdateId        = "hdnDtlsUpdateId_".$i;
            $useFor                 = "useFor_".$i;
            $appRef                 = "appRef_".$i;
            $txtShade               = "txtShade_".$i;
            $txtShadeId             = "txtShadeId_".$i;
            $txtShadeMstId          = "txtShadeMstId_".$i;
            $dtlsUpdateId =str_replace("'",'',$$hdnDtlsUpdateId);
 		    $detailsOrderqty =str_replace("'",'',$$txtOrderqty);
		    $receive_qty=$receive_data[$dtlsUpdateId]["receive_qty"];
			if($detailsOrderqty*1<$receive_qty*1)
			{
				echo "40**$detailsOrderqty**$i"; 
				disconnect($con);die;
			}
			 
			
		//	echo "10**".$detailsOrderqty;

            /*if (str_replace("'", "", trim($$txtItemColor)) != "") {
                if (!in_array(str_replace("'", "", trim($$txtItemColor)),$new_array_color)){
                    $color_id = return_id( str_replace("'", "", trim($$txtItemColor)), $color_arr, "lib_color", "id,color_name","374");
                    $new_array_color[$color_id]=str_replace("'", "", trim($$txtItemColor));
                }
                else $color_id =  array_search(str_replace("'", "", trim($$txtItemColor)), $new_array_color);
            } else $color_id = 0;*/

            if (str_replace("'", "", trim($$txtYarnColor)) != "") 
			{
                if (!in_array(str_replace("'", "", trim($$txtYarnColor)),$new_array_color)){
                    $yd_color_id = return_id( str_replace("'", "", trim($$txtYarnColor)), $color_arr, "lib_color", "id,color_name","374");
                    $new_array_color[$yd_color_id]=str_replace("'", "", trim($$txtYarnColor));
                }
                else $yd_color_id =  array_search(str_replace("'", "", trim($$txtYarnColor)), $new_array_color);
            } else $yd_color_id = 0;

            if(str_replace("'",'',$$hdnDtlsUpdateId)!="")
            {
                $data_array2[$dtlsUpdateId]=explode("*",("".$hid_order_id."*'".$txt_order_no."'*".$$txtstyleRef."*".$$txtsaleOrder."*".$$txtsaleOrderID."*".$$txtProductID."*".$$txtprocess."*".$$txtprocessname."*".$$txtlot."*".$$cboCount."*".$$cboYarnType."*".$$cboComposition."*".$$txtItemColor."*".$yd_color_id."*".$$txtCSP."*".$$txtnoBag."*".$$txtConeBag."*".$$txtNoCone."*".$$txtAVG."*".$$cboUom."*".str_replace(",",'',$$txtOrderqty)."*".$$txtRate."*".str_replace(",",'',$$txtAmount)."*".str_replace(",",'',$$txtProcessLoss)."*".str_replace(",",'',$$txtTotalqty)."*".str_replace(",",'',$$txtcounttype)."*".str_replace(",",'',$$txtbuyerbuyer)."*".str_replace(",",'',$$txtadjtype)."*".str_replace(",",'',$$useFor)."*".str_replace(",",'',$$appRef)."*".str_replace(",",'',$$txtShade)."*".str_replace(",",'',$$txtShadeId)."*".str_replace(",",'',$$txtShadeMstId)."*".$user_id."*'".$pc_date_time."'"));
                $hdn_dtls_id_arr[]=str_replace("'",'',$$hdnDtlsUpdateId);
            }
            else
            {
				
				
				 if ($add_commaa!=0) $data_array3 .=",";    
                $data_array3 .="(".$id1.",".$update_id.",'".$txt_job_no."','".$hid_order_id."','".$txt_order_no."',".$$txtstyleRef.",".$$txtsaleOrder.",".$$txtsaleOrderID.",".$$txtProductID.",".$$txtprocess.",".$$txtprocessname.",".$$txtlot.",".$$cboCount.",".$$cboYarnType.",".$$cboComposition.",".$$txtItemColor.",".$yd_color_id.",".$$txtCSP.",".$$txtnoBag.",".$$txtConeBag.",".$$txtNoCone.",".$$txtAVG.",".$$cboUom.",".str_replace(",",'',$$txtOrderqty).",".$$txtRate.",".str_replace(",",'',$$txtAmount).",".str_replace(",",'',$$txtProcessLoss).",".str_replace(",",'',$$txtTotalqty).",".str_replace(",",'',$$txtcounttype).",".str_replace(",",'',$$txtbuyerbuyer).",".str_replace(",",'',$$txtadjtype).",".str_replace(",",'',$$useFor).",".str_replace(",",'',$$appRef).",".str_replace(",",'',$$txtShade).",".str_replace(",",'',$$txtShadeMstId).",".str_replace(",",'',$$txtShadeId).",'".$user_id."','".$pc_date_time."')";
				
                $id1=$id1+1; $add_commaa++;
            }
        }
		//die;

        $rID=sql_update("yd_ord_mst",$field_array,$data_array,"id",$update_id,0);  
        if($rID) $flag=1; else $flag=0;
        //echo "10**".bulk_update_sql_statement( "yd_ord_dtls", "id",$field_array2,$data_array2,$hdn_dtls_id_arr); die;

        if($data_array3!="" && $flag==1)
        {
			
			//echo "10**INSERT INTO yd_ord_dtls (".$field_array3.") VALUES ".$data_array3; die;
            $rID3=sql_insert("yd_ord_dtls",$field_array3,$data_array3,1);
            if($rID3==1) $flag=1; else $flag=0;
        }

        if($txt_deleted_id!="" && $flag==1)
        {
            $field_array_status="updated_by*update_date*status_active*is_deleted";
            $data_array_status=$user_id."*'".$pc_date_time."'*0*1";

            $rID4=sql_multirow_update("yd_ord_dtls",$field_array_status,$data_array_status,"id",$txt_deleted_id,0);
            if($flag==1)
            {
                if($rID4) $flag=1; else $flag=0; 
            }
        }

        if($data_array2!="" && $flag==1)
        {
			
			//echo "10**".bulk_update_sql_statement( "yd_ord_dtls", "id",$field_array2,$data_array2,$hdn_dtls_id_arr); die;
            $rID2=execute_query(bulk_update_sql_statement( "yd_ord_dtls", "id",$field_array2,$data_array2,$hdn_dtls_id_arr),1);
            if($rID2) $flag=1; else $flag=0;
        }
        
       // echo "10**$rID**$rID2**$rID3**$rID4**$flag"; die;
        if($db_type==0)
        {
            if($flag==1)
            {
                mysql_query("COMMIT");  
                echo "1**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no);
            }
            else
            {
                mysql_query("ROLLBACK"); 
                echo "10**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no);
            }
        }
        else if($db_type==2)
        {  
            if($flag==1)
            {
                oci_commit($con);
                echo "1**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no);
            }
            else
            {
                oci_rollback($con);
                echo "10**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no);
            }
        }
        disconnect($con);
        die;
    }
    else if ($operation==2)   // delete here
    {
        $con = connect();
        if($db_type==0)
        {
            mysql_query("BEGIN");  
        }
        
        /*$next_process=return_field_value( "id", "trims_job_card_mst"," entry_form=257 and $update_id=received_id and status_active=1 and is_deleted=0");
        if($next_process!=''){
            echo "20**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no);
            die;
        }
        $job_no="'".$txt_job_no."'";
        $order_no="'".$txt_order_no."'";*/
		
		/*
		$next_process_pi=return_field_value( "work_order_no", "com_export_pi_dtls","work_order_no='$txt_job_no' and status_active=1 and is_deleted=0");
			//echo "10**select work_order_no from com_export_pi_dtls where work_order_no='$txt_job_no' and status_active=1 and is_deleted=0 ".$next_process_bill; die;
			//$chk_next_process=0;  

			if($next_process_pi!='' )
			{
				echo "20**".str_replace("'",'',$txt_job_no);
				disconnect($con);
				die;
			}*/
			
			$sql_pi= sql_select("select  a.pi_number,a.id from  com_export_pi_mst a,com_export_pi_dtls b  where a.id=b.pi_id and b.work_order_no='$txt_job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pi_number,a.id");
			$pi_data=array();
			foreach($sql_pi as $row)
			{
 				$all_pi_number.=$row[csf('pi_number')].",";
				$all_job_dtls_id.=$row[csf('id')].",";
 			}
 			$all_pi_number=chop($all_pi_number,",");
			$all_job_dtls_id=chop($all_job_dtls_id,",");
 			$all_pi_count=count($sql_pi)	;
			if($all_pi_count)
			{
				if($all_pi_count>0)
				{
					echo "20**Pi Found So Update/Delete Not Possible. "."PI No :" .$all_pi_number=str_replace("'","",$all_pi_number); disconnect($con); oci_rollback($con); disconnect($con); die;
				}
			}
	
			
		//$sql_receive = "select  b.receive_qty,b.job_dtls_id from  yd_material_mst a, yd_material_dtls b  where a.id=b.mst_id and a.embl_job_no='$txt_job_no' and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 order by  b.job_dtls_id";
		//echo "10**".$sql_receive;die;
		//$sql_receive_result = sql_select($sql_receive);
		
		
		
		
			$next_process_receive= sql_select("select  a.yd_trans_no,a.id from  yd_material_mst a, yd_material_dtls b  where a.id=b.mst_id and a.yd_job_id='$update_id' and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and a.trans_type=1 group by a.yd_trans_no,a.id");
			$next_process_receive_data=array();
			foreach($next_process_receive as $row)
			{
 				$all_receive_number.=$row[csf('yd_trans_no')].",";
				$all_receive_id.=$row[csf('id')].",";
 			}
 			$all_receive_number=chop($all_receive_number,",");
			$all_receive_id=chop($all_receive_id,",");
 			$all_receive_count=count($next_process_receive)	;
			if($all_receive_count)
			{
				if($all_receive_count>0)
				{
					echo "20**Receive Found So Update/Delete Not Possible. "."Receive No :" .$all_receive_number=str_replace("'","",$all_receive_number); disconnect($con); oci_rollback($con); disconnect($con); die;
				}
			}
		
		
			/*$next_process_receive=return_field_value( "job_dtls_id", "yd_material_mst a, yd_material_dtls b","a.id=b.mst_id and a.embl_job_no='$txt_job_no' and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0");
		    if($next_process_receive!='')
			{
 				echo "18**".str_replace("'",'',$txt_job_no); 
				disconnect($con);die;
			}*/
		/*$receive_data=array();
		foreach($sql_receive_result as $row)
		{
			$receive_data[$row[csf('job_dtls_id')]]["receive_qty"]+=$row[csf('receive_qty')];;
 		}
			
		 $receive_qty=0;$detailsOrderqty =0;
        for($i=1; $i<=$total_row; $i++)
        {  
            $txtOrderqty            = "txtOrderqty_".$i;
            $hdnDtlsUpdateId        = "hdnDtlsUpdateId_".$i;
            $dtlsUpdateId =str_replace("'",'',$$hdnDtlsUpdateId);
 		    $detailsOrderqty =str_replace("'",'',$$txtOrderqty);
		    $receive_qty=$receive_data[$dtlsUpdateId]["receive_qty"];
			if($detailsOrderqty*1<$receive_qty*1)
			{
				
				echo "18**".str_replace("'",'',$txt_job_no); 
				disconnect($con);die;
			}
         }	*/
			
        $flag=0;
        $field_array="status_active*is_deleted*updated_by*update_date";
        $data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
        $rID=sql_update("yd_ord_mst",$field_array,$data_array,"id",$update_id,0);
        
        if($rID) $flag=1; else $flag=0; 
        
        if($flag==1)
        {
            $rID1=sql_update("yd_ord_dtls",$field_array,$data_array,"mst_id",$update_id,1);
            if($rID1) $flag=1; else $flag=0; 
        }   
        
        //echo "10**".$rID."**".$rID1."**".$rID2."**".$rID3."**".$flag; die;
        if($db_type==0)
        {
            if($flag==1)
            {
                mysql_query("COMMIT");  
                echo "2**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no);
            }
            else
            {
                mysql_query("ROLLBACK"); 
                echo "10**";
            }
        }
        else if($db_type==2)
        {
            if($rID)
            {
                oci_commit($con);
                echo "2**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no);
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



if($action=="yarn_dyeing_order_entry_print")
{
    extract($_REQUEST);
    $data=explode('*',$data);
    $company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
    $company_short_name_library=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
    $source_for_order = array(1 => 'In-House', 2 => 'Sub-Contract');
    $image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");

    
	 
    $count_arr = return_library_array("Select id, yarn_count from  lib_yarn_count where  status_active=1", 'id', 'yarn_count');

    $color_arr = return_library_array("select id,color_name from lib_color", "id", "color_name");

    if($data[2]==1)
    {
        $party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
        $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    }
    else
    {
        
        $party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    }

    $team_leader_sql = "select id,team_leader_name from lib_marketing_team where  status_active =1 and is_deleted=0 and project_type=10";

    $team_leader=sql_select($team_leader_sql);

    foreach ($team_leader as  $row) 
    {
        $team_leader_arr[$row[csf("id")]] = $row[csf("team_leader_name")];
    }
    unset($team_leader);


    $team_member_sql = "select b.id,b.team_member_name  from lib_marketing_team a, lib_mkt_team_member_info b where a.id=b.team_id and   a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.project_type=10";

    $team_member=sql_select($team_member_sql);

    foreach ($team_member as  $row) 
    {
        $team_member_arr[$row[csf("id")]] = $row[csf("team_member_name")];
    }
    unset($team_member);


    $sql = "SELECT id, company_id, location_id, within_group, party_id, party_location, order_id, order_no,yd_job, receive_date, rec_start_date, rec_end_date, delivery_date, remarks, currency_id, tag_pi_no, exchange_rate, attention, party_ref, yd_process, order_type,remarks, team_leader, team_member,pro_type,inserted_by from yd_ord_mst where id =$data[1] and status_active=1 and is_deleted=0  and entry_form = 374 order by id ASC";
    $qry_result=sql_select($sql);
	$inserted_by = $qry_result[0][csf('inserted_by')];

    $com_dtls = fnc_company_location_address($data[0], 0, 2);


    $sql_details = "SELECT id, count_type, yd_color_id, order_quantity, process_loss, buyer_buyer, style_ref,yarn_composition_id, item_color_id, order_quantity, total_order_quantity, no_cone, uom, yarn_type_id,use_for,app_ref,count_id,lot, sales_order_no, no_bag  from yd_ord_dtls where mst_id =$data[1] and status_active=1 and is_deleted=0 order by id ASC";
        
    $qry_result_details=sql_select($sql_details);

    foreach ($qry_result_details as  $row) 
    {
        $wo_arr[$row[csf("id")]]["count_type"] =$row[csf("count_type")];
        $wo_arr[$row[csf("id")]]["yd_color_id"] =$row[csf("yd_color_id")];
        $wo_arr[$row[csf("id")]]["item_color_id"] =$row[csf("item_color_id")];
        $wo_arr[$row[csf("id")]]["order_quantity"] =$row[csf("order_quantity")];
        $wo_arr[$row[csf("id")]]["process_loss"] =$row[csf("process_loss")];
        $wo_arr[$row[csf("id")]]["yarn_composition_id"] =$row[csf("yarn_composition_id")];
        $wo_arr[$row[csf("id")]]["order_quantity"] =$row[csf("order_quantity")];
        $wo_arr[$row[csf("id")]]["total_order_quantity"] =$row[csf("total_order_quantity")];
        $wo_arr[$row[csf("id")]]["no_cone"] =$row[csf("no_cone")];
        $wo_arr[$row[csf("id")]]["uom"] =$row[csf("uom")];
        $wo_arr[$row[csf("id")]]["yarn_type_id"] =$row[csf("yarn_type_id")];
        $wo_arr[$row[csf("id")]]["style_ref"] =$row[csf("style_ref")];
        $wo_arr[$row[csf("id")]]["buyer_buyer"] =$row[csf("buyer_buyer")];
        $wo_arr[$row[csf("id")]]["use_for"] =$row[csf("use_for")];
        $wo_arr[$row[csf("id")]]["app_ref"] =$row[csf("app_ref")];
        $wo_arr[$row[csf("id")]]["count"] =$row[csf("count_id")];
		$wo_arr[$row[csf("id")]]["lot"] =$row[csf("lot")];
        $wo_arr[$row[csf("id")]]["sales_order_no"] =$row[csf("sales_order_no")];
        $wo_arr[$row[csf("id")]]["no_bag"] =$row[csf("no_bag")];
    }

    foreach ($qry_result as  $row) 
    {

    ?>
    <style type="text/css">
        td.make_bold {
            font-weight: 900;
        }
    </style>
    <div  style="width:100%">
        <table width="1400" cellspacing="0" align="center" border="0">
            <tr>
                <td  align="left"><img style="margin-left: -10px;" src="../../../<? echo $com_dtls[2]; ?>" height="70" width="120"></td>
                <td colspan="3" align="center"><strong style="font-size:xx-large; text-align:left;" ><? echo $com_dtls[0]; ?></strong>
                    <br>
                    <? echo $com_dtls[1]; ?>
                <td align="right" >Y/D Process : <?php echo $yd_process_arr[$row[csf("yd_process")]];?></td>
            </tr>
            <tr><br>
                <?php
                $order_type = '';
                    if($row[csf("order_type")]==1){
                        $order_type = "Commission Yarn Dyeing Order";
                    }
                    else if($row[csf("order_type")]==2){
                        $order_type = "Sales Yarn Dyeing Order";
                    }
                ?>
                <td class="make_bold" style="font-size:large;" colspan="6" align="center">
                    <?php echo $order_type;?>
                </td>
            </tr>
        </table>
        <table width="1400" cellspacing="0" align="center" border="0">
            <tr>            
                <td width="120" >Order/Job No : </td> <td width="175" ><? echo $row[csf("yd_job")];//$company_short_name_library[$data[0]]; ?></td>
                <td width="120" >Buyer : </td> <td width="175" ><? echo $companys=str_replace(" and ", "&", $party_arr[$row[csf("party_id")]]);//$party_arr[$row[csf("party_id")]]; ?></td>
                <td width="120" >Confirm Date : </td> <td width="175" ><? echo change_date_format($row[csf('receive_date')]); ?></td>
            </tr>
            <tr>            
                <td width="120"  >Order No : </td> <td  width="175" ><? echo $row[csf("order_no")]; ?></td>
                <td width="120"  >Prod. Type : </td> <td  width="175" ><? echo $w_pro_type_arr[$row[csf("pro_type")]]; ?></td>
                <td width="120"  >Order Type : </td> <td  width="175" ><? echo $w_order_type_arr[$row[csf("order_type")]]; ?></td>
            </tr>
            <tr>
                <td width="120" >Buyer Ref. : </td> <td width="175" ><?  echo $row[csf('party_ref')];?></td>
                <td width="120" >Order By : </td> <td width="175" ><?  echo $team_leader_arr[$row[csf('team_leader')]];?></td>
                <td width="120" >Merchants : </td> <td width="175" ><?  echo $team_member_arr[$row[csf('team_member')]];?></td>          
            </tr>
            <tr> 
                 <td width="120" >Delivery Date : </td> <td width="175" ><? echo change_date_format($row[csf('delivery_date')]); ?></td>
                <td width="120" >Assign PI No : </td> <td width="175" ><? echo $row[csf('tag_pi_no')]; ?></td>  
                 <td width="120" >Special Instruction : </td> <td colspan="5" ><? echo $row[csf('remarks')]; ?></td>        
            </tr>
             
        </table>
        <br>
        <div style="width:100%;">
            <table align="left" cellspacing="0" width="1400"  border="1" rules="all" class="rpt_table"  >
                <thead>
                    <tr>
                        <th width="120" >Style Ref.</th>
                        <th width="120" >End Customer</th>
                        <th width="150" >Job No</th>
                        <th width="280">Yarn Description</th>
                        <th width="50" >Count Type</th>
                        <th width="70" >Lot</th>
                        <th width="60">Use For</th>
                        <th width="95">App. Ref.</th>
                        <th width="200">Y/D Colour</th>
                        <th width="100">Colour Range</th>
                        <th width="70">Order Qty.</th>
                        <th width="30">P.Loss%</th>
                        <th width="70">Dyed yarn Qty.</th>
                        <th width="30">Unit</th>
                        <th width="80">Cone Qty.</th>
                        <th width="80">Bag Qty</th>
                    </tr>
                </thead>
                <tbody>
                <?
                $tblRow=1; $i=1;

                $total_order=$total_yd_qty=$total_cone_qty=$total_bag_qty=0;

                $count_type_arr = array(1 => "Single",2 => "Double");
                foreach($wo_arr as $yd_details_id=> $yd_data)
                {
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
                        <td width="120" style="word-break:break-all"><?  echo $wo_arr[$yd_details_id]["style_ref"] ; ?></td>
                        <td width="120" style="word-break:break-all"><?  echo  $buyerbuye=str_replace(" and ", "&", $wo_arr[$yd_details_id]["buyer_buyer"]); 	//$wo_arr[$yd_details_id]["buyer_buyer"] ; ?></td>
                        <td width="120" style="word-break:break-all"><?  echo $wo_arr[$yd_details_id]["sales_order_no"]; ?></td>
                        <td width="280" style="word-break:break-all"><?  echo $composition[$wo_arr[$yd_details_id]["yarn_composition_id"]].' '.$yarn_type[$wo_arr[$yd_details_id]["yarn_type_id"]].', '.$count_arr[$wo_arr[$yd_details_id]["count"]]; ?></td>
                        <td width="50" style="word-break:break-all" ><?  echo $count_type_arr[$wo_arr[$yd_details_id]["count_type"]] ; ?></td>
                         <td width="70" style="word-break:break-all" ><?  echo $wo_arr[$yd_details_id]["lot"]; ?></td>
                        <td width="60" style="word-break:break-all"><?  echo $wo_arr[$yd_details_id]["use_for"] ; ?></td>
                        <td width="95" style="word-break:break-all"><?  echo $wo_arr[$yd_details_id]["app_ref"] ; ?></td>
                        <td width="200" style="word-break:break-all"><?  echo $color_arr[$wo_arr[$yd_details_id]["yd_color_id"]] ; ?></td> 
                        <td width="100" style="word-break:break-all"><?  echo $color_range[$wo_arr[$yd_details_id]["item_color_id"]] ; ?></td>  
                        <td width="70" style="word-break:break-all"><?  echo number_format($wo_arr[$yd_details_id]["order_quantity"],2,".",""); ?></td>
                        <td width="30" align="center"><?  echo $wo_arr[$yd_details_id]["process_loss"] ; ?></td>
                        <td width="70" align="center"><? echo number_format($wo_arr[$yd_details_id]["total_order_quantity"],2,".","");   ?></td>
                        <td width="30" align="center" align="right"><?  echo $unit_of_measurement[$wo_arr[$yd_details_id]["uom"]] ; ?></td>
                        <td  align="center" align="right"><?  echo number_format($wo_arr[$yd_details_id]["no_cone"],0,".","");   ?></td>
                        <td  align="center" align="right"><?  echo number_format($wo_arr[$yd_details_id]["no_bag"],0,".","");   ?></td>
                    </tr>
                    <?
                    $total_order += $wo_arr[$yd_details_id]["order_quantity"];
                    $total_yd_qty += $wo_arr[$yd_details_id]["total_order_quantity"];
                    $total_cone_qty += $wo_arr[$yd_details_id]["no_cone"];
                    $total_bag_qty += $wo_arr[$yd_details_id]["no_bag"];
                    $tblRow++; 
                    }
                    ?>
                    <tr>
                        <td colspan="10" align="right">Total:</td>
                        <td align="center"><?php  echo number_format($total_order,2,".",""); //echo $total_order;?></td>
                        <td></td>
                        <td align="center"><?php  echo number_format($total_yd_qty,2,".",""); //echo $total_yd_qty;?></td>
                        <td></td>
                        <td align="center"><?php  echo number_format($total_cone_qty,0,".",""); //echo $total_cone_qty;?></td>
                        <td align="center"><?php  echo number_format($total_bag_qty,0,".","");?></td>
                    </tr>
                </table>
            </div>
        <br>
        <br>
        <br>
    </div>
    <br>
    <?

    //$inserted_by = $_SESSION['logic_erp']['user_id'];
    echo signature_table(113, $data[0], "1100px",$cbo_template_id,50,$inserted_by);

    ?>
</div>
<?
}
exit();
}


if($action=="shade_popup")
{
    echo load_html_head_contents("Process Popup","../../../", 1, 1, $unicode);
    extract($_REQUEST);

    $cbo_company_name    =str_replace("'",'',$cbo_company_name);
    $cbo_within_group    =str_replace("'",'',$cbo_within_group);
    $cbo_party_name      =str_replace("'",'',$cbo_party_name);
    $txt_delivery_date   =str_replace("'",'',$txt_delivery_date);
    $color_range_id      =str_replace("'",'',$color_range_id);

    if($db_type==0){
        $txt_delivery_date=change_date_format(str_replace("'",'',$txt_delivery_date),'yyyy-mm-dd');
    }else{
        $txt_delivery_date=change_date_format(str_replace("'",'',$txt_delivery_date), "", "",1);
    }

    $sql_cond = "";

    if($cbo_company_name!=0)
    {
        $sql_cond .=" and a.company_id=$cbo_company_name";
    }

    if($cbo_within_group!=0)
    {
        $sql_cond .=" and a.within_group=$cbo_within_group";
    }
    if($cbo_party_name!=0)
    {
        $sql_cond .=" and a.party_id=$cbo_party_name";
    }

    if($color_range_id!=0)
    {
        $sql_cond .=" and b.color_range_id=$color_range_id";
    }

    if($txt_delivery_date!='')
    {
        if($db_type==0)
        { 
            
            $sql_cond .= " and a.applicable_upto_date >= '".change_date_format($txt_delivery_date,'yyyy-mm-dd')."'";
        }
        else
        {
           
            $sql_cond .= " and a.applicable_upto_date >= '".change_date_format($txt_delivery_date, "", "",1)."'";
        }
    }

    $sql = "select a.id as mst_id, a.applicable_upto_date, b.id, b.color_range_id,  b.uper_limit,  b.lower_limit, b.price from shade_entry_mst a, shade_entry_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond";

    $result = sql_select($sql);

    ?>
    <script type="text/javascript">
        function js_set_value(value,rate,id,mst_id)
        { 

            $("#hidden_uper_limit").val(value);
            $("#hidden_rate").val(rate);
            $("#hidden_id").val(id);
            $("#hidden_shade_mst_id").val(mst_id);
            parent.emailwindow.hide();
        }
    </script>
    </head>
    <body>
        <div align="center">
            <input type="hidden" name="hidden_uper_limit" id="hidden_uper_limit" class="text_boxes" style="width:70px">
            <input type="hidden" name="hidden_rate" id="hidden_rate" class="text_boxes" style="width:70px">
            <input type="hidden" name="hidden_id" id="hidden_id" class="text_boxes" style="width:70px">
            <input type="hidden" name="hidden_shade_mst_id" id="hidden_shade_mst_id" class="text_boxes" style="width:70px">
            <table width="100%" cellspacing="0" class="rpt_table" border="0"   rules="all">
                <thead>
                    <tr>
                        <th width="40">Sl</th>
                        <th width="150">Color Range</th>
                        <th width="150">Lower Limit(Shade %)</th>
                        <th width="150">Upper Limit(Shade %)</th>
                        <th width="150">Price [USD]</th>
                        <th width="150">Applicable Date Upto</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $i=1;

                        if(count($result)<=0)
                        {
                            echo "<tr><td align='center' colspan='6'><h3  style='color:red; float:center'> No data Found!!!</h3></td></tr>";
                            die;
                        }
                        else
                        {
                            foreach($result as $data)
                            {
                                if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

                        ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value(<? echo $data[csf('uper_limit')];?>,<? echo $data[csf('price')];?>,<? echo $data[csf('id')];?>,<? echo $data[csf('mst_id')];?>)">
                                <td><?php echo $i;?></td>
                                <td align="center" width="150" ><?php echo $color_range[$data[csf('color_range_id')]];?></td>
                                <td align="center" width="150" ><?php echo $data[csf('lower_limit')];?></td>
                                <td align="center" width="150" ><?php echo $data[csf('uper_limit')];?></td>
                                <td align="center" width="150" ><?php echo $data[csf('price')];?></td>
                                <td align="center" width="150" ><?php echo $data[csf('applicable_upto_date')];?></td>
                            </tr>
                        <?php
                                $i++;
                            }
                        }
                        ?>
                </tbody>
            </table>
        </div>
    </body>
    <?php
    exit();
}

if($action=="populate_shade_rate_from_data")
{

    $data = explode("_",$data);

    $cbo_company_name    =str_replace("'",'',$data[0]);
    $cbo_within_group    =str_replace("'",'',$data[1]);
    $cbo_party_name      =str_replace("'",'',$data[2]);
    $txt_delivery_date   =str_replace("'",'',$data[3]);
    $color_range_id      =str_replace("'",'',$data[4]);
    $shade_limit         =str_replace("'",'',$data[5]);

    $row_num      =str_replace("'",'',$data[6]);

    $sql_cond = "";

    if($cbo_company_name!=0)
    {
        $sql_cond .=" and a.company_id=$cbo_company_name";
    }

    if($cbo_within_group!=0)
    {
        $sql_cond .=" and a.within_group=$cbo_within_group";
    }
    if($cbo_party_name!=0)
    {
        $sql_cond .=" and a.party_id=$cbo_party_name";
    }

    if($color_range_id!=0)
    {
        $sql_cond .=" and b.color_range_id=$color_range_id";
    }

    if($shade_limit!='')
    {
        $sql_cond .=" and b.lower_limit<=$shade_limit and b.uper_limit>=$shade_limit";
    }
    else
    {
        echo "alert('Please Input Shade %')";
        die;
    }

    if($txt_delivery_date!='')
    {
        if($db_type==0)
        { 
            
            $sql_cond .= " and a.applicable_upto_date >= '".change_date_format($txt_delivery_date,'yyyy-mm-dd')."'";
        }
        else
        {
           
            $sql_cond .= " and a.applicable_upto_date >= '".change_date_format($txt_delivery_date, "", "",1)."'";
        }
    }

    $sql = "select a.id as mst_id, a.applicable_upto_date, b.id, b.color_range_id,  b.uper_limit,  b.lower_limit, b.price from shade_entry_mst a, shade_entry_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond order by a.applicable_upto_date desc";

    $result = sql_select($sql);

    if(count($result)>0)
    {
        foreach($result as $data)
        {
            echo "document.getElementById('txtShade_".$row_num."').value         = '".$data[csf("uper_limit")]."';\n";
            echo "document.getElementById('txtRate_".$row_num."').value         = '".$data[csf("price")]."';\n";
            echo "document.getElementById('txtShadeId_".$row_num."').value         = '".$data[csf("id")]."';\n";
            echo "document.getElementById('txtShadeMstId_".$row_num."').value         = '".$data[csf("mst_id")]."';\n";
            echo "$('#txtRate_".$row_num."').attr('readonly','readonly');\n";
        }
    }
    else
    {
        echo "alert('Rate Not Found!!!');\n";
        echo "document.getElementById('txtShade_".$row_num."').value            = '';\n";
        echo "document.getElementById('txtRate_".$row_num."').value            = '';\n";
        echo "$('#txtRate_".$row_num."').removeAttr('readonly');\n";
        die;
    }
    exit();
}


if ($action=="job_no_popup")
{
    echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
    extract($_REQUEST);
    $data=explode('_',$data);
    ?>  
    <script>
        function js_set_value(str)
        {
            $("#hdn_job_info").val(str); 
            parent.emailwindow.hide();
        }  
    </script>
    <input type="hidden" id="hdn_job_info" />
    <?
    //if ($data[1]==0) $buyer_name=""; else $buyer_name=" and buyer_name=$data[1]";
    // if ($data[2]=="") $order_no=""; else $order_no=" and a.po_number=$data[2]";
    //$job_no=str_replace("'","",$txt_job_id);
    //if ($data[2]=="") $job_no_cond=""; else $job_no_cond="  and job_no_prefix_num in('$data[2]')";
    
    //$sql="select id, job_no_prefix_num, yd_job, order_no, pro_type, order_type, yd_type, yd_process, tag_pi_no from yd_ord_mst where company_id=$data[0] and status_active=1 and is_deleted=0 and entry_form=374 and order_type=2 and check_box_advance=1 and yd_job not in(select YD_JOB from com_export_pi_mst where item_category_id=69 and YD_JOB is not null and status_active in(1,2,3) and is_deleted=0) order by id desc";//$job_no_cond

    $sql="select a.id, a.job_no_prefix_num, a.yd_job, a.order_no, a.pro_type, a.order_type, a.yd_type, a.yd_process, c.pi_number, sum(b.order_quantity) as order_quantity from yd_ord_mst a, yd_ord_dtls b, com_export_pi_mst c where a.id=b.mst_id and a.company_id=$data[0] and a.status_active=1 and a.is_deleted=0 and a.entry_form=374 and a.order_type=2 and a.check_box_advance=1 and a.yd_job=c.YD_JOB group by a.id, a.job_no_prefix_num, a.yd_job, a.order_no, a.pro_type, a.order_type, a.yd_type, a.yd_process, c.pi_number order by id desc";//$job_no_cond

    $sql1="select a.advance_job, a.tag_pi_no, sum(b.order_quantity) as order_quantity from yd_ord_mst a, yd_ord_dtls b where a.id=b.mst_id and a.company_id=$data[0] and a.status_active=1 and a.is_deleted=0 and a.entry_form=374 and a.check_box_confirm=1 and a.tag_pi_no is not null and a.advance_job is not null group by a.advance_job, a.tag_pi_no ";//$job_no_cond

    $sql_pre_order_res=sql_select($sql1);

    $pre_qty_arr = array();

    foreach($sql_pre_order_res as $data)
    {
        $pre_qty_arr[$data[csf('advance_job')]][$data[csf('tag_pi_no')]] += $data[csf('order_quantity')];
    }

    $sql_res=sql_select($sql);
    // echo $sql;

    //$arr=array(2=>$w_pro_type_arr,3=>$w_order_type_arr,4=>$yd_type_arr,5=>$yd_process_arr);
    
    //echo  create_list_view("list_view", "YD Job No,YD Worder No,Prod. Type,Order Type,Y/D Type,Y/D Process", "100,100,100,100,100,100","660","350",0, $sql, "js_set_value", "id,yd_job", "", 1, "0,0,pro_type,order_type,yd_type,yd_process", $arr , "yd_job,order_no,pro_type, order_type, yd_type, yd_process", "export_pi_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0,0','') ;
    ?>
    <table cellpadding="0" cellspacing="0" border="1" rules="all" width="725" class="rpt_table">
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="100">YD Job No</th>
                <th width="100">YD Worder No</th>
                <th width="100">Pi No</th>
                <th width="80">Prod. Type</th>
                <th width="80">Order Type</th>
                <th width="80">Y/D Type</th>
                <th width="80">Y/D Process</th>
                <th width="50">Order Qty</th>
                <th width="50">Balance</th>               
            </tr>
        </thead>
    </table>
    <div style="width:740px; max-height:350; overflow-y:scroll;">
        <table cellpadding="0" cellspacing="0" border="1" rules="all" width="725" class="rpt_table" id="list_view">         
            <?
            $i=1;
            foreach($sql_res as $row)
            {
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

                $balance = $row[csf('order_quantity')]-$pre_qty_arr[$row[csf('yd_job')]][$row[csf('pi_number')]];

                if($balance>0)
                {
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $i; ?>" onClick="js_set_value('<? echo $row[csf('id')].'_'.$row[csf('yd_job')].'_'.$row[csf('pi_number')].'_'.$balance;?>')" style="cursor:pointer" align="center">
                        <td width="30"><? echo $i; ?></td>
                        <td width="100" align="center"><? echo $row[csf('yd_job')]; ?></td>
                        <td width="100" align="center"><? echo $row[csf('order_no')]; ?></td>
                        <td width="100" align="center"><? echo $row[csf('pi_number')]; ?></td>
                        <td width="80" align="center"><? echo $w_pro_type_arr[$row[csf('pro_type')]]; ?></td>
                        <td width="80" align="center"><? echo $w_order_type_arr[$row[csf('order_type')]]; ?></td>
                        <td width="80" align="center"><? echo $yd_type_arr[$row[csf('yd_type')]]; ?></td>
                        <td width="80" align="center"><? echo $yd_type_arr[$row[csf('yd_type')]]; ?></td>
                        <td width="50" align="right"><? echo $row[csf('order_quantity')]; ?></td>
                        <td width="50" align="right"><? echo $balance; ?></td>
                    </tr>
                    <?
                    $i++;
                }
            }
            ?>              
        </table>
        <script>
            setFilterGrid('list_view');
        </script>
    </div>  
    <?

    exit();
}
?>
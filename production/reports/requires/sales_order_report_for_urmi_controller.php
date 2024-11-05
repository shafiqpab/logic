<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

$search_by_arr=array(1=>"Date Wise Report",2=>"Wait For Heat Setting",5=>"Wait For Singeing",3=>"Wait For Dyeing",4=>"Wait For Re-Dyeing");//--------------------------------------------------------------------------------------------------------------------
if($action=="load_drop_down_buyer")
{
	echo load_html_head_contents("Buyer Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$data=explode('_',$data);
	$company=$data[0];
	if($company>0)
	{
		echo create_drop_down( "cbo_buyer_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", 0, "" );
	}
	else
	{
		echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "-- Select Buyer --", $selected, "",0,"","","","");
	}
	exit();
}

if ($action == "load_drop_down_buyer____") 
{
	$data = explode("_", $data);
	$company_id = $data[1];

	if ($data[0] == 0)
	{
		echo create_drop_down("cbo_buyer_name", 140, $blank_array, "", 1, "--Select Buyer--", 0, "");
	} 
	else 
	{
		if ($data[0] == 1) 
		{
			echo create_drop_down("cbo_buyer_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Buyer --", "0", "", 0);
		} 
		else if ($data[0] == 2) 
		{
			echo create_drop_down("cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90,80)) order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "", 0);
		}
	}
	exit();
}

if($action=="booking_No_popup")
{
    echo load_html_head_contents("Job Info","../../../", 1, 1, '','1','');
    extract($_REQUEST);
    ?>
    <script>
        function js_set_value(job_id,job_no,booking_no)
        {
            
            document.getElementById('hidden_job_id').value=job_id;
            document.getElementById('hidden_job_no').value=job_no;
            document.getElementById('hidden_booking_no').value=booking_no;
            parent.emailwindow.hide();
        }    
    </script>
    </head>
    <body>
    <div align="center">
        <fieldset style="width:830px;margin-left:4px;">
            <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
                <table cellpadding="0" cellspacing="0" width="700" border="1" rules="all" class="rpt_table">
                    <thead>
                        <th>Year</th>
                        <th>Within Group</th>
                        <th>Company</th>
                        <th>Search By</th>
                        <th>Search</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                            <input type="hidden" name="hidden_job_id" id="hidden_job_id" value="">
                            <input type="hidden" name="hidden_job_no" id="hidden_job_no" value="">
                            <input type="hidden" name="hidden_booking_no" id="hidden_booking_no" value="">
                        </th> 
                    </thead>
                    <tr class="general">
                        <td> 
                            <?
                                echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
                            ?>
                        </td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_within_group", 80, $yes_no,"", 0, "-- All --", 1, "",0,"" );
                            ?>
                        </td>
                        <td align="center"> 
                           <?
                             echo create_drop_down( "cbo_company_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", 0, "" );
                            ?>
                        </td>
                        <td align="center"> 
                            <?
                                $search_by_arr=array(1=>"Sales Order No",2=>"Sales / Booking No",3=>"Style Ref.");
                                echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", 2,$dd,0 );
                            ?>
                        </td>                 
                        <td align="center">             
                            <input type="text" style="width:140px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />   
                        </td>                       
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year').value, 'create_booking_search_list_view', 'search_div', 'sales_order_report_for_urmi_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                        </td>
                    </tr>
                </table>
                <div id="search_div" style="margin-top:10px"></div>   
            </form>
        </fieldset>
    </div>    
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_booking_search_list_view")
{
    $data=explode('_',$data);
    // echo "<pre>"; print_r($data);
    if ($data[2]==0) 
    {
        echo "<span style='color:red;'><strong>Please Select Company</strong></span>";die;
    }
    $company_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
    $buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
    $location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
    
    $search_string=trim($data[0]);
    $search_by =$data[1];
    $company_id =$data[2];
    $within_group=$data[3];
    $cbo_year=$data[4];
    
    if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
    else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
    else $year_field="";//defined Later

    if($db_type==0)
    {
        if($cbo_year==0) $year_cond=""; else $year_cond="and year(a.insert_date)='$cbo_year'";
    }
    else if($db_type==2)
    {
        if($cbo_year==0) $year_cond=""; else $year_cond="and to_char(a.insert_date,'YYYY')='$cbo_year'";
    }
    else $year_cond="";

    if($within_group==0) $within_group_cond=""; else $within_group_cond=" and a.within_group=$within_group";
    if ($within_group==1) // Yes
    {
        $company_cond=" and a.buyer_id=$company_id";
        $search_field_cond='';
        if($search_string!="")
        {
            if($search_by==1)
            {
                $search_field_cond=" and a.job_no_prefix_num='$search_string'";
            }
            else if($search_by==2)
            {
                $search_field_cond=" and b.booking_no_prefix_num='$search_string'";
            }
            else
            {
                $search_field_cond=" and a.style_ref_no ='$search_string'";
            }
        }
        $sql = "SELECT a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.po_buyer, a.style_ref_no, a.location_id 
        from fabric_sales_order_mst a, wo_booking_mst b where a.booking_id=b.id  and a.sales_booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 $company_cond $within_group_cond $search_field_cond $year_cond
        union all
        SELECT a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.po_buyer, a.style_ref_no, a.location_id 
        from fabric_sales_order_mst a, wo_non_ord_samp_booking_mst b where a.booking_id=b.id  and a.sales_booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 $company_cond $within_group_cond $search_field_cond $year_cond order by id";
    }
    else // No
    {
        $search_field_cond='';
        if($search_string!="")
        {
            if($search_by==1)
            {
                $search_field_cond=" and a.job_no_prefix_num='$search_string'";
            }
            else if($search_by==2)
            {
                $search_field_cond=" and a.sales_booking_no='$search_string'";
            }
            else
            {
                $search_field_cond=" and a.style_ref_no ='$search_string'";
            }
        }
        $company_cond=" and a.company_id=$company_id";
        $sql = "SELECT a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.po_buyer, a.style_ref_no, a.location_id from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 $company_cond $within_group_cond $search_field_cond $year_cond order by a.id";
    }
    
    // $sql = "SELECT a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.po_buyer, a.style_ref_no, a.location_id from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 $company_cond $within_group_cond $search_field_cond $year_cond order by a.id"; 
    // echo $sql;//die;
    $result = sql_select($sql);
    ?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="110">Sales Order No</th>
            <th width="40">Year</th>
            <th width="80">Within Group</th>
            <th width="70">Buyer</th>               
            <th width="120">Sales/ Booking No</th>
            <th width="80">Booking date</th>
            <th width="110">Style Ref.</th>
            <th>Location</th>
        </thead>
    </table>
    <div style="width:800px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">    
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table" id="tbl_list_search">  
            <?
            $i=1;
            foreach ($result as $row)
            {
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                     
                if($row[csf('within_group')]==1)
                    $buyer=$buyer_arr[$row[csf('po_buyer')]]; 
                else
                    $buyer=$buyer_arr[$row[csf('buyer_id')]];
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]; ?>','<? echo $row[csf('job_no')]; ?>','<? echo $row[csf('sales_booking_no')]; ?>');"> 
                    <td width="40"><? echo $i; ?></td>
                    <td width="110"><p>&nbsp;<? echo $row[csf('job_no')]; ?></p></td>
                    <td width="40" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="80"><p><? echo $yes_no[$row[csf('within_group')]]; ?>&nbsp;</p></td>
                    <td width="70"><p><? echo $buyer; ?>&nbsp;</p></td>
                    <td width="120"><p><? echo $row[csf('sales_booking_no')]; ?></p></td>               
                    <td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
                    <td width="110"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                    <td><p><? echo $location_arr[$row[csf('location_id')]]; ?></p></td>
                </tr>
                <?
                $i++;
            }
            ?>
        </table>
    </div>
    <?  
    exit(); 
}

if($action=="jobNo_popup")
{
    echo load_html_head_contents("Job Info","../../../", 1, 1, '','1','');
    extract($_REQUEST);
	?>
    <script>
        function js_set_value(job_id,job_no,booking_no)
        {
            
            document.getElementById('hidden_job_id').value=job_id;
            document.getElementById('hidden_job_no').value=job_no;
            document.getElementById('hidden_booking_no').value=booking_no;
            parent.emailwindow.hide();
        }
    </script>
	</head>
	<body>
		<div align="center">
		    <fieldset style="width:830px;margin-left:4px;">
		        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		            <table cellpadding="0" cellspacing="0" width="700" border="1" rules="all" class="rpt_table">
		                <thead>
                            <th>Year</th>
                            <th>Within Group</th>
		                    <th>Search By</th>
		                    <th>Search</th>
		                    <th>
		                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
		                        <input type="hidden" name="hidden_job_id" id="hidden_job_id" value="">
		                         <input type="hidden" name="hidden_job_no" id="hidden_job_no" value="">
		                          <input type="hidden" name="hidden_booking_no" id="hidden_booking_no" value="">
		                    </th> 
		                </thead>
		                <tr class="general">                              
                            <td> 
                                <?
                                    echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
                                ?>
                            </td> 
		                    <td align="center"> 
		                        <?
		                            echo create_drop_down( "cbo_within_group", 150, $yes_no,"",0, "--Select--", 1,"",0 );
		                        ?>
		                    </td>
		                    <td align="center"> 
		                        <?
		                            $search_by_arr=array(1=>"Sales Order No",2=>"Sales / Booking No",3=>"Style Ref.");
		                            echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
		                        ?>
		                    </td>                 
		                    <td align="center">             
		                        <input type="text" style="width:140px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />   
		                    </td>                       
		                    <td align="center">
		                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year').value, 'create_job_search_list_view', 'search_div', 'sales_order_report_for_urmi_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
		                    </td>
		                </tr>
		            </table>
		            <div id="search_div" style="margin-top:10px"></div>   
		        </form>
		    </fieldset>
		</div>    
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_job_search_list_view")
{
    $data=explode('_',$data);
    
    $company_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
    $buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
    $location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
    
    $search_string=trim($data[0]);
    $search_by =$data[1];
    $company_id =$data[2];
    $within_group=$data[3];
    $cbo_year=$data[4];
    
        
    
    
    if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
    else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
    else $year_field="";//defined Later

    if($db_type==0)
    {
        if($cbo_year==0) $year_cond=""; else $year_cond="and year(a.insert_date)='$cbo_year'";
    }
    else if($db_type==2)
    {
        if($cbo_year==0) $year_cond=""; else $year_cond="and to_char(a.insert_date,'YYYY')='$cbo_year'";
    }
    else $year_cond="";

    if($within_group==0) $within_group_cond=""; else $within_group_cond=" and a.within_group=$within_group";
    if ($within_group==1) // Yes
    {
        $search_field_cond='';
        if($search_string!="")
        {
            if($search_by==1)
            {
                $search_field_cond=" and a.job_no_prefix_num='$search_string'";
            }
            else if($search_by==2)
            {
                $search_field_cond=" and b.booking_no_prefix_num='$search_string'";
            }
            else
            {
                $search_field_cond=" and a.style_ref_no ='$search_string'";
            }
        }
        $sql = "SELECT a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.po_buyer, a.style_ref_no, a.location_id 
        from fabric_sales_order_mst a, wo_booking_mst b where a.booking_id=b.id  and a.sales_booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $within_group_cond $search_field_cond $year_cond 
        union all 
        SELECT a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.po_buyer, a.style_ref_no, a.location_id 
        from fabric_sales_order_mst a, wo_non_ord_samp_booking_mst b where a.booking_id=b.id  and a.sales_booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $within_group_cond $search_field_cond $year_cond order by id";
    }
    else // No
    {
        $search_field_cond='';
        if($search_string!="")
        {
            if($search_by==1)
            {
                $search_field_cond=" and a.job_no_prefix_num='$search_string'";
            }
            else if($search_by==2)
            {
                $search_field_cond=" and a.sales_booking_no='$search_string'";
            }
            else
            {
                $search_field_cond=" and a.style_ref_no ='$search_string'";
            }
        }
        $sql = "SELECT a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.po_buyer, a.style_ref_no, a.location_id from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $within_group_cond $search_field_cond $year_cond order by id";
    }
    
    // $sql = "SELECT id, $year_field, job_no_prefix_num, job_no, within_group, sales_booking_no, booking_date, buyer_id, po_buyer, style_ref_no, location_id from fabric_sales_order_mst where status_active=1 and is_deleted=0 and company_id=$company_id $within_group_cond $search_field_cond $year_cond order by id"; 
    // echo $sql;//die;
    $result = sql_select($sql);
    ?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="110">Sales Order No</th>
            <th width="40">Year</th>
            <th width="80">Within Group</th>
            <th width="70">Buyer</th>               
            <th width="120">Sales/ Booking No</th>
            <th width="80">Booking date</th>
            <th width="110">Style Ref.</th>
            <th>Location</th>
        </thead>
    </table>
    <div style="width:800px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">    
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                     
                if($row[csf('within_group')]==1)
                    $buyer=$buyer_arr[$row[csf('po_buyer')]]; 
                else
                    $buyer=$buyer_arr[$row[csf('buyer_id')]];
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]; ?>','<? echo $row[csf('job_no')]; ?>','<? echo $row[csf('sales_booking_no')]; ?>');"> 
                    <td width="40"><? echo $i; ?></td>
                    <td width="110"><p>&nbsp;<? echo $row[csf('job_no')]; ?></p></td>
                    <td width="40" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="80"><p><? echo $yes_no[$row[csf('within_group')]]; ?>&nbsp;</p></td>
                    <td width="70"><p><? echo $buyer; ?>&nbsp;</p></td>
                    <td width="120"><p><? echo $row[csf('sales_booking_no')]; ?></p></td>               
                    <td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
                    <td width="110"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                    <td><p><? echo $location_arr[$row[csf('location_id')]]; ?></p></td>
                </tr>
                <?
                $i++;
            }
            ?>
        </table>
    </div>
    <?  
    exit(); 
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company = str_replace("'","",$cbo_company_name);
	$within_group = str_replace("'","",$cbo_within_group);
	$buyer = str_replace("'","",$cbo_buyer_name);
	$booking_no = str_replace("'","",$txt_booking_no);
	$fso_no = str_replace("'","",$txt_job_no);
    $fso_id = str_replace("'","",$txt_job_hidden_id);
	$cbo_date_search_type = str_replace("'","",$cbo_date_search_type);
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);

	if ($within_group==0) $within_group_cond=""; else $within_group_cond="  and a.within_group=$within_group";
	if ($within_group==1) 
	{
		if ($buyer==0) $buyer_cond=""; else $buyer_cond="  and a.buyer_id='$buyer'";
	}
	else
	{
		if ($buyer==0) $buyer_cond=""; else $buyer_cond="  and a.po_buyer='$buyer'";
	}

	if ($booking_no=="") $booking_no_cond=""; else $booking_no_cond="  and a.sales_booking_no='$booking_no'";
	if ($fso_no=="") $fso_no_cond=""; else $fso_no_cond=" and a.job_no='$fso_no'";
	if ($fso_id=="") $fsoid_cond=""; else $fsoid_cond=" and a.id=$fso_id";

    $con = connect();
    execute_query("delete from tmp_booking_id where userid=$user_name");
    oci_commit($con);

	if($txt_date_from && $txt_date_to)
	{
		$date_from=change_date_format($txt_date_from,'','',1);
		$date_to=change_date_format($txt_date_to,'','',1);
        if ($cbo_date_search_type==1) // delivery_date
        {
            $delivery_date_cond="and a.delivery_date between '$date_from' and '$date_to'";
        }
        else if ($cbo_date_search_type==2) // booking date
        {
            $booking_date_cond="and a.booking_date between '$date_from' and '$date_to'";

            $booking_id_sql="SELECT a.id, a.booking_no
            FROM wo_booking_mst a, fabric_sales_order_mst b
            WHERE a.id=b.booking_id and a.supplier_id=$company and a.item_category=2 and a.pay_mode=5 and a.fabric_source in (1,2,4) $booking_date_cond and b.booking_without_order=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
            union all
            select a.id, a.booking_no
            from wo_non_ord_samp_booking_mst a, fabric_sales_order_mst b
            where a.id=b.booking_id and a.pay_mode=5 and a.fabric_source in (1,2,4) and a.supplier_id=$company and a.item_category=2  $booking_date_cond and b.booking_without_order=1 and a.status_active=1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted=0";
            // echo $booking_id_sql;die;
            $booking_id_sql_result=sql_select($booking_id_sql);

            foreach($booking_id_sql_result as $row)
            {
                if ($booking_id_check[$row[csf("id")]]=="") 
                {
                    $booking_id_check[$row[csf('id')]]=$row[csf('id')];
                    $booking_id = $row[csf('id')];
                    execute_query("insert into tmp_booking_id (userid, booking_id) values ($user_name,$booking_id)");
                }            
            }
            oci_commit($con);
        }
		// $delivery_date_cond="and  case when a.within_group in (1) then  a.delivery_date else a.booking_date  end between  '$date_from' and '$date_to' ";
	}

    $buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$party_name_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$season_arr = return_library_array("select id,season_name from lib_buyer_season", 'id', 'season_name');
	$color_library = return_library_array("select id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name");
	$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count", "id", "yarn_count"  );
	//composition and constructtion
	$composition_arr=array();
	$constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$deter_array=sql_select($sql_deter);
	foreach( $deter_array as $row) 
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}

    if ($cbo_date_search_type==2 && $txt_date_from!="" && $txt_date_to!="") // booking date
    {
        $sql="SELECT A.ID, A.BOOKING_ID, A.SALES_BOOKING_NO, A.REVISE_NO, A.JOB_NO AS FSO_NO, A.BUYER_ID, A.PO_BUYER, A.PO_JOB_NO, A.COMPANY_ID, A.SEASON, A.SEASON_ID, A.STYLE_REF_NO, A.BOOKING_TYPE, A.WITHIN_GROUP, A.BOOKING_DATE, A.DELIVERY_DATE, A.BOOKING_WITHOUT_ORDER, B.COLOR_TYPE_ID, B.DETERMINATION_ID, B.COLOR_ID, B.GSM_WEIGHT, B.DIA, B.BODY_PART_ID, B.FINISH_QTY, B.PROCESS_LOSS, B.GREY_QTY, B.CONS_UOM, B.GREY_QNTY_BY_UOM
        FROM FABRIC_SALES_ORDER_MST A, FABRIC_SALES_ORDER_DTLS B, TMP_BOOKING_ID C
        where a.id=b.mst_id and a.booking_id=c.booking_id and c.userid=$user_name and a.company_id=$company $within_group_cond $delivery_date_cond $fsoid_cond $booking_no_cond $buyer_cond and a.entry_form=109 and a.status_active=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
    }
    else
    {
        $sql="SELECT A.ID, A.BOOKING_ID, A.SALES_BOOKING_NO, A.REVISE_NO, A.JOB_NO AS FSO_NO, A.BUYER_ID, A.PO_BUYER, A.PO_JOB_NO, A.COMPANY_ID, A.SEASON, A.SEASON_ID, A.STYLE_REF_NO, A.BOOKING_TYPE, A.WITHIN_GROUP, A.BOOKING_DATE, A.DELIVERY_DATE, A.BOOKING_WITHOUT_ORDER, B.COLOR_TYPE_ID, B.DETERMINATION_ID, B.COLOR_ID, B.GSM_WEIGHT, B.DIA, B.BODY_PART_ID, B.FINISH_QTY, B.PROCESS_LOSS, B.GREY_QTY, B.CONS_UOM, B.GREY_QNTY_BY_UOM
        FROM FABRIC_SALES_ORDER_MST A, FABRIC_SALES_ORDER_DTLS B
        where a.id=b.mst_id and a.company_id=$company $within_group_cond $delivery_date_cond $fsoid_cond $booking_no_cond $buyer_cond and a.entry_form=109 and a.status_active=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0"; // $booking_date_cond 
       
    }
	// echo $sql;
	$sql_result=sql_select($sql);
	if(empty($sql_result)) {echo "<b>Data Not Found</b>";die;}
	$data_arr=array();
	foreach($sql_result as $row)
	{
		if ($row["WITHIN_GROUP"]==1) { $buyer_id=$row["PO_BUYER"]; }
		else { $buyer_id=$row["BUYER_ID"]; }

		$str_ref=$row["COLOR_TYPE_ID"].'*'.$row["DETERMINATION_ID"].'*'.$row["COLOR_ID"].'*'.$row["BODY_PART_ID"];
		$data_arr[$row["FSO_NO"]][$str_ref]['FSO_ID']=$row["ID"];
		$data_arr[$row["FSO_NO"]][$str_ref]['BOOKING_NO']=$row["SALES_BOOKING_NO"];
		$data_arr[$row["FSO_NO"]][$str_ref]['REVISE_NO']=$row["REVISE_NO"];
		$data_arr[$row["FSO_NO"]][$str_ref]['BUYER_NAME']=$buyer_arr[$buyer_id];
		$data_arr[$row["FSO_NO"]][$str_ref]['SEASON_NAME']=$season_arr[$row["SEASON_ID"]];
		$data_arr[$row["FSO_NO"]][$str_ref]['STYLE_REF_NO']=$row["STYLE_REF_NO"];
		$data_arr[$row["FSO_NO"]][$str_ref]['BOOKING_TYPE']=$row["BOOKING_TYPE"];
		$data_arr[$row["FSO_NO"]][$str_ref]['GSM']=$row["GSM_WEIGHT"];
		$data_arr[$row["FSO_NO"]][$str_ref]['DIA']=$row["DIA"];
		$data_arr[$row["FSO_NO"]][$str_ref]['WITHIN_GROUP']=$yes_no[$row["WITHIN_GROUP"]];
		$data_arr[$row["FSO_NO"]][$str_ref]['PARTY_NAME']=$company_library[$company];
		// $data_arr[$row["FSO_NO"]][$str_ref]['BODY_PART_NAME'].=$body_part[$row["BODY_PART_ID"]].',';
        $data_arr[$row["FSO_NO"]][$str_ref][$row["CONS_UOM"]]+=$row["GREY_QNTY_BY_UOM"];
		$data_arr[$row["FSO_NO"]][$str_ref]['FINISH_QTY']+=$row["FINISH_QTY"];
		$data_arr[$row["FSO_NO"]][$str_ref]['PROCESS_LOSS']+=$row["PROCESS_LOSS"];
		$data_arr[$row["FSO_NO"]][$str_ref]['GREY_QTY']+=$row["GREY_QTY"];
		$data_arr[$row["FSO_NO"]][$str_ref]['BOOKING_DATE']= change_date_format($row["BOOKING_DATE"]);
		$data_arr[$row["FSO_NO"]][$str_ref]['DELIVERY_DATE']= change_date_format($row["DELIVERY_DATE"]);

		$fso_id_arr[$row[csf("id")]]=$row[csf("id")];
	}
	// echo "<pre>";print_r($data_arr);die;

	$fso_nos = implode(",", $fso_id_arr);	
	$all_fso_id_cond="";$all_fso_id_cond2="";
	if($fso_nos)
	{
		$fso_nos = implode(",",array_filter(array_unique(explode(",", $fso_nos))));
		$fso_nos_arr = explode(",", $fso_nos);
		if($db_type==0)
		{
			$all_fso_id_cond = " and a.id in ($fso_nos )";
			$all_fso_id_cond2 = " and d.po_id in ($fso_nos )";
		}
		else
		{
			if(count($fso_nos_arr)>999)
			{
				$fso_nos_chunk_arr=array_chunk($fso_nos_arr, 999);
				$all_fso_id_cond=" and (";
				$all_fso_id_cond2=" and (";
				foreach ($fso_nos_chunk_arr as $value)
				{
					$all_fso_id_cond .="a.id in (".implode(",", $value).") or ";
					$all_fso_id_cond2 .="d.po_id in (".implode(",", $value).") or ";
				}
				$all_fso_id_cond=chop($all_fso_id_cond,"or ");
				$all_fso_id_cond2=chop($all_fso_id_cond2,"or ");
				$all_fso_id_cond.=")";
				$all_fso_id_cond2.=")";
			}
			else
			{
				$all_fso_id_cond = " and a.id in ($fso_nos )";
				$all_fso_id_cond2 = " and d.po_id in ($fso_nos )";
			}
		}
	}

	$job_fso_chk=array();$job_from_fso_arr=array();
	$booking_sql = "SELECT c.booking_no,c.booking_type,c.is_short, b.client_id, b.product_dept, b.product_code, b.job_no_prefix_num,b.job_no, a.job_no as fso_no, d.id, d.short_booking_type, d.pay_mode, d.supplier_id, d.booking_date, min(po_received_date) as po_received_date, min(pub_shipment_date) first_shipment_date, max(pub_shipment_date) last_shipment_date
	from fabric_sales_order_mst a, wo_booking_dtls c, wo_po_details_master b, wo_booking_mst d, wo_po_break_down e
	where a.sales_booking_no=c.booking_no and c.job_no=b.job_no and b.id=e.job_id and c.po_break_down_id=e.id
	and a.company_id=$company $all_fso_id_cond and a.within_group=1 and a.booking_id = d.id and c.booking_no = d.booking_no 
	group by c.booking_no,c.booking_type,c.is_short, b.job_no_prefix_num,b.job_no, a.job_no, d.id, d.short_booking_type, d.pay_mode, d.supplier_id, d.booking_date, b.client_id, b.product_dept, b.product_code
	union all 
	select b.booking_no,4 as booking_type,0 as is_short, 0 as client_id, 0 as product_dept, null as product_code, 0 as job_no_prefix_num,null as job_no, a.job_no as fso_no, b.id, null as short_booking_type, b.pay_mode, b.supplier_id, b.booking_date, null as po_received_date, null as first_shipment_date, null as last_shipment_date
	from fabric_sales_order_mst a, wo_non_ord_samp_booking_mst b 
	where a.within_group=1 and a.sales_booking_no=b.booking_no and a.company_id=$company $all_fso_id_cond
	group by b.booking_no, a.job_no, b.id,  b.pay_mode, b.supplier_id, b.booking_date";

	// echo $booking_sql;
	$booking_sql_result = sql_select($booking_sql);
	foreach ($booking_sql_result as $val)
	{
		if($job_fso_chk[$val[csf("fso_no")]][$val[csf("job_no")]] == "")
		{
			$job_fso_chk[$val[csf("fso_no")]][$val[csf("job_no")]] = $val[csf("job_no")];
			$job_from_fso_arr[$val[csf("fso_no")]]["job_no"] .= $val[csf("job_no_prefix_num")].",";

			$short_booking_type_arr[$val[csf("booking_no")]]=$short_booking_type[$val[csf("short_booking_type")]];
			if($val[csf("booking_type")]==1 && $val[csf("is_short")]==2)
			{
				$booking_type_arr[$val[csf("booking_no")]]="Main";
			}
			else if($val[csf("booking_type")]==1 && $val[csf("is_short")]==1)
			{
				$booking_type_arr[$val[csf("booking_no")]]="Short";
			}
			else if($val[csf("booking_type")]==4)
			{
				$booking_type_arr[$val[csf("booking_no")]]="Sample";
			}
			if ($val[csf("pay_mode")]==3 || $val[csf("pay_mode")]==5) 
			{
				$party_name_arr[$val[csf("booking_no")]]=$company_library[$val[csf('supplier_id')]];
			}
			else
			{
				$party_name_arr[$val[csf("booking_no")]]=$buyer_arr[$val[csf('supplier_id')]];
			}

            $client_id_arr[$val[csf("booking_no")]]=$party_name_arr[$val[csf('client_id')]];
			$source_arr[$val[csf("booking_no")]]=$pay_mode[$val[csf('pay_mode')]];
            $product_dept_arr[$val[csf("booking_no")]]=$product_dept[$val[csf('product_dept')]];
			$product_code_arr[$val[csf("booking_no")]]=$val[csf('product_code')];
			$po_received_date_arr[$val[csf("booking_no")]]=change_date_format($val[csf('po_received_date')]);
			$first_shipment_date_arr[$val[csf("booking_no")]]=change_date_format($val[csf('first_shipment_date')]);
			$last_shipment_date_arr[$val[csf("booking_no")]]=change_date_format($val[csf('last_shipment_date')]);
            $booking_date_arr[$val[csf("booking_no")]]=change_date_format($val[csf('booking_date')]);

            $bookin_id_arr[$val[csf("id")]]=$val[csf("id")];
		}
	}
	// echo "<pre>";print_r($party_name_arr);die;

    $booking_ids = implode(",", $bookin_id_arr);   
    $all_booking_ids_cond="";
    if($booking_ids)
    {
        $booking_ids = implode(",",array_filter(array_unique(explode(",", $booking_ids))));
        $booking_ids_arr = explode(",", $booking_ids);
        if($db_type==0)
        {
            $all_booking_ids_cond = " and a.id in ($booking_ids )";
        }
        else
        {
            if(count($booking_ids_arr)>999)
            {
                $booking_ids_chunk_arr=array_chunk($booking_ids_arr, 999);
                $all_booking_ids_cond=" and (";
                foreach ($booking_ids_chunk_arr as $value)
                {
                    $all_booking_ids_cond .="a.id in (".implode(",", $value).") or ";
                }
                $all_booking_ids_cond=chop($all_booking_ids_cond,"or ");
                $all_booking_ids_cond.=")";
            }
            else
            {
                $all_booking_ids_cond = " and a.id in ($booking_ids )";
            }
        }

        // $nameArray_approved = sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id $all_booking_ids_cond and b.entry_form=7");
        $nameArray_approved = sql_select("SELECT a.booking_no, max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id $all_booking_ids_cond and b.entry_form=7 group by a.booking_no
        union all
        select a.booking_no, max(b.approved_no) as approved_no from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id  $all_booking_ids_cond and b.entry_form=7 group by a.booking_no");
        foreach ($nameArray_approved as $val)
        {
            $booking_revised_arr[$val[csf("booking_no")]]=$val[csf('approved_no')];
        }
    }

	$program_sql="SELECT a.lot, a.yarn_count_id, b.knit_id, c.id as program_no, c.stitch_length, c.color_id, d.dia, d.po_id, d.determination_id, d.color_type_id
	from product_details_master a, ppl_yarn_requisition_entry b, ppl_planning_info_entry_dtls c, ppl_planning_entry_plan_dtls d
	where a.id=b.prod_id and b.knit_id=c.id and c.id=d.dtls_id and a.item_category_id=1 and a.company_id=6 and a.status_active=1 and a.is_deleted=0 $all_fso_id_cond2 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0";
	// echo $program_sql;
	$program_sql_result = sql_select($program_sql);
	foreach ($program_sql_result as $row)
	{
		$color_arr=array_filter(array_unique(explode(",", $row[csf("color_id")])));
		foreach ($color_arr as $key => $color_id) 
		{
			$program_data_arr[$row[csf("po_id")]][$row[csf("determination_id")]][$row[csf("color_type_id")]][$color_id]['yarn_count'].=$yarn_count_arr[$row[csf('yarn_count_id')]].',';
			$program_data_arr[$row[csf("po_id")]][$row[csf("determination_id")]][$row[csf("color_type_id")]][$color_id]['lot'].=$row[csf('lot')].',';
			$program_data_arr[$row[csf("po_id")]][$row[csf("determination_id")]][$row[csf("color_type_id")]][$color_id]['stitch_length'].=$row[csf('stitch_length')].',';
			$program_data_arr[$row[csf("po_id")]][$row[csf("determination_id")]][$row[csf("color_type_id")]][$color_id]['program_no'].=$row[csf('program_no')].',';
			$program_data_arr[$row[csf("po_id")]][$row[csf("determination_id")]][$row[csf("color_type_id")]][$color_id]['grey_dia'].=$row[csf('dia')].',';
		}
		/*$program_data_arr[$row[csf("po_id")]][$row[csf("determination_id")]][$row[csf("color_type_id")]]['yarn_count'].=$yarn_count_arr[$row[csf('yarn_count_id')]].',';
		$program_data_arr[$row[csf("po_id")]][$row[csf("determination_id")]][$row[csf("color_type_id")]]['lot'].=$row[csf('lot')].',';
		$program_data_arr[$row[csf("po_id")]][$row[csf("determination_id")]][$row[csf("color_type_id")]]['stitch_length'].=$row[csf('stitch_length')].',';
		$program_data_arr[$row[csf("po_id")]][$row[csf("determination_id")]][$row[csf("color_type_id")]]['program_no'].=$row[csf('program_no')].',';
		$program_data_arr[$row[csf("po_id")]][$row[csf("determination_id")]][$row[csf("color_type_id")]]['grey_dia'].=$row[csf('dia')].',';*/
	}
	// echo "<pre>";print_r($program_data_arr);die;
    execute_query("delete from tmp_booking_id where userid=$user_name");
    oci_commit($con);

	ob_start();
	?>
	<style type="text/css">
		.word_wrap_break {
			word-break: break-all;
			word-wrap: break-word;
		}
	</style>
    <div align="left">
        <fieldset style="width:3485px;">
        	<?
        	if(count($sql_result)>0)
        	{
        		?>
	            <div  align="center"> <strong> <? echo $company_library[$company]; ?> </strong>
	            <br><b>
	                <?

	                $date_head="";
	                if( $date_from)
	                {
	                	$date_head .= change_date_format($date_from).' To ';
	                }
	                if( $date_to)
	                {
	                	$date_head .= change_date_format($date_to);
	                }
	                echo $date_head;
	                ?> </b>
	            </div>
	        	<?
	        }
	        else
	        {
	        	echo "<b>Data Not Found</b>";die;
	        }
	        ?>
            <div align="left">
                <table class="rpt_table" width="4190" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                    <thead>
                        <tr>
                            <th width="30" class="word_wrap_break">SL</th>
                            <th width="120" class="word_wrap_break">Booking Number</th>
                            <th width="100" class="word_wrap_break">Booking Date</th>
                            <th width="100" class="word_wrap_break">Revised Number</th>
                            <th width="110" class="word_wrap_break">FSO No</th>
                            <th width="80" class="word_wrap_break">Buyer</th>
                            <th width="80" class="word_wrap_break">Prod. Dept.</th>
                            <th width="80" class="word_wrap_break">Season</th>
                            <th width="80" class="word_wrap_break">Style Ref</th>                            
                            <th width="50" class="word_wrap_break">Booking<br>Type</th>
                            <th width="50" class="word_wrap_break">Color<br>Type</th>
                            <th width="100" class="word_wrap_break">Fabrication</th>
                            <th width="150" class="word_wrap_break">Composition</th>
                            <th width="80" class="word_wrap_break">Fabric Color</th>

                            <th width="100" class="word_wrap_break">Count</th>
                            <th width="100" class="word_wrap_break">Yarn Lot</th>
                            <th width="100" class="word_wrap_break">Stitch</th>
                            <th width="100" class="word_wrap_break">Program No</th>
                            <th width="100" class="word_wrap_break">Grey Dia</th>

                            <th width="60" class="word_wrap_break">GSM</th>
                            <th width="80" class="word_wrap_break">Finish Dia</th>
                            <th width="60" class="word_wrap_break">Source</th>
                            <th width="150" class="word_wrap_break">Party Name</th>
                            <th width="100" class="word_wrap_break">Boday Part</th>
                            <th width="100" class="word_wrap_break">Finish Fab. Req.<br>(KG)</th>
                            <th width="100" class="word_wrap_break">Finish Fab. Req.<br>(Yds)</th>
                            <th width="100" class="word_wrap_break">Finish Req</th>
                            <th width="100" class="word_wrap_break">Process Loss</th>
                            <th width="100" class="word_wrap_break">Grey Req</th>

                            <th width="70" class="word_wrap_break">Inside Prod</th>
                            <th width="70" class="word_wrap_break">Outside Prod</th>
                            <th width="70" class="word_wrap_break">Transfer Qnty</th>
                            <th width="70" class="word_wrap_break">Transfer Out Qnty</th>
                            <th width="70" class="word_wrap_break">Total Prod</th>
                            <th width="70" class="word_wrap_break">No Need</th>
                            <th width="70" class="word_wrap_break">Inside Plan<br>Qnty</th>
                            <th width="70" class="word_wrap_break">Outside Plan<br>Qnty</th>
                            <th width="70" class="word_wrap_break">Inside Bal.<br>Qty</th>
                            <th width="70" class="word_wrap_break">Outside Bal.<br>Qty</th>
                            <th width="70" class="word_wrap_break">Total Bal.<br>Qty</th>
                            <th width="70" class="word_wrap_break">Stock in<br>Hand</th>

                            <th width="70" class="word_wrap_break">Order Sheet Rcv Date</th>
                            <th width="70" class="word_wrap_break">Po Received Date</th>
                            <th width="70" class="word_wrap_break">Knit Start<br>TNA</th>
                            <th width="70" class="word_wrap_break">Knit End<br>TNA</th>
                            <th width="70" class="word_wrap_break">Ac.Knit<br>Start</th>
                            <th width="70" class="word_wrap_break">Ac.Knit<br>End</th>
                            <th width="70" class="word_wrap_break">Delivery Date</th>
                            <th width="70" class="word_wrap_break">First<br>Shipment</th>
                            <th width="" class="word_wrap_break">Last<br>Shipment</th>
                        </tr>
                    </thead>
                </table>
                <div style=" max-height:350px; width:4210px; overflow-y:scroll;" id="scroll_body">
                    <table class="rpt_table" id="table_body" width="4190" cellpadding="0" cellspacing="0" border="1" rules="all">
                        <tbody>
                            <?
                            $i=1;
                            foreach($data_arr as $fso_no => $fso_no_value)
                            {
                            	foreach ($fso_no_value as $str_ref => $row) 
                            	{
	                    			$data=explode("*", $str_ref);
	                    			$color_type_id=$data[0];
	                    			$determination_id=$data[1];
                                    $color_name=$color_library[$data[2]];
	                    			$body_part_name=$body_part[$data[3]];

	                    			$yarn_count=$program_data_arr[$row['FSO_ID']][$determination_id][$color_type_id][$data[2]]['yarn_count'];
                                    $yarn_counts =implode(",",array_filter(array_unique(explode(",", $yarn_count))));

									$lot=$program_data_arr[$row['FSO_ID']][$determination_id][$color_type_id][$data[2]]['lot'];
									$lots =implode(",",array_filter(array_unique(explode(",", $lot))));

									$stitch_length=$program_data_arr[$row['FSO_ID']][$determination_id][$color_type_id][$data[2]]['stitch_length'];
									$stitch_lengths =implode(",",array_filter(array_unique(explode(",", $stitch_length))));

									$program_no=$program_data_arr[$row['FSO_ID']][$determination_id][$color_type_id][$data[2]]['program_no'];
									$program_nos =implode(",",array_filter(array_unique(explode(",", $program_no))));

									$grey_dia=$program_data_arr[$row['FSO_ID']][$determination_id][$color_type_id][$data[2]]['grey_dia'];
									$grey_dias =implode(",",array_filter(array_unique(explode(",", $grey_dia))));

                                    $buyer_and_client=$row["BUYER_NAME"];
                                    if ($client_id_arr[$row["BOOKING_NO"]]!="") 
                                    {
                                        $buyer_and_client.='-'.$client_id_arr[$row["BOOKING_NO"]];
                                    }
                                    $product_dept_and_code=$product_dept_arr[$row["BOOKING_NO"]];
                                    if ($product_code_arr[$row["BOOKING_NO"]]) 
                                    {
                                        $product_dept_and_code.='-'.$product_code_arr[$row["BOOKING_NO"]];
                                    }

									if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	                                ?>
	                                <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
	                                    <td class="word_wrap_break" width="30"><? echo $i; ?></td>
	                                    <td class="word_wrap_break" width="120"><? echo $row["BOOKING_NO"]; ?></td>
	                                    <td class="word_wrap_break" width="100"><? echo $booking_date_arr[$row["BOOKING_NO"]]; ?></td>
	                                    <td width="100" align="center"><p><? if ($booking_revised_arr[$row["BOOKING_NO"]]) {
                                            echo $booking_revised_arr[$row["BOOKING_NO"]]-1;
                                        }  ?></p></td>
	                                    <td class="word_wrap_break" width="110" title="<? echo $row['FSO_ID']; ?>"><? echo $fso_no; ?></td>
	                                    <td width="80"><p class="word_wrap_break"><? echo $buyer_and_client; ?></p></td>
	                                    
	                                    <td width="80"><p class="word_wrap_break"><? echo $product_dept_and_code; ?></p>
	                                    <td width="80"><p class="word_wrap_break"><? echo $row['SEASON_NAME']; ?></p>
	                                    <td width="80"><p class="word_wrap_break"><? echo $row['STYLE_REF_NO']; ?></p>		                                        
	                                    <td class="word_wrap_break" width="50"><p><? echo $booking_type_arr[$row["BOOKING_NO"]]; ?></p></td>
	                                    <td width="50"><p class="word_wrap_break"><? echo $color_type[$color_type_id]; ?></p>
	                                    </td>
	                                    <td width="100" align="center" title="<? echo $determination_id; ?>"><p class="word_wrap_break"><? echo $constructtion_arr[$determination_id]; ?></p></td>
	                                    <td width="150" align="center"><p class="word_wrap_break"><? echo $composition_arr[$determination_id]; ?></p></td>
	                                    <td width="80" align="center"><p class="word_wrap_break"><? echo $color_name; ?></p></td>

	                                    <td width="100" title="Count"><p><? echo $yarn_counts; ?></p></td>
	                                    <td width="100"><p><? echo $lots; ?></p></td>
	                                    <td width="100"><p><? echo $stitch_lengths; ?></p></td>
	                                    <td width="100"><p><? echo $program_nos; ?></p></td>
	                                    <td width="100" title="Grey Dia"><p><? echo $grey_dias; ?></p></td>

	                                    <td class="word_wrap_break" width="60" align="center"><p><? echo $row['GSM']; ?></p></td>
	                                    <td width="80"><p class="word_wrap_break"><? echo $row['DIA']; ?></p></td>
	                                    <td class="word_wrap_break" width="60"><p><? echo $source_arr[$row["BOOKING_NO"]]; ?></p></td>
	                                    <td class="word_wrap_break" width="150"><p><? echo $party_name_arr[$row["BOOKING_NO"]]; ?></p></td>
	                                    <td class="word_wrap_break" width="100"><p><? echo $body_part_name; ?></p></td>

	                                    <td width="100" align="right"><p><? echo number_format($row[12],2,'.',''); ?></p></td>
	                                    <td width="100" align="right"><p><? echo number_format($row[27],2,'.',''); ?></p></td>

	                                    <td width="100" align="right"><p class="word_wrap_break"><? echo number_format($row['FINISH_QTY'],2,'.',''); ?></p></td>
	                                    <td width="100" align="right"><p class="word_wrap_break"><? echo number_format($row['PROCESS_LOSS'],2,'.',''); ?></p></td>
	                                    <td width="100" align="right"><p class="word_wrap_break"><? echo number_format($row['GREY_QTY'],2,'.',''); ?></p></td>

	                                    <td width="70" title="Inside Prod"></td>
	                                    <td width="70"></td>
	                                    <td width="70"></td>
	                                    <td width="70"></td>
	                                    <td width="70"></td>
	                                    <td width="70"></td>
	                                    <td width="70"></td>
	                                    <td width="70"></td>
	                                    <td width="70"></td>
	                                    <td width="70"></td>
	                                    <td width="70"></td>
	                                    <td width="70" title="Stock in Hand"></td>

	                                    <td width="70"><? echo $booking_date_arr[$row["BOOKING_NO"]]; ?></td>
	                                    <td width="70"><? echo $po_received_date_arr[$row["BOOKING_NO"]]; ?></td>

	                                    <td width="70"></td>
	                                    <td width="70"></td>
	                                    <td width="70"></td>
	                                    <td width="70"></td>

	                                    <td width="70"><? echo $row['DELIVERY_DATE']; ?></td>
	                                    <td width="70"><? echo $first_shipment_date_arr[$row["BOOKING_NO"]]; ?></td>
	                                    <td width=""><? echo $last_shipment_date_arr[$row["BOOKING_NO"]]; ?></td>
	                                </tr>
	                                <?
	                                $i++;
                                    $tot_finish_fab_req_kg += $row[12];
                                    $tot_finish_fab_req_yds += $row[27];
	                                $tot_finish_qty += $row['FINISH_QTY'];
                                    $tot_grey_qty += $row['GREY_QTY'];
                            	}
		                    }
                            ?>
                        </tbody>
                    </table>
                </div>
                <table class="rpt_table" width="4190" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <tfoot>
                        <tr>
                        	<th width="30">&nbsp;</th>  
                            <th width="120">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="100" title="Revised Number">&nbsp;</th>
                            <th width="110">&nbsp;</th>
                            <th width="80" title="Buyer">&nbsp;</th>
                            <th width="80" title="Prod. Dept.">&nbsp;</th>
                            <th width="80" title="Season">&nbsp;</th>
                            <th width="80" title="Style Ref">&nbsp;</th>
                            <th width="50" title="booking_type">&nbsp;</th>
                            <th width="50" title="Color Type">&nbsp;</th>
                            <th width="100" title="Fabrication">&nbsp;</th>
                            <th width="150" title="Composition">&nbsp;</th>
                            <th width="80" title="Composition">&nbsp;</th>

                            <th width="100" title="Count">&nbsp;</th>
                            <th width="100" title="Lot">&nbsp;</th>
                            <th width="100" title="Stitch">&nbsp;</th>
                            <th width="100" title="Program">&nbsp;</th>
                            <th width="100" title="Grey Dia">&nbsp;</th>

                            <th width="60" title="GSM">&nbsp;</th>
                            <th width="80" title="Finish Dia">&nbsp;</th>
                            <th width="60" title="Source">&nbsp;</th>
                            <th width="150" title="Party Name">&nbsp;</th>
                            <th width="100" title="Boday Part"><strong>Total</strong></th>
                            <th width="100" title="Finish Fab. Req. (KG)"><? echo number_format($tot_finish_fab_req_kg,2,'.',''); ?></th>
                            <th width="100" title="Finish Fab. Req. (Yds)"><? echo number_format($tot_finish_fab_req_yds,2,'.',''); ?></th>
                            <th width="100" title="Finish Req"><? echo number_format($tot_finish_qty,2,'.',''); ?></th>
                            <th width="100" title="Process Loss">&nbsp;</th>
                            <th width="100" title="Grey Req"><? echo number_format($tot_grey_qty,2,'.',''); ?></th>

                            <th width="70" title="Inside Prod">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70" title="Stock in Hand">&nbsp;</th>

                            <th width="70" title="Order Sheet Rcv Date">&nbsp;</th>
                            <th width="70" title="Po Received Date">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70" title="Delivery Date">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="">&nbsp;</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </fieldset>
    </div>
	<?

    $html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
        @unlink($filename);
    }
    $name=time();
    $filename=$user_name."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$report_type";
    exit();
}

?>
<?
	
header('Content-type:text/html; charset=utf-8'); 
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header('location:login.php');
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$user_id=$_SESSION['logic_erp']["user_id"];
// die($user_id);
if ($action=="load_drop_down_location")
{   
    echo create_drop_down("cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );   
    exit();
}

if ($action=="load_drop_down_party")
{	
    $data=explode("_",$data);
    if($data[1]==1 && $data[0]!=0)
    {
        echo create_drop_down( "cbo_party_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name","id,company_name", 1, "-- Select Party --", 0, "load_drop_down('requires/yd_bill_issue_controller', this.value,'load_drop_down_party_location', 'party_location_td' );");
    }
    elseif($data[1]==2 && $data[0]!=0)
    {
		echo create_drop_down( "cbo_party_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",0, "" );
    }
    else
    {
    	echo create_drop_down('cbo_party_name', 120, $blank_array, '', 1, '-- Select Party --', $selected, "",1);
    }   
    exit();  
}

if ($action=="load_drop_down_party_location")
{
    echo create_drop_down("cbo_party_location", 150, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );   
    exit();
}

if ($action=="bill_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	$data=explode("_",$data);
	$company=$data[0];
	$location=$data[1];
	$party_name=$data[2];
	$within_group=$data[3];
	?>
	<script>
		function js_set_value(id,bill_no,order_no)
		{ 
			
			$("#update_id").val(id);
			$("#txt_bill_no").val(bill_no);
			$("#hidden_order_no").val(order_no);

			parent.emailwindow.hide();
		}
		
		function fnc_load_party_order_popup(company,party_name,within_group)
		{   	
			load_drop_down( 'yd_bill_issue_controller', company+'_'+within_group+'_'+party_name, 'load_drop_down_party', 'buyer_td' );
		}
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('Embl. Job No');
			else if(val==2) $('#search_by_td').html('W/O No');
			else if(val==3) $('#search_by_td').html('Buyer Job');
			// else if(val==4) $('#search_by_td').html('Buyer Po');
			else if(val==5) $('#search_by_td').html('Buyer Style');
		}
	</script>
	</head>
	<body onLoad="fnc_load_party_order_popup(<? echo $company;?>,<? echo $party_name;?>,<? echo $within_group;?>)">
        <div align="center" style="width:100%;" >
            <form name="searchreceivefrm_1"  id="searchreceivefrm_1" autocomplete="off">
                <table width="870" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                        <tr>
                            <th colspan="9"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                        </tr>
                    	<tr>                	 
                            <th width="140" class="must_entry_caption">Company Name</th>
                            <th width="50">Within Group</th>
                            <th width="120">Party Name</th>
                            <th width="70">Bill ID</th>
                            <th width="100">Search By</th>
                    		<th width="100" id="search_by_td">Yarn Job No</th>
                            <th width="100" colspan="2">Date Range</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                        </tr>         
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td> <input type="hidden" id="selected_job">  <!--  echo $data;-->
							<? 
								echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company, "load_drop_down( 'yd_bill_issue_controller', this.value+'_'+".$within_group.", 'load_drop_down_party', 'buyer_td' );"); ?>
                            </td>
                            <td>
							<?
								echo create_drop_down( "cbo_within_group", 50, $yes_no,"", 0, "--  --",$within_group, "load_drop_down( 'yd_bill_issue_controller',document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_party', 'buyer_td' );" ); ?>
							</td>
                            <td id="buyer_td">
								<? 
								echo create_drop_down( "cbo_party_name", 120, $blank_array,"", 1, "-- Select Party --", $selected, "" );?>
                            </td>
                            <td>
                                <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes_numeric" style="width:60px" placeholder="Bill ID" />
                            </td>
                            <td>
								<?
                                    $search_by_arr=array(1=>"Yarn Job No",2=>"W/O No",3=>"Buyer Job",5=>"Buyer Style");
                                    echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                                ?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                            </td>
                            <td>
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px" placeholder="From">
                            </td>
                            <td>
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px" placeholder="To">
                            </td> 
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_year_selection').value, 'create_bill_search_list_view', 'search_div', 'yd_bill_issue_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
                        </tr>
                        <tr>
                            <td colspan="9" align="center" valign="middle">
								<? echo load_month_buttons(1);  ?>
                                <input type="hidden" name="update_id" id="update_id" >
                                <input type="hidden" name="txt_bill_no" id="txt_bill_no" >
                                <input type="hidden" name="hidden_order_no" id="hidden_order_no" >
                            </td>
                        </tr>
                    </tbody>
                </table> 
                <div id="search_div"></div>   
            </form>
        </div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>$('#cbo_company_name').attr('disabled','disabled');</script>
	</html>
	<?
	exit();
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

if($action == "job_search_popup_job")
{
    echo load_html_head_contents('Search Yarn Dyeing Job', '../../../', 1, 0, $unicode);
	// print_r($_REQUEST); die;
    extract($_REQUEST);

    ?>
    <script>
        function search_by(val)
        {
            $('#txt_search_string').val('');
            if(val==1 || val==0) $('#search_by_td').html('YD Job No');
            else if(val==2) $('#search_by_td').html('W/O No');
            else if(val==3) $('#search_by_td').html('Buyer Style');
            else if(val==4) $('#search_by_td').html('Buyer Job');
        }

        var selected_id = new Array(); var jobNoArr = new Array;
        function toggle( x, origColor ) 
        {
            var newColor = 'yellow';
            if ( x.style ) {
            x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
            }
        }
        
        function check_all_data() 
        {
            $("#tbl_data_list tbody tr").each(function() 
            {
                var valTP=$(this).attr("id");
                

                if (typeof valTP != "undefined")
                {
                    var val = valTP.split("_");
                    var id = val[1];
                    var job_no =  $('#txt_buyer_id'+id).val()*1;

                    if(jobNoArr.length==0)
                    {
                        jobNoArr.push( job_no );
                    }
                    else if( jQuery.inArray( job_no, jobNoArr )==-1 &&  jobNoArr.length>0)
                    {
                        alert("Buyer Mixed Not Allowed");
                        return true;
                    }
                    toggle( document.getElementById( 'search_' +id ), '#FFFFCC' );

                    if( jQuery.inArray( $('#txt_individual_id_' +id).val(), selected_id ) == -1 ) 
                    {
                        selected_id.push( $('#txt_individual_id_' +id).val() );
                    }
                    else 
                    {
                        for( var i = 0; i < selected_id.length; i++ ) 
                        {
                            if( selected_id[i] == $('#txt_individual_id_' +id).val() ) break;
                        }
                        selected_id.splice( i, 1 );
                        jobNoArr.splice( i, 1 );
                    }

                    var id = '';
                    for( var i = 0; i < selected_id.length; i++ ) 
                    {
                        id += selected_id[i] + ',';
                    }
                    id = id.substr( 0, id.length - 1 );
                    $('#txt_selected_id').val( id );
                    document.getElementById('hidden_party_id').value=job_no;
                }
            });
        }

        function js_set_value(str)
        {
            splitArr = str.split('***'); 
            var delevery_id=splitArr[0]*1;
            var receive_dtls_id=splitArr[1]*1;
            var job_no=splitArr[2]*1;

            $("#tbl_data_list tbody tr").each(function() 
            {
                var valTP=$(this).attr("id");

                if (typeof valTP != "undefined")
                {
                    var val = valTP.split("_");
                    var id = val[1];
                    var delevery_id1 =  $('#txt_id'+id).val()*1;
                    if(delevery_id==delevery_id1)
                    {
        
                    if(jobNoArr.length==0)
                    {
                        jobNoArr.push( job_no );
                    }
                    else if( jQuery.inArray( job_no, jobNoArr )==-1 &&  jobNoArr.length>0)
                    {
                        alert("Buyer Mixed Not Allowed");
                        return true;
                    }
                    toggle( document.getElementById( 'search_' +id ), '#FFFFCC' );

                    if( jQuery.inArray( $('#txt_individual_id_' +id).val(), selected_id ) == -1 ) 
                    {
                        selected_id.push( $('#txt_individual_id_' +id).val() );
                    }
                    else 
                    {
                        for( var i = 0; i < selected_id.length; i++ ) 
                        {
                            if( selected_id[i] == $('#txt_individual_id_' +id).val() ) break;
                        }
                        selected_id.splice( i, 1 );
                        jobNoArr.splice( i, 1 );
                    }

                    var id = '';
                    for( var i = 0; i < selected_id.length; i++ ) 
                    {
                        id += selected_id[i] + ',';
                    }
                    id = id.substr( 0, id.length - 1 );
                    $('#txt_selected_id').val( id );
                    document.getElementById('hidden_party_id').value=job_no;
                }

                }
            });
        }
		function show_job()
		{
			
			if ( form_validation('cbo_company_name*cbo_within_group*cbo_party_name','Company Name*Within Group* Party Name')==false )
			{
				return;
			}
			show_list_view ( document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_pro_type').value+'_'+document.getElementById('cbo_order_type').value+'_'+document.getElementById('cbo_yd_type').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_receive_no').value, 'create_job_search_list_view', 'search_div', 'yd_bill_issue_controller', 'setFilterGrid(\'tbl_data_list\',-1)');
		}
            
    </script>
    </head>
    <body>
        <div align="center" style="width:100%;" >
            <form name="searchorderfrm_<?php echo $tblRow;?>" id="searchorderfrm_<?php echo $tblRow;?>" autocomplete="off">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" style="width: 100%;">
                    <thead>
                        <tr>
                            <th colspan="11"><?php echo create_drop_down('cbo_string_search_type', 163, $string_search_type, '', 1, '-- Searching Type --'); ?></th>
                        </tr>
                        <tr>
                            <th width="120" class="must_entry_caption" >Company Name</th>
                            <th width="80" class="must_entry_caption" >Within Group</th>
                            <th width="120" class="must_entry_caption">Party Name</th>
                            <th width="80">Delivery Id</th>
                            <th width="80">Search By</th>
                            <th width="80" id="search_by_td">YD Job No</th>
                            <th width="70">Prod. Type</th>
                            <th width="70">Order Type</th>
                            <th width="70">Y/D Type</th>
                            <th width="160">YD Delv. Date Range</th>
                            <th>
                                <input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width: 100%" />
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td>
                                <?php echo create_drop_down('cbo_company_name', 120, "select id, company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", 'id,company_name', 1, '-- Select Company --', $cbo_company_name, "load_drop_down( 'yd_delivery_entry_controller', this.value+'_'+document.getElementById('cbo_within_group').value, 'load_drop_down_party', 'party_td' );",1); ?>
                            </td>
                            <td> 
                                <?php echo create_drop_down('cbo_within_group', 80, $yes_no, '', 1, '-- Select Within Group --', $cbo_within_group, "load_drop_down( 'yd_delivery_entry_controller', document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_party', 'party_td' );",1); ?>
                            </td>
                            <td id="party_td"> 
                                <?php 

                                    if($cbo_within_group==1 && $cbo_company_name!=0)
                                    {

                                        echo create_drop_down( "cbo_party_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name","id,company_name", 1, "-- Select Party --", $cbo_party_name, "",1);
                                    }
                                    elseif($cbo_within_group==2 && $cbo_company_name!=0)
                                    {
                                        echo create_drop_down( "cbo_party_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_name' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",$cbo_party_name, "",1 );
                                    }
                                    else
                                    {
                                        echo create_drop_down('cbo_party_name', 120, $blank_array, '', 1, '-- Select Party --', $selected, "",1);
                                    }

                                ?>
                            </td>
                            <td> 
                                <input type="text" name="txt_receive_no" id="txt_receive_no" class="text_boxes" placeholder="Write Receive Id" />
                            </td>
                            <td>
                                <?
                                    $search_by_arr=array(1=>"YD Job No",2=>"W/O No",3=>"Buyer Style",4=>"Buyer Job");
                                    echo create_drop_down( "cbo_type",80, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                                ?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:80px" placeholder="Write" />
                            </td>
                            <td>
                                <? echo create_drop_down( "cbo_pro_type",70, $w_pro_type_arr,"",1, "--Select--",$cbo_pro_type,'',0 );?>
                            </td>
                            <td>
                                <? echo create_drop_down( "cbo_order_type",70, $w_order_type_arr,"",1, "--Select--",$cbo_order_type,'',0 ); ?>
                            </td>
                            <td>
                                <? echo create_drop_down( "cbo_yd_type",70, $yd_type_arr,"",1, "--Select--",$selected,'',0 ); ?>
                            </td>
                            <td>
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To">
                            </td>
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_job();" style="width:100%;" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="10" align="center" valign="middle">
                                <? echo load_month_buttons(1);  ?>
                                <input type="hidden" name="hidden_party_id" id="hidden_party_id" class="text_boxes" style="width:70px">
                                <input type="hidden" id="txt_selected_id" name="txt_selected_id" value="" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <div id="search_div" align="center">
            
        </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
}

if($action=="create_bill_search_list_view")
{
	// echo $data;
	$data=explode('_',$data);
	$search_type =$data[5];
	$within_group =$data[6];
	$search_by=str_replace("'","",$data[7]);
	$search_str=trim(str_replace("'","",$data[8]));

	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer_cond=" and a.party_id='$data[1]'"; else $buyer_cond="";
	if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $bill_date = "and a.bill_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $bill_date ="";

		$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[9]";
		$insert_year="YEAR(a.insert_date)";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $bill_date = "and a.bill_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $bill_date ="";

		$year_cond=" and to_char(a.insert_date,'YYYY')=$data[9]";
		$insert_year="to_char(a.insert_date,'YYYY')";
	}
	
	$search_job_cond=""; $style_cond=""; $po_cond=""; $search_wo_cond="";$buyer_job_cond="";$bill_id_cond="";
	
		if($search_type==1)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_job_cond="and c.job_no_prefix_num='$search_str'";
				else if($search_by==2) $search_wo_cond="and c.order_no='$search_str'";
				
				else if ($search_by==3) $buyer_job_cond=" and d.sales_order_no = '$search_str' ";
				else if ($search_by==4) $po_cond=" and d.po_number = '$search_str' ";
				else if ($search_by==5) $style_cond=" and d.style_ref = '$search_str' ";
			}
			if ($data[4]!='') $bill_id_cond=" and a.YD_BILL_NO='$data[4]'"; else $bill_id_cond="";
		}
		else if($search_type==4 || $search_type==0)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_job_cond="and c.job_no_prefix_num like '%$search_str%'";  
				else if($search_by==2) $search_wo_cond="and c.order_no like '%$search_str%'";  
				
				else if ($search_by==3) $buyer_job_cond=" and d.sales_order_no like '%$search_str%'";  
				else if ($search_by==4) $po_cond=" and d.po_number like '%$search_str%'"; 
				else if ($search_by==5) $style_cond=" and d.style_ref like '%$search_str%'";   
			}
			if ($data[4]!='') $bill_id_cond=" and a.YD_BILL_NO like '%$data[4]%'"; else $bill_id_cond="";
		}
		else if($search_type==2)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_job_cond="and c.job_no_prefix_num like '$search_str%'";  
				else if($search_by==2) $search_wo_cond="and c.order_no like '$search_str%'";  
				
				else if ($search_by==3) $buyer_job_cond=" and d.sales_order_no like '$search_str%'";  
				else if ($search_by==4) $po_cond=" and d.po_number like '$search_str%'";
				else if ($search_by==5) $style_cond=" and d.style_ref like '$search_str%'";  
			}
			if ($data[4]!='') $bill_id_cond=" and a.YD_BILL_NO like '$data[4]%'"; else $bill_id_cond="";
		}
		else if($search_type==3)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_job_cond="and c.job_no_prefix_num like '%$search_str'";  
				else if($search_by==2) $search_wo_cond="and c.order_no like '%$search_str'";  
				
				else if ($search_by==3) $buyer_job_cond=" and d.sales_order_no like '%$search_str'";  
				else if ($search_by==4) $po_cond=" and d.po_number like '%$search_str'";
				else if ($search_by==5) $style_cond=" and d.style_ref like '%$search_str'";  
			}
			if ($data[4]!='') $bill_id_cond=" and a.YD_BILL_NO like '%$data[4]'"; else $bill_id_cond="";
		}	
	
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array("select id, company_name from lib_company",'id','company_name');
	$order_arr=array();

	if($db_type==0)
	{
		$insert_date_cond="year(a.insert_date)";
	}
	else if($db_type==2)
	{
		$insert_date_cond="TO_CHAR(a.insert_date,'YYYY')";

	}
	
	
	$sql= "select a.id, a.YD_BILL_NO, a.BILL_NO_PREFIX_NUM, a.BILL_DATE ,$insert_date_cond as year, a.location_id, a.bill_date, a.party_id, a.party_location, a.PROD_TYPE, a.ORDER_TYPE, a.WITHIN_GROUP, a.YD_JOB_NO, a.remarks, b.SALES_ORDER_ID, b.BUYER_BUYER, b.YARN_TYPE_ID, b.COUNT_TYPE, a.currency_id, a.exchange_rate, c.order_no,d.style_ref,d.sales_order_no,c.yd_type,b.count_type 
	from YD_BILL_MST a, YD_BILL_DTLS b,yd_ord_mst c,yd_ord_dtls d 
	where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=698 and a.YD_JOB_NO=c.YD_JOB and c.id=d.mst_id $company $buyer_cond  $bill_date $withinGroup $search_job_cond  $style_cond $po_cond  $search_wo_cond $buyer_job_cond $bill_id_cond group by a.id, a.YD_BILL_NO, a.BILL_NO_PREFIX_NUM, a.BILL_DATE, a.insert_date, a.location_id, a.bill_date, a.party_id, a.party_location, a.PROD_TYPE, a.ORDER_TYPE, a.WITHIN_GROUP, a.YD_JOB_NO, a.remarks, b.SALES_ORDER_ID, b.BUYER_BUYER, b.YARN_TYPE_ID, b.COUNT_TYPE, a.currency_id, a.exchange_rate,c.order_no,d.style_ref,d.sales_order_no,c.yd_type,b.count_type ORDER BY a.id DESC";
	// echo $sql;die; 
	$result = sql_select($sql);
	?>
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1050" class="rpt_table">
            <thead>
                <th width="40">SL</th>
                <th width="120">Bill No</th>
                <th width="120">Party Name</th>
                <th width="70">Prod. Type</th>
                <th width="70">Within Group</th>
                <th width="80">Job No.</th>
                <th width="80">WO No.</th>
                <th width="80">Buyer Style</th>
                <th width="80">Buyer Job</th>
                <th width="70">Order Type</th>
                <th width="70">YD Type</th>
                <th width="70">Count Type</th>
                <th width="100">Bill Date</th>
            </thead>
     	</table>
     <div style="width:1050px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1050" class="rpt_table" id="tbl_po_list">
			<?
			$i=1;
            foreach($result as $row)
            {
                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick='js_set_value("<? echo $row[csf('id')]; ?>","<? echo $row[csf('YD_BILL_NO')]; ?>","<? echo $row[csf('YD_JOB_NO')]; ?>");' > 
						<td align="center" width="40"><? echo $i; ?></td>
						<td align="center" width="120"><? echo $row[csf("YD_BILL_NO")]; ?></td>
                        <td align="center" width="120"><? echo $row[csf("party_id")]; ?></td>
                        <td width="70"><? echo $row[csf("PROD_TYPE")]; ?></td>		
                        <td width="70"><? echo $row[csf("WITHIN_GROUP")]; ?></td>		
                        <td width="80"><? echo $row[csf("YD_JOB_NO")]; ?></td>		
                        <td width="80"><? echo $row[csf("order_no")]; ?></td>		
                        <td width="80"><? echo $row[csf("style_ref")]; ?></td>		
                        <td width="80"><? echo $row[csf("sales_order_no")]; ?></td>		
                        <td width="70"><? echo $row[csf("ORDER_TYPE")]; ?></td>		
                        <td width="70"><? echo $row[csf("yd_type")]; ?></td>		
                        <td width="70"><? echo $row[csf("count_type")]; ?></td>		
                        <td width="100"><? echo $row[csf("BILL_DATE")]; ?></td>
					</tr>
				<? 
				$i++;
            }
   		?>
			</table>
		</div>
     </div>
     <?	
	exit();
}

if($action=="create_job_search_list_view")
{	
	$data=explode('_',$data);

	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp_arr=return_library_array("select id, company_name from lib_company",'id','company_name');

	$search_type 			=trim(str_replace("'","",$data[0]));
	$cbo_company_name  		=trim(str_replace("'","",$data[1]));
	$cbo_within_group 		=trim(str_replace("'","",$data[2]));
	$cbo_party_name 		=trim(str_replace("'","",$data[3]));
	$search_by 				=trim(str_replace("'","",$data[4]));
	$search_str 			=trim(str_replace("'","",$data[5]));
	$cbo_pro_type 			=trim(str_replace("'","",$data[6]));
	$cbo_order_type 		=trim(str_replace("'","",$data[7]));
	$cbo_yd_type 			=trim(str_replace("'","",$data[8]));
	$txt_date_from 			=trim(str_replace("'","",$data[9]));
	$txt_date_to 			=trim(str_replace("'","",$data[10]));
	$cbo_year_selection 	=trim(str_replace("'","",$data[11]));
	$txt_receive_no 		=trim(str_replace("'","",$data[12]));
	
	
	// $nameArray= sql_select("select id, company_name, variable_list, service_process_id,yarn_dyeing_process from variable_setting_yarn_dyeing where company_name='$cbo_company_name' and variable_list=2 order by id");
	// $variable_list=$nameArray[0][csf('variable_list')];//2
	// $yarn_dyeing_process=$nameArray[0][csf('yarn_dyeing_process')];//1 
	// $service_process_id=$nameArray[0][csf('service_process_id')]; //1

	if($cbo_company_name==0)
	{
		echo "<p style='margin-top: 10px;'>Please Select Company Name first!!!</p>";
		die;
	}

	if($cbo_within_group==0)
	{
		echo "<p style='margin-top: 10px;'>Please Select Within Group first!!!</p>";
		die;
	}
	if($cbo_party_name==0)
	{
		echo "<p style='margin-top: 10px;'>Please Select Party Name first!!!</p>";
		die;
	}

	// if($cbo_order_type==0)
	// {
	// 	echo "<p style='margin-top: 10px;'>Please Select Order Type first!!!</p>";
	// 	die;
	// }

	$condition = "";
 
				
		if($cbo_company_name!=0)
		{
			$condition .= " and a.company_id=$cbo_company_name";
		}
	
		if($txt_receive_no!=0)
		{
			$condition .= " and a.receive_no_prefix_num=$txt_receive_no";
		}
	
		if($cbo_within_group!=0)
		{
			$condition .= " and a.within_group=$cbo_within_group";
		}
	
		if($cbo_party_name!=0)
		{
			$condition .= " and a.party_id=$cbo_party_name";
		}
	
		if($cbo_pro_type!=0)
		{
			$condition .= " and a.pro_type=$cbo_pro_type";
		}
	
		if($cbo_order_type!=0)
		{
			$condition .= " and a.order_type=$cbo_order_type";
		}
	
		if($cbo_yd_type!=0)
		{
			$condition .= " and c.yd_type=$cbo_yd_type";
		}
	 
		$date_con = '';
		if($db_type==0)
		{ 
			if ($txt_date_from!="" &&  $txt_date_to!="") $date_con = "and a.receive_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'"; else $date_con ="";
		}
		else
		{
			if ($txt_date_from!="" &&  $txt_date_to!="") $date_con = "and a.receive_date between '".change_date_format($txt_date_from, "", "",1)."' and '".change_date_format($txt_date_to, "", "",1)."'"; else $date_con ="";
		}
	
	
		if($search_type==1)
		{
			if($search_str!="")
			{
				if($search_by==1) $condition.="and b.job_no='$search_str'";
				else if($search_by==2) $condition.="and b.order_no='$search_str'";
				else if ($search_by==3) $condition.=" and b.style_ref = '$search_str' ";
				else if ($search_by==4) $condition.=" and b.sales_order_no = '$search_str' ";
			}
			
		}
		else if($search_type==2)
		{
			if($search_str!="")
			{
				if($search_by==1) $condition.="and b.job_no like '$search_str%'";
				else if($search_by==2) $condition.="and b.order_no like '$search_str%'";
				else if ($search_by==3) $condition.=" and b.style_ref like  '$search_str%' ";
				else if ($search_by==4) $condition.=" and b.sales_order_no like  '$search_str%' ";
			}
			
		}
		else if($search_type==3)
		{
			if($search_str!="")
			{
				if($search_by==1) $condition.="and b.job_no like '%$search_str'";
				else if($search_by==2) $condition.="and b.order_no like '%$search_str'";
				else if ($search_by==3) $condition.=" and b.style_ref like  '%$search_str' ";
				else if ($search_by==4) $condition.=" and b.sales_order_no like  '%$search_str' ";
			}
			
		}
		else if($search_type==4 || $search_type==0)
		{
			if($search_str!="")
			{
				if($search_by==1) $condition.="and b.job_no like '%$search_str%'";
				else if($search_by==2) $condition.="and b.order_no like '%$search_str%'";
				else if ($search_by==3) $condition.=" and b.style_ref like  '%$search_str%' ";
				else if ($search_by==4) $condition.=" and b.sales_order_no like  '%$search_str%' ";
			}
			
		}   
				

		if($db_type==0)
		{ 
			$ins_year_cond="year(a.insert_date)";
		}
		else
		{
			$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')"; 
		}
		    
			// $sql= "select a.yd_receive, a.id, b.id as receive_dtls_id, b.style_ref, a.party_id, a.pro_type, a.within_group, a.job_no, a.order_no, a.order_id, a.order_type, a.receive_date, b.count_type, b.sales_order_no 
			// from yd_store_receive_mst a, yd_store_receive_dtls b, yd_ord_mst c 
			// where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.yd_job=a.job_no and a.entry_form=640 $condition $date_con group by a.yd_receive, a.id, b.id, b.style_ref, a.party_id, a.pro_type, a.within_group, a.job_no, a.order_no, a.order_id, a.order_type, a.receive_date, b.count_type, b.sales_order_no order by a.id desc";
			$sql = "select a.yd_receive, a.id, b.id as receive_dtls_id, b.style_ref, a.party_id, b.pro_type, a.within_group, b.job_no, b.order_no, a.order_id, b.order_type, a.receive_date, b.count_type, b.sales_order_no from yd_store_receive_mst a, yd_store_receive_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=640 $condition $date_con group by a.yd_receive, a.id, b.id, b.style_ref, a.party_id, b.pro_type, a.within_group, b.job_no, b.order_no, a.order_id, b.order_type, a.receive_date, b.count_type, b.sales_order_no order by a.id desc";
		
// echo $sql; die;
	$result = sql_select($sql);
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="995" >
		<thead>
            <th width="30">SL</th>
            <th width="100">Party Name</th>
            <th width="60">Prod. Type</th>
            <th width="80">Within Group</th>
            <th width="100">Delivery No</th>
            <th width="100">Job No</th>
            <th width="100">WO No</th>
            <th width="80">Buyer Style</th>
            <th width="80">Buyer Job</th>
            <th width="80">Order Type</th>
            <th width="80">Count Type</th>
            <th >Delivery Date</th>
        </thead>
	</table>
	<div style="width:996px; max-height:300px;overflow-y:scroll;" >
		<table class="rpt_table" border="1" id="tbl_data_list" cellpadding="0" cellspacing="0" rules="all" width="995" >
			<tbody>
				<?php
					$i=1;
					$count_type_arr = array(1 => "Single",2 => "Double");
					foreach($result as $data)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						if($data[csf('within_group')]==1)
						{
							$party_name = $comp_arr[$data[csf('party_id')]];

						}
						else
						{
							$party_name = $party_arr[$data[csf('party_id')]];
						}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $data[csf('id')].'***'.$data[csf('receive_dtls_id')].'***'.$data[csf('party_id')]; ?>")' style="cursor:pointer" id="search_<? echo $data[csf('receive_dtls_id')];?>">
					<td align="center" width="30"><? echo $i; ?></td>
		            <td align="center" width="100"><? echo $party_name; ?></td>
		            <td align="center" width="60"><? echo $w_pro_type_arr[$data[csf('pro_type')]]; ?></td>
		            <td align="center" width="80"><? echo $yes_no[$data[csf('within_group')]]; ?></td>
		            <td align="center" width="100"><? echo $data[csf('yd_receive')]; ?></td>
		            <td align="center" width="100"><? echo $data[csf('job_no')]; ?></td>
		            <td align="center" width="100"><? echo $data[csf('order_no')]; ?></td>
		            <td align="center" width="80"><? echo $data[csf('style_ref')]; ?></td>
		            <td align="center" width="80"><? echo $data[csf('sales_order_no')]; ?></td>
		            <td align="center" width="80"><? echo $w_order_type_arr[$data[csf('order_type')]]; ?></td>
		            <td align="center" width="80"><? echo $count_type_arr[$data[csf('count_type')]]; ?></td>
		            <td align="center" >
		            	<? echo $data[csf('receive_date')]; ?>
		            	<input type="hidden" name="txt_individual_id" id="txt_individual_id_<?php echo $data[csf('receive_dtls_id')]; ?>" value="<? echo $data[csf('receive_dtls_id')]; ?>"/>
		            	<input type="hidden" name="txt_id" id="txt_id<? echo $data[csf('receive_dtls_id')];?>" value="<? echo $data[csf('id')]; ?>"/>
		            	<input type="hidden" name="txt_buyer_id" id="txt_buyer_id<? echo $data[csf('receive_dtls_id')];?>" value="<? echo $data[csf('party_id')]; ?>"/>
		            </td>
				</tr>
				<?php
					$i++;
					}
				?>
	        </tbody>
		</table>
		
	</div>
	<br>
	<table width="100%" cellspacing="0" cellpadding="0" style="border:none" align="center">
		<tr>
			<td align="center" height="30" valign="middle">
				<div style="width:100%">
				<div style="width:50%; float:left;padding-left:75px" align="left">
				<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
				</div>
				<div style="width:100%; float:left" align="center">
				<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
				</div>
				</div>
			</td>
		</tr>
	</table>
	<?

	exit();
}

if($action=="save_update_delete")
{
	// echo "<pre>"; print_r($_POST );die;
	$process = array( &$_POST );
    extract(check_magic_quote_gpc( $process )); 
    // $user_id=$_SESSION['logic_erp']['user_id'];

	$txt_bill_no   		= str_replace("'",'',$txt_bill_no);
	$cbo_company_name   = str_replace("'",'',$cbo_company_name);
	$cbo_location_name  = str_replace("'",'',$cbo_location_name);
	$cbo_within_group   = str_replace("'",'',$cbo_within_group);
	$cbo_party_name    	= str_replace("'",'',$cbo_party_name);
	$cbo_party_location = str_replace("'",'',$cbo_party_location);
	$txt_bill_date 		= str_replace("'",'',$txt_bill_date);
	$cbo_currency   	= str_replace("'",'',$cbo_currency);
	$exchange_rate   	= str_replace("'",'',$txt_exchange_rate);
	$cbo_pro_type 		= str_replace("'",'',$cbo_pro_type);
	$cbo_order_type    	= str_replace("'",'',$cbo_order_type);
	$update_id    		= str_replace("'",'',$update_id);
	$txt_yd_job_no   	= str_replace("'",'',$txt_yd_job_no);


	if ($operation==0) // Insert Start Here
    {
        $con = connect();
        if($db_type==0)
        {
            mysql_query("BEGIN");
        }

	    if($db_type==0){
            $txt_bill_date=change_date_format(str_replace("'",'',$txt_bill_date),'yyyy-mm-dd');
        }else{
            $txt_bill_date=change_date_format(str_replace("'",'',$txt_bill_date), "", "",1);
        }

        if($db_type==0) $insert_date_con="and YEAR(insert_date)=".date('Y',time())."";
	    else if($db_type==2) $insert_date_con="and TO_CHAR(insert_date,'YYYY')=".date('Y',time())."";

	    $pre_bill_arr =array();

	    if(str_replace("'","",$update_id)=="")
		{

			$mst_id=return_next_id("id","YD_BILL_MST",1);

			$txt_bill_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'YDBI', date("Y",time()), 5, "select bill_no_prefix,bill_no_prefix_num from YD_BILL_MST where entry_form=698 and company_id=$cbo_company_name $insert_date_con and status_active=1 and is_deleted=0 order by id desc ", "bill_no_prefix", "bill_no_prefix_num" ));

	    	$field_array="id, entry_form, YD_BILL_NO, BILL_NO_PREFIX, BILL_NO_PREFIX_NUM, BILL_DATE, company_id, location_id, within_group, party_id,party_location,YD_JOB_NO,CURRENCY_ID,EXCHANGE_RATE,PROD_TYPE,ORDER_TYPE,remarks,inserted_by, insert_date";

	    	$data_array="(".$mst_id.", 698, '".$txt_bill_no[0]."', '".$txt_bill_no[1]."', '".$txt_bill_no[2]."', '".$txt_bill_date."', '".$cbo_company_name."', '".$cbo_location_name."', '".$cbo_within_group."', '".$cbo_party_name."', '".$cbo_party_location."', '".$txt_yd_job_no."','".$cbo_currency."','".$exchange_rate."','".$cbo_pro_type."','".$cbo_order_type."','".$txt_remarks."',".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."')";

	    	$txt_bill_no=$txt_bill_no[0];
		}
		else
		{
			$mst_id = $update_id;
			// $txt_delivery_no=$txt_delivery_no;

			$pre_bill_sql = "select b.id, a.YD_BILL_NO,a.YD_JOB_NO,b.DELV_DTLS_ID, b.YD_JOB_DTLS_ID, b.delivery_id, b.BILL_QUANTITY, b.BILL_NO_MST from YD_BILL_MST a, YD_BILL_DTLS b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.YD_BILL_NO='$txt_bill_no' and a.id=$mst_id and a.entry_form=698";
			// echo $pre_bill_sql;
			$pre_bill_data = sql_select($pre_bill_sql);

			foreach($pre_bill_data as $data)
			{

				$pre_bill_arr[$data[csf('YD_BILL_NO')]][$data[csf('YD_JOB_NO')]][$data[csf('YD_JOB_DTLS_ID')]]['id'] = $data[csf('id')];
			}
		}
		// print_r($pre_bill_arr);
		$id=return_next_id( "id", "YD_BILL_DTLS",1);

	    $field_array2="id, mst_id, bill_no_mst, DELIVERY_ID, DELV_DTLS_ID, YD_JOB_ID, YD_JOB_DTLS_ID, DELIVERY_NO,DELIVERY_DATE,style_ref, sales_order_no, sales_order_id, buyer_buyer, lot, gray_lot, count_type, count_id, yarn_type_id, yarn_composition_id, yd_color_id, no_bag, cone_per_bag, uom, order_quantity, BILL_QUANTITY, RATE, AMOUNT, DOMESTIC_AMOUNT, REMARKS, wo_no, ORDER_ID, inserted_by, insert_date";

	    $field_array3="DELIVERY_ID*DELIVERY_NO*DELIVERY_DATE*style_ref*sales_order_no*sales_order_id*buyer_buyer*lot*gray_lot*count_type*count_id* yarn_type_id*yarn_composition_id*yd_color_id*no_bag*cone_per_bag*uom*order_quantity*BILL_QUANTITY*RATE* AMOUNT*DOMESTIC_AMOUNT*REMARKS*wo_no*ORDER_ID*updated_by*update_date";

	    $data_array2=""; $add_commaa=0;
	    for($i=1; $i<=$total_row; $i++)
	    {
	    	$ydJobId         		= "ydJobId_".$i;
	    	$ydJobDtlsId         	= "ydJobDtlsId_".$i;
	    	$deliveryId         	= "deliveryId_".$i;
	    	$deliveryDtlsId         = "deliveryDtlsId_".$i;
	    	$txtdeliveryNo          = "txtdeliveryno_".$i;
	    	$txtdeliveryDate        = "txtdeliveryDate_".$i;
	    	$txtstyleRef            = "txtstyleRef_".$i;
	        $txtsaleOrder           = "txtsaleOrder_".$i;
	        $txtsaleOrderID         = "txtsaleOrderID_".$i;
	        $buyerBuyer           	= "buyerBuyer_".$i;
	        $txtlot             	= "txtlot_".$i;
	        $txtGrayLot             = "txtGrayLot_".$i;
	        $txtcountTypeId       	= "txtcountTypeId_".$i;
	        $txtcountId         	= "txtcountId_".$i;
	        $cboYarnTypeId         	= "cboYarnTypeId_".$i;
	        $txtydCompositionId     = "txtydCompositionId_".$i;
	        $txtYarnColorId         = "txtYarnColorId_".$i;
	        $txtnoBag         		= "txtnoBag_".$i;
	        $txtConeBag         	= "txtConeBag_".$i;
	        $cboUomId         		= "cboUomId_".$i;
	        $txtOrderqty            = "txtOrderqty_".$i;
	        $txtbillqty         	= "txtbillqty_".$i;          
	        $txtrate           		= "txtrate_".$i;
	        $txtamount      		= "txtamount_".$i;
	        $txtdomesticamount      = "txtdomesticamount_".$i;
	        $txtremarks     		= "txtremarks_".$i;
	        $workOrderNo     		= "workOrderNo_".$i;
	        $ydOrderId     			= "ydOrderId_".$i;
	        
	        // $hidden_dtls_id = str_replace("'","",$$txtHiddenDtlsId);


	        if($pre_bill_arr!=array())
	        {
	        	if($pre_bill_arr[$txt_bill_no][$yd_job_Id][$job_DtlsId]['id']!='')
				{
	        		$cu_receive_qty = str_replace("'","",$$txtReceiveQty);
					$yd_job_Id=str_replace("'",'',$$ydJobId);
					// $delv_DtlsId=str_replace("'",'',$$deliveryDtlsId);
					$job_DtlsId=str_replace("'",'',$$ydJobDtlsId);
					
	        		$dtlsUpdateId = $pre_bill_arr[$txt_bill_no][$yd_job_Id][$job_DtlsId]['id'];
	        		
					// echo "20**This Job already Saved on this Bill No.";disconnect($con);die;
	        		$data_array3[$dtlsUpdateId]=explode("*",("".$$deliveryDtlsId."*".$$txtdeliveryNo."*".$$txtdeliveryDate."*".$$txtstyleRef."*".$$txtsaleOrder."*".$$txtsaleOrderID."*".$$buyerBuyer."*".$$txtlot."*".$$txtGrayLot."*".$$txtcountTypeId."*".$$txtcountId."*".$$cboYarnTypeId."*".$$txtydCompositionId."*".$$txtYarnColorId."*".$$txtnoBag."*".$$txtConeBag."*".$$cboUomId."*".$$txtOrderqty."*".$$txtbillqty."*".$$txtrate."*".$$txtamount."*".$$txtdomesticamount."*".$$txtremarks."*".$$workOrderNo."*".$$ydOrderId."*".$user_id."*'".$pc_date_time."'"));

                	$hdn_dtls_id_arr[]=str_replace("'",'',$dtlsUpdateId);

				}
				else
	        	{
	        		if ($add_commaa!=0) $data_array2 .=","; $add_comma=0;

					$data_array2 .="(".$id.",".$mst_id.",'".$txt_bill_no."',".$$deliveryId.",".$$deliveryDtlsId.",".$$ydJobId.",".$$ydJobDtlsId.",".$$txtdeliveryNo.",".$$txtdeliveryDate.",".$$txtstyleRef.",".$$txtsaleOrder.",".$$txtsaleOrderID.",".$$buyerBuyer.",".$$txtlot.",".$$txtGrayLot.",".$$txtcountTypeId.",".$$txtcountId.",".$$cboYarnTypeId.",".$$txtydCompositionId.",".$$txtYarnColorId.",".$$txtnoBag.",".$$txtConeBag.",".$$cboUomId.",".$$txtOrderqty.",".$$txtbillqty.",".$$txtrate.",".$$txtamount.",".$$txtdomesticamount.",".$$txtremarks.",".$$workOrderNo.",".$$ydOrderId.",'".$user_id."','".$pc_date_time."')";
					// echo $data_array2 ; die;
					$id=$id+1; $add_commaa++;
	        	} 	
	        }
	        
			else{
				
	        
		        if ($add_commaa!=0) $data_array2 .=","; $add_comma=0;

		        $data_array2 .="(".$id.",".$mst_id.",'".$txt_bill_no."',".$$deliveryId.",".$$deliveryDtlsId.",".$$ydJobId.",".$$ydJobDtlsId.",".$$txtdeliveryNo.",".$$txtdeliveryDate.",".$$txtstyleRef.",".$$txtsaleOrder.",".$$txtsaleOrderID.",".$$buyerBuyer.",".$$txtlot.",".$$txtGrayLot.",".$$txtcountTypeId.",".$$txtcountId.",".$$cboYarnTypeId.",".$$txtydCompositionId.",".$$txtYarnColorId.",".$$txtnoBag.",".$$txtConeBag.",".$$cboUomId.",".$$txtOrderqty.",".$$txtbillqty.",".$$txtrate.",".$$txtamount.",".$$txtdomesticamount.",".$$txtremarks.",".$$workOrderNo.",".$$ydOrderId.",'".$user_id."','".$pc_date_time."')";
				// echo $data_array2 ; die;
	           	$id=$id+1; $add_commaa++;
			}
		   
	    }

	    $flag=true;

	    if($data_array!='')
	    {
	    	//echo "10**INSERT INTO yd_store_receive_mst (".$field_array.") VALUES ".$data_array; die;
		    $rID=sql_insert("YD_BILL_MST",$field_array,$data_array,1);

		    if($rID==1) $flag=1; else $flag=0;
	    }

	    if($data_array3!=array() && $flag==1)
        {
			// echo "10**".bulk_update_sql_statement( "YD_BILL_DTLS", "id",$field_array3,$data_array3,$hdn_dtls_id_arr); die;
            $rID2=execute_query(bulk_update_sql_statement( "YD_BILL_DTLS", "id",$field_array3,$data_array3,$hdn_dtls_id_arr),1);
            if($rID2) $flag=1; else $flag=0;
        }

        if($flag==1 && $data_array2!=''){

        	// echo "10**INSERT INTO yd_store_receive_dtls (".$field_array2.") VALUES ".$data_array2; die;
            $rID3=sql_insert("YD_BILL_DTLS",$field_array2,$data_array2,1);
            if($rID3==1) $flag=1; else $flag=0;
        }
		// echo "Check Query: ".$rID."**".$rID2."***".$rID3; die;
        if($db_type==0){

	        if($flag==1){

	            mysql_query("COMMIT");  
	            echo "0**".str_replace("'",'',$txt_bill_no)."**".str_replace("'",'',$mst_id)."**".str_replace("'",'',$txt_yd_job_no);
	        }
	        else{

	            mysql_query("ROLLBACK"); 
	            echo "10**".str_replace("'",'',$txt_bill_no)."**".str_replace("'",'',$mst_id)."**".str_replace("'",'',$txt_yd_job_no);
	        }
		}
		else if($db_type==2){

		    if($flag==1){

		        oci_commit($con);
		        echo "0**".str_replace("'",'',$txt_bill_no)."**".str_replace("'",'',$mst_id)."**".str_replace("'",'',$txt_yd_job_no);
		    }else{
		        oci_rollback($con);
		        echo "10**".str_replace("'",'',$txt_bill_no)."**".str_replace("'",'',$mst_id)."**".str_replace("'",'',$txt_yd_job_no);
		    }
		}
        
        disconnect($con);
        die;
    }
	else if ($operation==1) // Update Start Here
    {
        $con = connect();
        if($db_type==0)
        {
            mysql_query("BEGIN");
        }

      

	    if($db_type==0){
            $txt_bill_date=change_date_format(str_replace("'",'',$txt_bill_date),'yyyy-mm-dd');
        }else{
            $txt_bill_date=change_date_format(str_replace("'",'',$txt_bill_date), "", "",1);
        }

        $field_array="YD_BILL_NO*BILL_DATE*company_id*location_id*within_group*party_id*party_location*YD_JOB_NO*CURRENCY_ID*EXCHANGE_RATE*order_type*PROD_TYPE*remarks*updated_by*update_date";

        $data_array="'".$txt_bill_no."'*'".$txt_bill_date."'*'".$cbo_company_name."'*'".$cbo_location_name."'*'".$cbo_within_group."'*'".$cbo_party_name."'*'".$cbo_party_location."'*'".$txt_yd_job_no."'*'".$cbo_currency."'*'".$exchange_rate."'*'".$cbo_order_type."'*'".$cbo_pro_type."'*'".$txt_remarks."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		

		$field_array2="DELIVERY_NO*DELIVERY_DATE*style_ref*sales_order_no*sales_order_id*buyer_buyer*lot*gray_lot*count_type*count_id* yarn_type_id*yarn_composition_id*yd_color_id*no_bag*cone_per_bag*uom*order_quantity*BILL_QUANTITY*RATE* AMOUNT*DOMESTIC_AMOUNT*REMARKS*updated_by*update_date";


		$field_array3="id, mst_id, bill_no_mst, DELIVERY_ID, DELV_DTLS_ID, YD_JOB_ID, YD_JOB_DTLS_ID, DELIVERY_NO,DELIVERY_DATE,style_ref, sales_order_no, sales_order_id, buyer_buyer, lot, gray_lot, count_type, count_id, yarn_type_id, yarn_composition_id, yd_color_id, no_bag, cone_per_bag, uom, order_quantity, BILL_QUANTITY, RATE, AMOUNT, DOMESTIC_AMOUNT, REMARKS, inserted_by, insert_date";


        $data_array2=array(); $add_commaa=0; $data_array3 ='';

        $id1=return_next_id( "id", "YD_BILL_DTLS",1);

        for($i=1; $i<=$total_row; $i++)
	    {

	    	// $orderDtlsId          	= "orderDtlsId_".$i;
	    	$deliveryDtlsId         = "deliveryDtlsId_".$i;
			$txtdeliveryNo          = "txtdeliveryNo_".$i;
	    	$txtdeliveryNo          = "txtdeliveryno_".$i;
	    	$txtdeliveryDate        = "txtdeliveryDate_".$i;
	    	$txtstyleRef            = "txtstyleRef_".$i;
	        $txtsaleOrder           = "txtsaleOrder_".$i;
	        $txtsaleOrderID         = "txtsaleOrderID_".$i;
	        $buyerBuyer           	= "buyerBuyer_".$i;
	        $txtlot             	= "txtlot_".$i;
	        $txtGrayLot             = "txtGrayLot_".$i;
	        $txtcountTypeId       	= "txtcountTypeId_".$i;
	        $txtcountId         	= "txtcountId_".$i;
	        $cboYarnTypeId         	= "cboYarnTypeId_".$i;
	        $txtydCompositionId     = "txtydCompositionId_".$i;
	        $txtYarnColorId         = "txtYarnColorId_".$i;
	        $txtnoBag         		= "txtnoBag_".$i;
	        $txtConeBag         	= "txtConeBag_".$i;
	        $cboUomId         		= "cboUomId_".$i;
	        $txtOrderqty            = "txtOrderqty_".$i;
	        $txtbillqty         	= "txtbillqty_".$i;          
	        $txtrate           		= "txtrate_".$i;
	        $txtamount      		= "txtamount_".$i;
	        $txtdomesticamount      = "txtdomesticamount_".$i;
	        $txtremarks     		= "txtremarks_".$i;
			$workOrderNo     		= "workOrderNo_".$i;
	        $ydOrderId     			= "ydOrderId_".$i;
	        $txtHiddenDtlsId     	= "txtHiddenDtlsId_".$i;

	        $dtlsUpdateId =str_replace("'",'',$$txtHiddenDtlsId);

	        if(str_replace("'",'',$$txtHiddenDtlsId)!="")
            {
            	
				$data_array2[$dtlsUpdateId]=explode("*",("".$$txtdeliveryNo."*".$$txtdeliveryDate."*".$$txtstyleRef."*".$$txtsaleOrder."*".$$txtsaleOrderID."*".$$buyerBuyer."*".$$txtlot."*".$$txtGrayLot."*".$$txtcountTypeId."*".$$txtcountId."*".$$cboYarnTypeId."*".$$txtydCompositionId."*".$$txtYarnColorId."*".$$txtnoBag."*".$$txtConeBag."*".$$cboUomId."*".$$txtOrderqty."*".$$txtbillqty."*".$$txtrate."*".$$txtamount."*".$$txtdomesticamount."*".$$txtremarks."*".$user_id."*'".$pc_date_time."'"));

                $hdn_dtls_id_arr[]=str_replace("'",'',$dtlsUpdateId);
            }
            else
        	{
        		if ($add_commaa!=0) $data_array3 .=","; $add_comma=0;

		      
				$data_array3 .="(".$id1.",".$id.",'".$txt_bill_no."',".$$deliveryId.",".$$deliveryDtlsId.",".$$ydJobId.",".$$ydJobDtlsId.",".$$txtdeliveryNo.",".$$txtdeliveryDate.",".$$txtstyleRef.",".$$txtsaleOrder.",".$$txtsaleOrderID.",".$$buyerBuyer.",".$$txtlot.",".$$txtGrayLot.",".$$txtcountTypeId.",".$$txtcountId.",".$$cboYarnTypeId.",".$$txtydCompositionId.",".$$txtYarnColorId.",".$$txtnoBag.",".$$txtConeBag.",".$$cboUomId.",".$$txtOrderqty.",".$$txtbillqty.",".$$txtrate.",".$$txtamount.",".$$txtdomesticamount.",".$$txtremarks.",".$user_id.",'".$pc_date_time."')";

	           	$id1=$id1+1; $add_commaa++;
        	}
	    }
	    $flag=true;
        $rID=sql_update("YD_BILL_MST",$field_array,$data_array,"id",$update_id,0);
        if($rID) $flag=1; else $flag=0;

        if($data_array2!="" && $flag==1)
        {
			// echo "10**".bulk_update_sql_statement( "YD_BILL_DTLS", "id",$field_array2,$data_array2,$hdn_dtls_id_arr); die;
            $rID2=execute_query(bulk_update_sql_statement( "YD_BILL_DTLS", "id",$field_array2,$data_array2,$hdn_dtls_id_arr),1);
            if($rID2) $flag=1; else $flag=0;
        }

        if($flag==1 && $data_array3!=''){

        	//echo "10**INSERT INTO YD_BILL_DTLS (".$field_array2.") VALUES ".$data_array2; die;
            $rID3=sql_insert("YD_BILL_DTLS",$field_array3,$data_array3,1);
            if($rID3==1) $flag=1; else $flag=0;
        }
		// echo $rID."**".$rID2."**".$rID3;die;	
        if($db_type==0){

	        if($flag==1){

	            mysql_query("COMMIT");  
	            echo "1**".str_replace("'",'',$txt_bill_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_yd_job_no);
	        }
	        else{

	            mysql_query("ROLLBACK"); 
	            echo "10**".str_replace("'",'',$txt_bill_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_yd_job_no);
	        }
		}
		else if($db_type==2){

		    if($flag==1){
		        oci_commit($con);
		        echo "1**".str_replace("'",'',$txt_bill_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_yd_job_no);
		    }else{
		        oci_rollback($con);
		        echo "10**".str_replace("'",'',$txt_bill_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_yd_job_no);
		    }
		}
        
        disconnect($con);
        die;

    }
	else if ($operation==2) // Update Start Here
    {
    	$con = connect();
        if($db_type==0)
        {
            mysql_query("BEGIN");  
        }


        $flag=0;
        $field_array="status_active*is_deleted*updated_by*update_date";
        $data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID=sql_update("yd_bill_mst",$field_array,$data_array,"id",$update_id,0); 
        if($rID) $flag=1; else $flag=0; 

        for($i=1; $i<=$total_row; $i++)
	    {

	        $txtHiddenDeliveryId    = "txtHiddenDtlsId_".$i;
	        $dtlsUpdateId =str_replace("'",'',$$txtHiddenDeliveryId);

	        if($flag==1)
	        {
	            $rID1=sql_update("YD_BILL_DTLS",$field_array,$data_array,"id",$dtlsUpdateId,1);
	            if($rID1) $flag=1; else $flag=0; 
	        }
	    }
		// echo $rID."**".$rID1;die;
        
        if($db_type==0)
        {
            if($flag==1)
            {
                mysql_query("COMMIT");  
                echo "2**".str_replace("'",'',$txt_bill_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_yd_job_no);
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
                echo "2**".str_replace("'",'',$txt_bill_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_yd_job_no);
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

if($action=="dtls_list_view")
{
	// print_r($data);
	$data=explode('_', $data);
	
	$party_id = $data[0];
	$receive_ids = $data[1];
	$cbo_company_name = $data[2];

	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp_arr=return_library_array("select id, company_name from lib_company",'id','company_name');
	
	if($db_type==0)
	{  
        $ins_year_cond="year(a.insert_date)";
    }
	else
	{
        
        $ins_year_cond="TO_CHAR(a.insert_date,'YYYY')"; 
    }
	$nameArray= sql_select("select id, company_name, variable_list, service_process_id,yarn_dyeing_process from variable_setting_yarn_dyeing where company_name='$cbo_company_name' and variable_list=2 order by id");
	$variable_list=$nameArray[0][csf('variable_list')];//2
	$yarn_dyeing_process=$nameArray[0][csf('yarn_dyeing_process')];//1 
	$service_process_id=$nameArray[0][csf('service_process_id')]; //1 

	$sql = "select a.id,a.yd_receive,a.RECEIVE_NO_PREFIX_NUM, a.party_id, b.job_no, b.order_type, b.pro_type, b.order_no from yd_store_receive_mst a, yd_store_receive_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.party_id='$party_id' and b.id in($receive_ids) and a.entry_form=640 group by a.id,a.yd_receive,a.RECEIVE_NO_PREFIX_NUM, a.party_id, b.job_no, b.order_type, b.pro_type, b.order_no";

	// echo $sql;
	$result = sql_select($sql);

	?>

	<table width="640" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
		<thead>
			<th width="35">Sl</th>
			<th width="150">Delivery No</th>
			<th width="150">Job No</th>
			<th width="100">WO No</th>
			<th width="100">Prod. Type</th>
			<th width="100">Order Type</th>
		</thead>
		<tbody id="tbl_list_view">
	<?php

	$tblRow = 1;
	foreach($result as $data)
	{
		if ($tblRow%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

		if($data[csf('within_group')]==1)
						{
			$party_name = $comp_arr[$data[csf('party_id')]];

		}
		else
		{
			$party_name = $party_arr[$data[csf('party_id')]];
		}
	?>
        <tr style="cursor:pointer" id="dtls_row_<?php echo $tblRow;?>" bgcolor="<? echo $bgcolor; ?>" onClick="load_receive_data('<?php echo $data[csf('job_no')];?>','<?php echo $data[csf('yd_receive')];?>',<?php echo $tblRow;?>)">
        	<td width="35" align="center"><?php echo $tblRow;?></td>
            <td width="150" align="center">
            	<?php echo $data[csf('RECEIVE_NO_PREFIX_NUM')];?>
            </td>
            <td width="150" align="center">
            	<?php echo $data[csf('job_no')];?>
            </td>
            <td width="100" align="center">
            	<?php echo $data[csf('order_no')];?>
            </td>
            <td width="100" align="center">
            	<?php echo $w_pro_type_arr[$data[csf('pro_type')]];?>
            </td>
            <td width="100" align="center">
            	<?php echo $w_order_type_arr[$data[csf('order_type')]];?>
            </td>
        </tr>
    <?php
    	$tblRow++;
	}
	?>
	</tbody>
	</table>
	<?
	exit();

}

if($action=="load_php_yd_job_data_to_form")
{
	$sql = "select a.id, a.company_id, a.location_id, a.within_group, a.party_id, a.party_location, a.order_no, a.pro_type, a.order_type from yd_ord_mst a where a.yd_job='$data' and a.status_active=1 and a.is_deleted=0 and a.check_box_confirm=1";

	$data_array = sql_select($sql);
    unset($sql);

    foreach($data_array as $data)
    {
    	// echo "document.getElementById('txt_wo_no').value = '".$data[csf('order_no')]."';\n";
    	echo "document.getElementById('cbo_pro_type').value = '".$data[csf('pro_type')]."';\n";
    	echo "document.getElementById('cbo_order_type').value = '".$data[csf('order_type')]."';\n";
    }
}

if($action=="receive_dtls_list_view")
{
	// print_r($data);die;
	$data=explode('_', $data);
	
	$job_no = $data[0];
	$receive_no = $data[1];
	$cbo_company_name = $data[2];

	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	
	if($db_type==0)
	{  
        $ins_year_cond="year(a.insert_date)";
    }
	else
	{
        
        $ins_year_cond="TO_CHAR(a.insert_date,'YYYY')"; 
    }
	$nameArray= sql_select("select id, company_name, variable_list, service_process_id,yarn_dyeing_process from variable_setting_yarn_dyeing where company_name='$cbo_company_name' and variable_list=2 order by id");
	$variable_list=$nameArray[0][csf('variable_list')];//2
	$yarn_dyeing_process=$nameArray[0][csf('yarn_dyeing_process')];//1 
	$service_process_id=$nameArray[0][csf('service_process_id')]; //1 
	
		$sql = "select a.id as delv_id, a.order_type,a.RECEIVE_NO_PREFIX_NUM,a.RECEIVE_DATE,a.YD_RECEIVE, b.id as delv_dtls_id, b.style_ref, b.sales_order_no, b.sales_order_id, b.buyer_buyer, b.lot, b.gray_lot, b.count_type, b.count_id, b.yarn_type_id, b.yarn_composition_id, b.yd_color_id, b.no_bag, b.cone_per_bag, b.uom, b.order_quantity, b.process_loss, b.adj_type, b.total_order_quantity, b.receive_qty, c.id as yd_job_id, c.yd_job, c.order_no as wo_no, c.order_id, d.rate,d.ID as yd_job_dtls_id
		from yd_store_receive_mst a, yd_store_receive_dtls b, yd_ord_mst c,yd_ord_dtls d 
		where a.id=b.mst_id and c.YD_JOB=b.job_no and c.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.yd_receive='$receive_no' and a.entry_form=640 and d.id=b.dtls_id group by a.id, a.order_type,a.RECEIVE_NO_PREFIX_NUM,a.RECEIVE_DATE,a.YD_RECEIVE, b.id , b.style_ref, b.sales_order_no, b.sales_order_id, b.buyer_buyer, b.lot, b.gray_lot, b.count_type, b.count_id, b.yarn_type_id, b.yarn_composition_id, b.yd_color_id, b.no_bag, b.cone_per_bag, b.uom, b.order_quantity, b.process_loss, b.adj_type, b.total_order_quantity, b.receive_qty, c.id, c.yd_job, c.order_no, c.order_id, d.rate, d.ID order by RECEIVE_NO_PREFIX_NUM ASC";
		// echo $sql;
		$result = sql_select($sql);
	$tblRow=1;
	foreach ($result as $data) {
		
	
	?>
        <tr id="row_<?php echo $tblRow;?>">
            <td align="center" width="80">
            	<input style="width:80px;text-align:center" readonly class="text_boxes" type="text" name="txtdeliveryNo[]" id="txtdeliveryNo_<?php echo $tblRow;?>" value="<?php echo $data[csf('receive_no_prefix_num')];?>">
            </td>
            <td align="center" width="80">
            	<input style="width:80px;text-align:center" readonly class="text_boxes" type="text" name="txtdeliveryDate[]" id="txtdeliveryDate_<?php echo $tblRow;?>" value="<?php echo $data[csf('receive_date')];?>">
            </td>
            <td align="center" width="80">
            	<input style="width:80px" readonly class="text_boxes" type="text" name="txtstyleRef[]" id="txtstyleRef_<?php echo $tblRow;?>" value="<?php echo $data[csf('style_ref')];?>" title="<?php echo $data[csf('style_ref')];?>" >
            </td>
            <td width="60">
				<input style="width:60px" readonly class="text_boxes" type="text" name="txtsaleOrder[]" id="txtsaleOrder_<?php echo $tblRow;?>" value="<?php echo $data[csf('sales_order_no')];?>">
            	<input  class="text_boxes_numeric" type="hidden" name="txtsaleOrderID[]" id="txtsaleOrderID_<?php echo $tblRow;?>" value="<?php echo $data[csf('sales_order_id')];?>">
            </td>
            <td width="60">
				<input style="width:60px" readonly class="text_boxes" type="text" name="buyerBuyer[]" id="buyerBuyer_<?php echo $tblRow;?>" value="<?php echo $data[csf('buyer_buyer')];?>">
            </td>
            <td width="80">
            	<input style="width:80px" readonly class="text_boxes" type="text" name="txtYdlot[]" id="txtYdlot_<?php echo $tblRow;?>" value="<?php echo $data[csf('lot')];?>">
            </td>
            <td width="80">
            	<input style="width:80px" <?php echo $readonly; ?> class="text_boxes" type="text" name="txtGrayLot[]" id="txtGrayLot_<?php echo $tblRow;?>" value="<?php echo $data[csf('gray_lot')];?>">
            	<input readonly class="text_boxes" type="hidden" name="txtHiddenGrayLot[]" id="txtHiddenGrayLot_<?php echo $tblRow;?>" value="<?php echo $data[csf('gray_lot')];?>">
            </td>
            <td width="60">
            	<input class="text_boxes" type="hidden" name="txtcountTypeId[]" id="txtcountTypeId_<?php echo $tblRow;?>" value="<?php echo $data[csf('count_type')];?>">
            	<?
                $count_type_arr = array(1 => "Single",2 => "Double");
                echo create_drop_down( "txtcountType_".$tblRow, 60, $count_type_arr,'', 1, '--- Select---', $data[csf('count_type')], "",1,'','','','','','',"txtcountType[]");
                ?>
            </td>
            <td width="60">
            	<input class="text_boxes" type="hidden" name="txtcountId[]" id="txtcountId_<?php echo $tblRow;?>" value="<?php echo $data[csf('count_id')];?>">
            	<?
                   if ($within_group==2) 
                   {
                    	
                    	$sql="select distinct(b.id) as id,b.yarn_count from lib_yarn_count b where b.status_active=1 and b.is_deleted=0";
                   }
                   else
                   {
						
						$sql="select distinct(b.id) as id,b.yarn_count from lib_yarn_count b where b.status_active=1 and b.is_deleted=0";
                   }

                	echo create_drop_down( "cboCount_".$tblRow, 60, $sql,"id,yarn_count", 1, "-- Select --",$data[csf('count_id')],"",1,'','','','','','',"cboCount[]"); 
                ?>
            </td>
            <td width="80">
            	<input class="text_boxes" type="hidden" name="cboYarnTypeId[]" id="cboYarnTypeId_<?php echo $tblRow;?>" value="<?php echo $data[csf('yarn_type_id')];?>">

            	<? echo create_drop_down( "cboYarnType_".$tblRow, 80, $yarn_type,"", 1, "-- Select --",$data[csf('yarn_type_id')],"",1,'','','','','','',"cboYarnType[]"); ?>
            </td>
            <td width="100">
            	<input class="text_boxes" type="hidden" name="txtydCompositionId[]" id="txtydCompositionId_<?php echo $tblRow;?>" value="<?php echo $data[csf('yarn_composition_id')];?>">
            	<? echo create_drop_down( "cboComposition_".$tblRow, 100, $composition,"", 1, "-- Select --",$data[csf('yarn_composition_id')],"",1,'','','','','','',"cboComposition[]"); ?>
            </td>
            <td width="80">
            	<input class="text_boxes" type="hidden" name="txtYarnColorId[]" id="txtYarnColorId_<?php echo $tblRow;?>" value="<?php echo $data[csf('yd_color_id')]; ?>">
            	<? echo create_drop_down( "txtYarnColor_".$tblRow, 80, $color_arr,"", 1, "-- Select --",$data[csf('yd_color_id')],"",1,'','','','','','',"txtYarnColor[]"); ?>
            </td>
            <td width="40">
            	<input style="width:40px" class="text_boxes_numeric" type="text" name="txtnoBag[]" id="txtnoBag_<?php echo $tblRow;?>" value="<?php echo $data[csf('no_bag')];?>">
            </td>
            <td width="50">
            	<input style="width:50px" class="text_boxes_numeric" type="text" name="txtConeBag[]" id="txtConeBag_<?php echo $tblRow;?>" value="<?php echo $data[csf('cone_per_bag')];?>">
            </td>
            <td width="50">
            	<input class="text_boxes" type="hidden" name="cboUomId[]" id="cboUomId_<?php echo $tblRow;?>" value="<?php echo $data[csf('uom')];?>">

            	<? echo create_drop_down( "cboUom_".$tblRow, 50, $unit_of_measurement,"", 1, "-- Select --",$data[csf('uom')],"", 1,'','','','','','',"cboUom[]"); ?>
            </td>
            <td width="50">
            	<input style="width:50px" readonly class="text_boxes_numeric" type="text" name="txtOrderqty[]" id="txtOrderqty_<?php echo $tblRow;?>" value="<?php echo $data[csf('order_quantity')];?>">
            	<input readonly class="text_boxes_numeric" type="hidden" name="txtHiddenOrderqty[]" id="txtHiddenOrderqty_<?php echo $tblRow;?>" value="<?php echo $data[csf('order_quantity')];?>">
            </td>

            <td width="50">
            	<input style="width:50px" class="text_boxes_numeric must_entry_caption" type="text" onKeyUp="validateBillQty(<?=$tblRow;?>);calculateAmount(<?=$tblRow;?>)" name="txtbillqty[]" id="txtbillqty_<?php echo $tblRow;?>" value="<?php echo $data[csf('receive_qty')];?>">
            </td>

            <td width="50">
            	<input style="width:50px" class="text_boxes_numeric must_entry_caption" type="text" name="txtrate[]" id="txtrate_<?php echo $tblRow;?>" onKeyUp="calculateAmount(<?=$tblRow;?>)" value="" placeholder="<?php echo number_format($data[csf('rate')],4);?>">
            </td>
            <td width="50">
            	<input style="width:50px" readonly class="text_boxes_numeric" type="text" name="txtamount[]" id="txtamount_<?php echo $tblRow;?>" value="<?php //echo number_format($balance,2);?>">
            	
            </td>
            <td width="50">
            	<input style="width:50px" readonly class="text_boxes_numeric" type="text" name="txtdomesticamount[]" id="txtdomesticamount_<?php echo $tblRow;?>" value="">
            </td>
            <td width="50">
            	<input style="width:50px" class="text_boxes" type="text" name="txtremarks[]" id="txtremarks_<?php echo $tblRow;?>" value="" placeholder="Remarks">
            	
            </td>
            <td width="50" style="display:none">
            	<input  readonly class="text_boxes" type="hidden" name="ydJobId[]" id="ydJobId_<?php echo $tblRow;?>" value="<?php echo $data[csf('yd_job_id')];?>">
            	<input  readonly class="text_boxes" type="hidden" name="ydJobDtlsId[]" id="ydJobDtlsId_<?php echo $tblRow;?>" value="<?php echo $data[csf('yd_job_dtls_id')];?>">
            	<input  readonly class="text_boxes" type="hidden" name="deliveryId[]" id="deliveryId_<?php echo $tblRow;?>" value="<?php echo $data[csf('delv_id')];?>">
            	<input  readonly class="text_boxes" type="hidden" name="deliveryDtlsId[]" id="deliveryDtlsId_<?php echo $tblRow;?>" value="<?php echo $data[csf('delv_dtls_id')];?>">
				<input  readonly class="text_boxes" type="hidden" name="ydOrderId[]" id="ydOrderId_<?php echo $tblRow;?>" value="<?php echo $data[csf('order_id')];?>">
            	<input  readonly class="text_boxes" type="hidden" name="workOrderNo[]" id="workOrderNo_<?php echo $tblRow;?>" value="<?php echo $data[csf('wo_no')];?>">
            </td>
        </tr>
    <?
    	$tblRow++;
	}
	exit();
}

if($action=="load_php_yd_bill_data_to_form")
{
	
	$data=explode('_', $data);
	$sql = "select a.id,a.YD_BILL_NO, a.company_id, a.location_id, a.within_group, a.party_id, a.party_location, a.bill_date,a.CURRENCY_ID,a.EXCHANGE_RATE,a.PROD_TYPE,a.ORDER_TYPE, a.remarks from yd_bill_mst a where a.id='$data[0]' and a.status_active=1 and a.is_deleted=0 and a.entry_form=698";
	// echo $sql; die;
	$data_array = sql_select($sql);
	// unset($sql);

	foreach($data_array as $data)
	{
		echo "document.getElementById('update_id').value = '".$data[csf('id')]."';\n";
		echo "document.getElementById('txt_bill_no').value = '".$data[csf('YD_BILL_NO')]."';\n";
		echo "document.getElementById('cbo_company_name').value = '".$data[csf('company_id')]."';\n";
		echo "document.getElementById('txt_bill_date').value = '".change_date_format($data[csf('bill_date')])."';\n";

		echo "load_drop_down( 'requires/yd_bill_issue_controller',".$data[csf('company_id')]."+'_'+1, 'load_drop_down_location', 'location_td' );\n";
		echo "document.getElementById('cbo_location_name').value = '".$data[csf('location_id')]."';\n";
		echo "$('#cbo_location_name').attr('disabled','disabled');\n";

		echo "document.getElementById('cbo_within_group').value = '".$data[csf('within_group')]."';\n";
		echo "$('#cbo_within_group').attr('disabled','disabled');\n";

		echo "load_drop_down( 'requires/yd_bill_issue_controller',".$data[csf('company_id')]."+'_'+".$data[csf('within_group')].", 'load_drop_down_party', 'party_td' );\n";
		echo "document.getElementById('cbo_party_name').value = '".$data[csf('party_id')]."';\n";
		echo "$('#cbo_party_name').attr('disabled','disabled');\n";

		echo "load_drop_down( 'requires/yd_bill_issue_controller',".$data[csf('party_id')]."+'_'+2, 'load_drop_down_party_location', 'party_location_td' );\n";
		echo "document.getElementById('cbo_party_location').value = '".$data[csf('party_location')]."';\n";
		echo "$('#cbo_party_location').attr('disabled','disabled');\n";
		echo "document.getElementById('cbo_currency_id').value = '".$data[csf('CURRENCY_ID')]."';\n";
		echo "document.getElementById('cbo_pro_type').value = '".$data[csf('PROD_TYPE')]."';\n";
		echo "document.getElementById('cbo_order_type').value = '".$data[csf('ORDER_TYPE')]."';\n";
		echo "document.getElementById('txt_exchange_rate').value = '".$data[csf('EXCHANGE_RATE')]."';\n";
		// echo "$('#cbo_currency_id').attr('disabled','disabled');\n";

		echo "document.getElementById('txt_remarks').value = '".$data[csf('remarks')]."';\n";
	}
}
if($action=="bill_dtls_list_view")
{
	$data=explode('_', $data);
	
	$update_id = $data[0];
	$cbo_company_name = $data[2];

	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp_arr=return_library_array("select id, company_name from lib_company",'id','company_name');
	
	
	if($db_type==0)
	{  
		$ins_year_cond="year(a.insert_date)";
	}
	else
	{
		
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')"; 
	}
	$nameArray= sql_select("select id, company_name, variable_list, service_process_id,yarn_dyeing_process from variable_setting_yarn_dyeing where company_name='$cbo_company_name' and variable_list=2 order by id");
	$variable_list=$nameArray[0][csf('variable_list')];//2
	$yarn_dyeing_process=$nameArray[0][csf('yarn_dyeing_process')];//1 
	$service_process_id=$nameArray[0][csf('service_process_id')]; //1 

	$sql = "select a.id, a.YD_BILL_NO, a.YD_JOB_NO, a.order_type, a.PROD_TYPE, b.wo_no,b.DELIVERY_NO from YD_BILL_MST a , YD_BILL_DTLS b where a.id=b.mst_id and  a.status_active=1 and a.is_deleted=0 and a.id=$update_id and a.entry_form=698 group by a.id, a.YD_BILL_NO, a.YD_JOB_NO, a.order_type, a.PROD_TYPE, b.wo_no,b.DELIVERY_NO";
	//  echo $sql;
	$result = sql_select($sql);

	$tblRow = 1;
	foreach($result as $data)
	{
		if ($tblRow%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

		
		?>
			<tr style="cursor:pointer" id="row_<?php echo $tblRow;?>" bgcolor="<? echo $bgcolor; ?>" onClick="load_bill_data('<?php echo $data[csf('YD_JOB_NO')];?>','<?php echo $data[csf('YD_BILL_NO')];?>')">
				<td width="35" align="center"><?php echo $tblRow;?></td>
				
				<td id="td_<?php echo $tblRow;?>" width="150" align="center">
					<?php echo $data[csf('DELIVERY_NO')];?>
					<p id="billId_<?php echo $tblRow;?>" style="display: none;"><?php echo $data[csf('id')];?></p>
				</td>
				<td width="150" align="center">
					<?php echo $data[csf('YD_JOB_NO')];?>
				</td>
				<td width="100" align="center">
					<?php echo $data[csf('wo_no')];?>
				</td>
				<td width="100" align="center">
					<?php echo $w_pro_type_arr[$data[csf('PROD_TYPE')]];?>
				</td>
				<td width="100" align="center">
					<?php echo $w_order_type_arr[$data[csf('order_type')]];?>
				</td>
			</tr>
		<?
		$tblRow++;
	}
	exit();
	
}
if($action=="bill_update_dtls_list_view")
{
	
	$data=explode('_', $data);
	
	$yd_job_no = $data[0];
	$bill_no = $data[1];
	$cbo_company_name = $data[2];
	$update_id = $data[3];

	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');

	if($db_type==0)
	{  
        $ins_year_cond="year(a.insert_date)";
    }
	else
	{
        
        $ins_year_cond="TO_CHAR(a.insert_date,'YYYY')"; 
    }

	$sql = "select a.id, b.id as bill_dtls_id,b.DELIVERY_NO,b.DELIVERY_DATE, b.style_ref, b.sales_order_no, b.sales_order_id, b.buyer_buyer, b.lot, b.gray_lot, b.count_type, b.count_id, b.yarn_type_id, b.yarn_composition_id, b.yd_color_id, b.no_bag, b.cone_per_bag, b.uom, b.order_quantity, b.BILL_QUANTITY, b.RATE, b.AMOUNT, b.DOMESTIC_AMOUNT, b.REMARKS,b.DELIVERY_ID
	from yd_bill_mst a, yd_bill_dtls b
	where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and YD_BILL_NO='$bill_no' and b.MST_ID='$update_id' and a.entry_form=698 ";
		// echo $sql;
		$result = sql_select($sql);
	$tblRow=1;
	foreach ($result as $data) {
		

	?>
		<tr id="row_<?php echo $tblRow;?>">
			<td align="center" width="80">
				<div class="text_boxes" style="width:80px;text-align:center;background-color:white;"><a href="#" onClick="fnc_yd_delivery_print(<?php echo $data[csf('DELIVERY_ID')];?>)" ><?php echo $data[csf('DELIVERY_NO')];?></a></div>
				
				<input  readonly class="text_boxes" type="hidden" name="txtdeliveryNo[]" id="txtdeliveryNo_<?php echo $tblRow;?>" value="<?php echo $data[csf('DELIVERY_NO')];?>">
			</td>
			<td align="center" width="80">
				<input style="width:80px;text-align:center" readonly class="text_boxes" type="text" name="txtdeliveryDate[]" id="txtdeliveryDate_<?php echo $tblRow;?>" value="<?php echo $data[csf('DELIVERY_DATE')];?>">
			</td>
			<td align="center" width="80">
				<input style="width:80px" readonly class="text_boxes" type="text" name="txtstyleRef[]" id="txtstyleRef_<?php echo $tblRow;?>" value="<?php echo $data[csf('style_ref')];?>" title="<?php echo $data[csf('style_ref')];?>">
			</td>
			<td width="60">
				<input style="width:60px" readonly class="text_boxes" type="text" name="txtsaleOrder[]" id="txtsaleOrder_<?php echo $tblRow;?>" value="<?php echo $data[csf('sales_order_no')];?>">
				<input  class="text_boxes_numeric" type="hidden" name="txtsaleOrderID[]" id="txtsaleOrderID_<?php echo $tblRow;?>" value="<?php echo $data[csf('sales_order_id')];?>">
			</td>
			<td width="60">
				<input style="width:60px" readonly class="text_boxes" type="text" name="buyerBuyer[]" id="buyerBuyer_<?php echo $tblRow;?>" value="<?php echo $data[csf('buyer_buyer')];?>">
			</td>
			<td width="80">
				<input style="width:80px" readonly class="text_boxes" type="text" name="txtYdlot[]" id="txtYdlot_<?php echo $tblRow;?>" value="<?php echo $data[csf('lot')];?>">
			</td>
			<td width="80">
				<input style="width:80px" <?php echo $readonly; ?> class="text_boxes" type="text" name="txtGrayLot[]" id="txtGrayLot_<?php echo $tblRow;?>" value="<?php echo $data[csf('gray_lot')];?>">
				<input readonly class="text_boxes" type="hidden" name="txtHiddenGrayLot[]" id="txtHiddenGrayLot_<?php echo $tblRow;?>" value="<?php echo $data[csf('gray_lot')];?>">
			</td>
			<td width="60">
				<input class="text_boxes" type="hidden" name="txtcountTypeId[]" id="txtcountTypeId_<?php echo $tblRow;?>" value="<?php echo $data[csf('count_type')];?>">
				<?
				$count_type_arr = array(1 => "Single",2 => "Double");
				echo create_drop_down( "txtcountType_".$tblRow, 60, $count_type_arr,'', 1, '--- Select---', $data[csf('count_type')], "",1,'','','','','','',"txtcountType[]");
				?>
			</td>
			<td width="60">
				<input class="text_boxes" type="hidden" name="txtcountId[]" id="txtcountId_<?php echo $tblRow;?>" value="<?php echo $data[csf('count_id')];?>">
				<?
				if ($within_group==2) 
				{
						
						$sql="select distinct(b.id) as id,b.yarn_count from lib_yarn_count b where b.status_active=1 and b.is_deleted=0";
				}
				else
				{
						
						$sql="select distinct(b.id) as id,b.yarn_count from lib_yarn_count b where b.status_active=1 and b.is_deleted=0";
				}

					echo create_drop_down( "cboCount_".$tblRow, 60, $sql,"id,yarn_count", 1, "-- Select --",$data[csf('count_id')],"",1,'','','','','','',"cboCount[]"); 
				?>
			</td>
			<td width="80">
				<input class="text_boxes" type="hidden" name="cboYarnTypeId[]" id="cboYarnTypeId_<?php echo $tblRow;?>" value="<?php echo $data[csf('yarn_type_id')];?>">

				<? echo create_drop_down( "cboYarnType_".$tblRow, 80, $yarn_type,"", 1, "-- Select --",$data[csf('yarn_type_id')],"",1,'','','','','','',"cboYarnType[]"); ?>
			</td>
			<td width="100">
				<input class="text_boxes" type="hidden" name="txtydCompositionId[]" id="txtydCompositionId_<?php echo $tblRow;?>" value="<?php echo $data[csf('yarn_composition_id')];?>">
				<? echo create_drop_down( "cboComposition_".$tblRow, 100, $composition,"", 1, "-- Select --",$data[csf('yarn_composition_id')],"",1,'','','','','','',"cboComposition[]"); ?>
			</td>
			<td width="80">
				<input class="text_boxes" type="hidden" name="txtYarnColorId[]" id="txtYarnColorId_<?php echo $tblRow;?>" value="<?php echo $data[csf('yd_color_id')]; ?>">
				<? echo create_drop_down( "txtYarnColor_".$tblRow, 80, $color_arr,"", 1, "-- Select --",$data[csf('yd_color_id')],"",1,'','','','','','',"txtYarnColor[]"); ?>
			</td>
			<td width="40">
				<input style="width:40px" class="text_boxes_numeric" type="text" name="txtnoBag[]" id="txtnoBag_<?php echo $tblRow;?>" value="<?php echo $data[csf('no_bag')];?>">
			</td>
			<td width="50">
				<input style="width:50px" class="text_boxes_numeric" type="text" name="txtConeBag[]" id="txtConeBag_<?php echo $tblRow;?>" value="<?php echo $data[csf('cone_per_bag')];?>">
			</td>
			<td width="50">
				<input class="text_boxes" type="hidden" name="cboUomId[]" id="cboUomId_<?php echo $tblRow;?>" value="<?php echo $data[csf('uom')];?>">

				<? echo create_drop_down( "cboUom_".$tblRow, 50, $unit_of_measurement,"", 1, "-- Select --",$data[csf('uom')],"", 1,'','','','','','',"cboUom[]"); ?>
			</td>
			<td width="50">
				<input style="width:50px" readonly class="text_boxes_numeric" type="text" name="txtOrderqty[]" id="txtOrderqty_<?php echo $tblRow;?>" value="<?php echo $data[csf('order_quantity')];?>">
				<input readonly class="text_boxes_numeric" type="hidden" name="txtHiddenOrderqty[]" id="txtHiddenOrderqty_<?php echo $tblRow;?>" value="<?php echo $data[csf('order_quantity')];?>">
			</td>

			<td width="50">
				<input style="width:50px" class="text_boxes_numeric must_entry_caption" type="text" onKeyUp="validateBillQty(<?=$tblRow;?>);calculateAmount(<?=$tblRow;?>)" name="txtbillqty[]" id="txtbillqty_<?php echo $tblRow;?>" value="<?php echo $data[csf('BILL_QUANTITY')];?>">
			</td>

			<td width="50">
				<input style="width:50px" class="text_boxes_numeric must_entry_caption" type="text" name="txtrate[]" id="txtrate_<?php echo $tblRow;?>" onKeyUp="calculateAmount(<?=$tblRow;?>)" value="<?php echo number_format($data[csf('rate')],4);?>" placeholder="<?php echo number_format($data[csf('rate')],4,".","");?>">
			</td>
			<td width="50">
				<input style="width:50px" readonly class="text_boxes_numeric" type="text" name="txtamount[]" id="txtamount_<?php echo $tblRow;?>" value="<?php echo number_format($data[csf('AMOUNT')],2,".","");?>">
				
			</td>
			<td width="50">
				<input style="width:50px" readonly class="text_boxes_numeric" type="text" name="txtdomesticamount[]" id="txtdomesticamount_<?php echo $tblRow;?>" value="<?php echo number_format($data[csf('DOMESTIC_AMOUNT')],2,".","");?>">
			</td>
			<td width="50">
				<input style="width:50px" class="text_boxes" type="text" name="txtremarks[]" id="txtremarks_<?php echo $tblRow;?>" value="<?php echo $data[csf('REMARKS')];?>" placeholder="Remarks">
				
			</td>
			<td width="50" style="display:none">
				<input class="text_boxes_numeric" type="hidden" name="txtHiddenDtlsId[]" id="txtHiddenDtlsId_<?php echo $tblRow;?>" value="<?php echo $data[csf('bill_dtls_id')];?>">
			</td>
		</tr>
	<?
		$tblRow++;
	}
	exit();

}

if($action=="yd_bill_print")
{
	// echo $data;
	extract($_REQUEST);
	$data = explode('*', $data);

	$yd_job_no 			= $data[0];
	$bill_no			= $data[1];
	$cbo_company_name 	= $data[2];
	$update_id 			= $data[3];
	$report_title		= $data[4];

	$imge_arr			= return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$company_library 	= return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_library 		= return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$count_arr			= return_library_array( "select distinct(b.id) as id,b.yarn_count from lib_yarn_count b where b.status_active=1 and b.is_deleted=0",'id','yarn_count');
	$color_arr			= return_library_array( "select id, color_name from lib_color",'id','color_name');
	$count_type_arr 	= array(1 => "Single",2 => "Double");

	$company_info = sql_select("Select COMPANY_NAME,PLOT_NO,LEVEL_NO,ROAD_NO,BLOCK_NO,COUNTRY_ID,CITY from lib_company where id=$cbo_company_name");
	// print_r($company_info);exit;
	$company_name = $company_info[0]['COMPANY_NAME'];
	if($db_type==0)
	{  
        $ins_year_cond="year(a.insert_date)";
    }
	else
	{
        $ins_year_cond="TO_CHAR(a.insert_date,'YYYY')"; 
    }
	$sql_mst= "select a.id, a.BILL_DATE, a.WITHIN_GROUP, a.PARTY_ID, a.PARTY_LOCATION, a.CURRENCY_ID, a.PROD_TYPE, a.ORDER_TYPE, a.REMARKS from yd_bill_mst a where a.id='$update_id' and a.entry_form=698 and a.status_active=1 and a.is_deleted=0";
	$data_array= sql_select($sql_mst);

	$sql = "select a.id, b.id as bill_dtls_id,b.DELIVERY_NO,b.DELIVERY_DATE, b.style_ref, b.sales_order_no, b.sales_order_id, b.buyer_buyer, b.lot, b.gray_lot, b.count_type, b.count_id, b.yarn_type_id, b.yarn_composition_id, b.yd_color_id, b.no_bag, b.cone_per_bag, b.uom, b.order_quantity, b.BILL_QUANTITY, b.RATE, b.AMOUNT, b.REMARKS,b.DELIVERY_ID,b.WO_NO,c.YD_RECEIVE,d.item_color_id
	from yd_bill_mst a, yd_bill_dtls b, yd_store_receive_mst c, yd_ord_dtls d
	where a.id=b.mst_id and b.DELIVERY_ID=c.id and b.DELV_DTLS_ID=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and YD_BILL_NO='$bill_no' and b.MST_ID='$update_id' and a.entry_form=698 ";
		// echo $sql;
	$result = sql_select($sql);
	?>
	    <div style="width:1220px;">
			<table width="1220" cellpadding="0" cellspacing="0" >
				<tr>
					<td style="display:none;" width="70" align="right"> 
						<img  src='../../<? echo $imge_arr[str_replace("'","",$data[2])]; ?>' height='100%' width='100%' />
					</td>
					<td>
						<table width="800" cellspacing="0" align="center" style="padding-left:100px;">
							<tr>
								<td align="center" style="font-size:20px"><strong ><? echo $company_name; ?></strong></td>
							</tr>
							<tr>
								<td align="center"  style="font-size:14px"><? echo $company_info[0]['CITY']; ?></td>
							</tr>
							<tr><td>&nbsp;</td></tr>
							<tr>
								<td align="center"><strong>YARN DYEING BILL</strong></td>
							</tr>
						</table>
					</td>
				</tr>
				
			</table>
        	<hr>
			<br>
			<table width="1220" align="center" cellpadding="0" cellspacing="0">
				<tr>
					<td>Bill No: &nbsp;<?php echo $bill_no;?></td>
					<td>Date: &nbsp; <?php echo $data_array[0]['BILL_DATE'];?></td>
				</tr>
				<tr>
					<td>Party: &nbsp;
						<?php
                		if($data_array[0][csf('within_group')]==1){
                			
                			$party_name=$company_library[$data_array[0][csf('party_id')]];
                		}
                		else{

                			$party_name=$buyer_library[$data_array[0][csf('party_id')]];
                		}
                		echo $party_name;	
                	 	?>
					</td>
					<td>Within Group:  &nbsp; <?php echo $yes_no[$data_array[0]['WITHIN_GROUP']];?></td>
				</tr>
				<tr>
					<td>Address: &nbsp;
						<?php
							if( $dataArray[0][csf('within_group')]==1)
							{
								
								$party_address=show_company($dataArray[0][csf('party_id')],'','');
							}
							else
							{
								$party_id=$dataArray[0][csf('party_id')];
								$nameArray=sql_select( "SELECT address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_id"); 
								foreach ($nameArray as $result)
								{ 
									if($result!="") $party_address=$result[csf('address_1')];
								}
							}
							echo $party_address;
						?>
					</td>
					<td>Currency: &nbsp; <?php echo $currency[$data_array[0]['CURRENCY_ID']];?></td>
				</tr>
				<tr>
					<td>Prod. Type: &nbsp;<?php echo $w_pro_type_arr[$data_array[0]['PROD_TYPE']];?></td>
					<td>Order Type: &nbsp; <?php echo $w_order_type_arr[$data_array[0]['ORDER_TYPE']];?></td>
				</tr>
				<tr>
					<td colspan="2">Remarks: &nbsp; <?php echo $data_array[0]['REMARKS'];?></td>
				</tr>
			</table>
			<br><br>
			<table align="left" cellspacing="0" width="1220" border="1" rules="all" class="rpt_table" style="font-size:13px">
				<thead bgcolor="#dddddd" align="center">
					<th width="30">SL</th>
					<th width="80">W/O No</th>
					<th width="80">Job No/Sales order no</th>
					<th width="80">Style/ Ref. No.</th>
					<th width="80">Cust:Buyer</th>
					<th width="80">Del. Challan No</th>
					<th width="80">Del. Date</th>
					<th width="80">Color Range</th>
					<th width="80">Y/D Color</th>
					<th width="200">Yarn Type</th>
					<th width="60">Order Qty. (Kg)</th>
					<th width="60">Delv. Qty. (Kg)</th>
					<th width="60">Rate.(Kg)</th>
					<th width="60">Total Value</th>
					<th width="100">Remarks</th>
				</thead>
				<tbody>
					<?php

						$i=1; //$grand_tot_qty=0; $grand_tot_total_order_quantity_qty=0; $grand_tot_del_qty=0; $grand_tot_no_bag_qty=0; 
						
						foreach ($result as $row) 
						{
							
							?>
							<tr>
								<td align="center" width="30"><? echo $i; ?></td>
								<td align="center" width="100"><?php echo $row[csf("WO_NO")]; ?></td>
								<td align="center" width="100"><?php echo $row[csf("sales_order_no")]; ?></td>
								<td align="center" width="100"><?php echo $row[csf("style_ref")]; ?></td>
								<td align="center" width="90"><?php echo $row[csf("buyer_buyer")]; ?></td>
								<td align="center" width="100"><?php echo $row[csf("YD_RECEIVE")]; ?></td>
								<td align="center" width="80"><?php echo $row[csf("DELIVERY_DATE")]; ?></td>
								<td align="center" width="80"><?php echo $color_range[$row[csf("item_color_id")]]; ?></td>
								<td align="center" width="80"><?php echo $color_arr[$row[csf("yd_color_id")]]; ?></td>
								<td align="right" width="100"><?php echo $composition[$row[csf("YARN_COMPOSITION_ID")]]." ".$yarn_type[$row[csf("YARN_TYPE_ID")]].", ".$count_arr[$row[csf("COUNT_ID")]]; ?></td>
								<td align="right" width="100"><?php echo number_format($row[csf("ORDER_QUANTITY")],2); ?></td>
								<td align="right" width="100"><?php echo number_format($row[csf("BILL_QUANTITY")],2); ?></td>
								<td align="right" width="60"><?php echo number_format($row[csf("RATE")],2); ?></td>
								<td align="right" width="60"><?php echo number_format($row[csf("AMOUNT")],2); ?></td>
								<td width="100"><?php echo $row[csf("REMARKS")]; ?></td>
								
							</tr>
							<?php
							$i++;
							$grand_tot_qty+=$row[csf("BILL_QUANTITY")];
							$grand_tot_amount += $row[csf("AMOUNT")];
						}
					?>
					<tr>
						<td colspan="11" align="right"><strong>Total:</strong></td>
						<td align="right"><strong><?php echo number_format($grand_tot_qty,2);?></strong></td>
						<td align="right">&nbsp</td>
						<td align="right"><strong><?php echo number_format($grand_tot_amount,2);?></strong></td>
						<td align="right"><strong></strong></td>
					</tr>
				</tbody>
        	</table>



		</div>

	<?
}
?>
<?
/*-------------------------------------------- Comments -----------------------
Version (MySql)          :  V2
Version (Oracle)         :  V1
Converted by             :  MONZU
Converted Date           :  24-05-2014
Purpose			         : 	This Form Will Create Woven Garments Price Quotation Entry.
Functionality	         :	
JS Functions	         :
Created by		         :	Monzu 
Creation date 	         : 	18-10-2012
Requirment Client        : 
Requirment By            : 
Requirment type          : 
Requirment               : 
Affected page            : 
Affected Code            :                   
DB Script                : 
Updated by 		         : 		
Update date		         : 		   
QC Performed BY	         :		
QC Date			         :	
Comments		         :
-------------------------------------------------------------------------------*/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.fabrics.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$type=$_REQUEST['type'];
$permission=$_SESSION['page_permission'];
$color_library=return_library_array( "select id,color_name from lib_color where is_deleted=0 and status_active=1", "id", "color_name"  );
$size_library=return_library_array( "select id,size_name from lib_size where is_deleted=0 and status_active=1", "id", "size_name"  );
//----------------------------------------------------Start---------------------------------------------------------
//*************************************************Master Form Start************************************************
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );     	 
} 

if ($action=="open_stripe_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
	var permission='<? echo $permission; ?>';
	var job_no='<? echo $txt_job_no; ?>';
	//alert(permission);
	function add_break_down_set_tr( i )
	{
		var row_num=$('table#tbl_set_details tbody tr').length;
		if (row_num!=i)
		{
			return false;
		}
		if (form_validation('txtstcolor_'+i+'*txtlenth_'+i+'*txtwidth_'+i,'Stripe Color*Lenth*Width')==false)
		{
			return;
		}
		else
		{
			i++;
			 $("table#tbl_set_details tbody tr:last").clone().find("input,select,a").each(function() {
				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				  'name': function(_, name) { return name + i },
				  'value': function(_, value) { return value }              
				});
			  }).end().appendTo("table#tbl_set_details tbody");
			  $('#txtlenth_'+i).removeAttr("onChange").attr("onChange","fnc_calculate_yarn_cons("+i+")");
			  $('#txtwidth_'+i).removeAttr("onChange").attr("onChange","fnc_calculate_yarn_cons("+i+")");
			  
			  $('#increaseset_'+i).removeAttr("onClick").attr("onClick","add_break_down_set_tr("+i+")");
			  $('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_delete_down_tr("+i+",'tbl_set_details')");
			  $('#txtstcolor_'+i).val(''); 
			  $('#txtlenth_'+i).val(''); 
			  $('#txtwidth_'+i).val(''); 
			  $('#txtyarncons_'+i).val('');
		}
	}
	
	function fn_delete_down_tr(rowNo,table_id) 
	{   
		if(table_id=='tbl_set_details')
		{
			var numRow = $('table#tbl_set_details tbody tr').length; 
			if(numRow==rowNo && rowNo!=1)
			{
				$('#tbl_set_details tbody tr:last').remove();
				set_sum();
				calculate_fab_req();
			}
		}
	}
	
	function color_select_popup(buyer_name,texbox_id)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'stripe_color_measurement_controller_sweater.php?action=color_popup&buyer_name='+buyer_name, 'Color Select Pop Up', 'width=300px,height=220px,center=1,resize=1,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var color_name=this.contentDoc.getElementById("color_name");
			if (color_name.value!="")
			{
				$('#'+texbox_id).val(color_name.value);
			}
		}
	}
	
	function set_sum()
	{
		var tottalmeasurement=0;
		var row_num=$('table#tbl_set_details tbody tr').length;
		for (var i=1; i<=row_num; i++)
		{
			tottalmeasurement+=$('#txtyarncons_'+i).val()*1;
		}
		$('#txtyarncons_tot').val(number_format(tottalmeasurement,4,'.','' ));
	}
	
	function fnc_calculate_yarn_cons(inc)
	{
		var consdzn=$('#consdzn').val()*1;
		var totalyarnreq=0; var lenth_width=0;
		var row_num=$('table#tbl_set_details tbody tr').length;
		for (var i=1; i<=row_num; i++)
		{
			var lenth=$('#txtlenth_'+i).val()*1;
			var width=$('#txtwidth_'+i).val()*1;
		
			lenth_width=lenth_width+(lenth*width);
		}
		
		for (var j=1; j<=row_num; j++)
		{
			var txtlenth=$('#txtlenth_'+j).val()*1;
			var txtwidth=$('#txtwidth_'+j).val()*1;
			var str_lanth_width=txtlenth*txtwidth;
			totalyarnreq=(consdzn/lenth_width)*str_lanth_width;
			
			//alert(totalyarnreq+'_'+consdzn+'_'+str_lanth_width);
			
			$('#txtyarncons_'+j).val( number_format(totalyarnreq,4,'.','' ) );
		}
		set_sum();
	}
	
	function fnc_stripe_color( operation )
	{
		freeze_window(operation);
		if(operation==2)
		{
			alert("Delete Restricted");
			release_freezing();
			return;
		}
		var row_num=$('table#tbl_set_details tbody tr').length;
		var data_all="";
		
		$('#txtlenth_'+j).val()
		
		var qccons=$('#consdzn').attr('qccons')*1;
		var stripecons=$('#totactualcons').val()*1;
		//alert(qccons+'='+stripecons)
		if(stripecons>qccons)//ISD-23-27297
		{
			var al_magg="QC Actual Cons is= "+qccons+" .\n Stripe Actual Color Cons= "+stripecons+" .\n Stripe Actual Color Cons is Greater then QC Actual Cons.";
			var r=confirm(al_magg);
			if(r==false)
			{
				release_freezing();
				return;
			}
			else
			{
			}
		}
		
		for (var i=1; i<=row_num; i++)
		{
			if (form_validation('txtstcolor_'+i+'*txtlenth_'+i+'*txtwidth_'+i,'Stripe Color*Lenth*Width')==false)
			{
				release_freezing();
				return;
			}
			
			data_all=data_all+get_submitted_data_string('txt_job_no*cbogmtsitem*fabric_cost_id*po_id*cbo_color_name*cbo_size_name*txtstcolor_'+i+'*txtlenth_'+i+'*txtwidth_'+i+'*txtyarncons_'+i,"../../../",i);
		}
		
		var data="action=save_update_delete&operation="+operation+'&total_row='+row_num+data_all;
		
		http.open("POST","stripe_color_measurement_controller_sweater.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_stripe_color_reponse;
	}
	
	function fnc_stripe_color_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			if (reponse[0].length>2) reponse[0]=10;
			release_freezing();
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
    	<div style="display:none"><? echo load_freeze_divs ("../../../",$permission); ?></div>
     	<?
		
        $condition= new condition();
        if(str_replace("'","",$txt_job_no) !=''){
            $condition->job_no("='$txt_job_no'");
        }
        $condition->init();
        $GmtsitemRatioArr=$condition->getGmtsitemRatioArr();
        //print_r($GmtsitemRatioArr);
        //$fabric= new fabric($condition);
        //$fabric_costing_arr=$fabric->getQtyArray_by_FabriccostidAndGmtscolor_knitAndwoven_greyAndfinish();	
        //$TotalGreyreq=array_sum($fabric_costing_arr['sweater']['grey'][$fabric_cost_id][$cbo_color_name]);
        $fabric_color=array();
        $color_type_id=0; $fab_des=''; $plan_cut_qnty=0; $buyer_name=0; $quotdtlsid=0; $companyid=0; $fabuom=0;
        $sql_data=sql_select("select a.company_name, a.buyer_name, c.color_number_id, c.order_quantity, c.plan_cut_qnty, d.body_part_id, d.color_type_id, d.fabric_description, d.color_size_sensitive, d.quotdtlsid, d.rate, d.uom, e.cons, e.requirment, f.contrast_color_id 
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_fabric_cost_dtls d, wo_pre_cos_fab_co_avg_con_dtls e left join wo_pre_cos_fab_co_color_dtls f 
		
		on e.pre_cost_fabric_cost_dtls_id=f.pre_cost_fabric_cost_dtls_id and e.color_number_id=f.gmts_color_id  
		
		where 1=1 and d.id=$fabric_cost_id and c.color_number_id=$cbo_color_name and c.size_number_id=$gmtssizesid and b.id=$ponoid and a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no  and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and  c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and e.cons!=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 order by b.id, d.id");//
        
        foreach($sql_data as $row)
        {
            $plan_cut_qnty+=$row[csf('plan_cut_qnty')];
            $fab_des=$body_part[$row[csf("body_part_id")]].', '.$color_type[$row[csf("color_type_id")]].', '.$row[csf("fabric_description")];
            $color_type_id=$row[csf("color_type_id")];
            if($row[csf('color_size_sensitive')]==1) $fabric_color[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
            else $fabric_color[$row[csf('color_number_id')]]=$row[csf('contrast_color_id')];
			$buyer_name=$row[csf('buyer_name')];
			$quotdtlsid=$row[csf('quotdtlsid')];
			$companyid=$row[csf('company_name')];
			$fabuom=$row[csf('uom')];
        }
		
		$mandatory_sql=sql_select("select variable_list, consumption_basis from variable_order_tracking where status_active=1 and variable_list in (103) and company_name='$companyid'");
		$qcconsfrom=2;
		foreach($mandatory_sql as $row){
			if($row[csf('variable_list')]==103) $qcconsfrom=$row[csf('consumption_basis')];
		}
		
		if($quotdtlsid>0)
		{
			$sql_qc="select id as quotdtlsid, consumption, rate, value as amount, ex_percent, tot_cons from qc_cons_rate_dtls where id='$quotdtlsid' and type=1 and tot_cons>0 and status_active=1 and is_deleted=0";
			
			$sql_qc_arr=sql_select($sql_qc);
			
			foreach($sql_qc_arr as $row)
			{
				if($fabuom==12)//KG
				{
					if($qcconsfrom==1 || $qcconsfrom==2)
					{
						$qcdata=number_format(($row[csf('consumption')]*0.453592),4);
					}
					else if($qcconsfrom==3)
					{
						$qcdata=number_format(($row[csf('tot_cons')]*0.453592),4);
					}
					$qcdata=number_format(($row[csf('tot_cons')]*0.453592),4);
				}
				else if($fabuom==15)//Lbs
				{
					if($qcconsfrom==1 || $qcconsfrom==2)
					{
						$qcdata=$row[csf('consumption')];
					}
					else if($qcconsfrom==3)
					{
						$qcdata=$row[csf('tot_cons')];
					}
					$qcdata=$row[csf('tot_cons')];
				}
			}
		}
		
		
        $GmtsitemRatio=$GmtsitemRatioArr[$txt_job_no][$cbogmtsitem];
        $cost_per_qty_arr=$condition->getCostingPerArr();
        //print_r($cost_per_qty_arr);
        $cost_per_qty=$cost_per_qty_arr[str_replace("'","",$txt_job_no)];
        $costing_per="Pcs";
        if($cost_per_qty>1){
            $costing_per=($cost_per_qty/12)."Dzn";
        }
        
        $TotalGreyreq=($plan_cut_qnty/$GmtsitemRatio)*($requirement/$cost_per_qty);
        //echo "(".$plan_cut_qnty."/".$GmtsitemRatio.")*(".$requirement."/".$cost_per_qty.")";
        //echo $color_type_id;
     ?>
        <table width="440" cellspacing="0" class="rpt_table" border="1" rules="all">
            <tr>
                <td width="150">Cons/<? echo $costing_per; ?></td>
                <td width="150" align="right">
                    <input type="hidden" id="TotalGreyreq" value="<?  echo $TotalGreyreq;?> "/>
                    <input type="hidden" id="consdzn" qccons="<?=$qcdata; ?>" value="<?  echo number_format($requirement,4);?> "/>
                    <? echo number_format($requirement,4); ?>
                </td>
                <td width="60">Lbs</td>
            </tr>
            <tr>
                <td>Yarn Desc</td>
                <td colspan="2"><? echo $fab_des; ?></td>
            </tr>
            </tr>
            <tr>
                <td>Body Color</td>
                <td colspan="2"><? echo $color_library[$cbo_color_name]; ?></td>
            </tr>
            <tr>
                <td>Body Size</td>
                <td colspan="2"><? echo $size_library[$gmtssizesid]; ?></td>
            </tr>
        </table>
        <br/>
            <input type="hidden" id="txt_job_no" name="txt_job_no" style="width:150px" class="text_boxes" value="<? echo $txt_job_no; ?>"/>
            <input type="hidden" id="cbogmtsitem" name="cbogmtsitem" style="width:150px" class="text_boxes" value="<? echo $cbogmtsitem; ?>"/>
            <input type="hidden" id="po_id" name="po_id" style="width:150px" class="text_boxes" value="<? echo $ponoid; ?>"/>
            <input type="hidden" id="fabric_cost_id" name="fabric_cost_id" style="width:150px" class="text_boxes" value="<? echo $fabric_cost_id; ?>"/>
            <input type="hidden" id="cbo_color_name" name="cbo_color_name" style="width:150px" class="text_boxes" value="<? echo $cbo_color_name; ?>"/>
            <input type="hidden" id="cbo_size_name" name="cbo_size_name" style="width:150px" class="text_boxes" value="<? echo $gmtssizesid; ?>"/>
    
        <table width="440" cellspacing="0" class="rpt_table" border="0" id="tbl_set_details" rules="all">
            <thead>
                <tr>
                    <th width="150">Stripe Color</th>
                    <th width="60">Lenth</th>
                    <th width="60">Width</th>
                    <th width="70">Yarn Cons</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
            <?
			
            $color_from_library=return_field_value("color_from_library", "variable_order_tracking", "company_name=$cbo_company_id and variable_list=23  and status_active=1 and is_deleted=0");
			//echo $cbo_company_id.'='. $color_from_library;
            if($color_from_library==1)
            {
                $readonly="readonly='readonly'"; $plachoder="placeholder='Click'"; $onClick="onClick='color_select_popup(".$buyer_name.",this.id)'";
            }
            else
            {
                $readonly=""; $plachoder=""; $onClick="";
            }
            $save_update=1;
            $sql_data=sql_select("select stripe_color, measurement, uom, totfidder, fabreq, fabreqtotkg, yarn_dyed, lenth, width from wo_pre_stripe_color where pre_cost_fabric_cost_dtls_id=$fabric_cost_id and color_number_id=$cbo_color_name and size_number_id=$gmtssizesid and po_break_down_id=$ponoid");
            if(count($sql_data)>0)
            {
                $i=1; $totmeasurement=0;
                foreach($sql_data as $row)
                {
                    $totmeasurement+=$row[csf('measurement')];
                    ?>
                    <tr>
                        <th><input type="text" id="txtstcolor_<? echo $i; ?>" name="txtstcolor_<? echo $i; ?>" style="width:140px" class="text_boxes" value="<? echo $color_library[$row[csf('stripe_color')]]; ?>" <? echo $onClick." ".$readonly." ".$plachoder; ?> /></th>
                        <th><input type="text" id="txtlenth_<? echo $i; ?>" name="txtlenth_<? echo $i; ?>" style="width:50px" class="text_boxes_numeric" value="<? echo $row[csf('lenth')]; ?>" onBlur="fnc_calculate_yarn_cons(<? echo $i;?>)"/></th>
                        <th><input type="text" id="txtwidth_<? echo $i; ?>" name="txtwidth_<? echo $i; ?>" style="width:50px" class="text_boxes_numeric" value="<? echo $row[csf('width')]; ?>" onBlur="fnc_calculate_yarn_cons(<? echo $i;?>)"/></th>
                        <th><input type="text" id="txtyarncons_<? echo $i; ?>" name="txtyarncons_<? echo $i; ?>" style="width:60px" class="text_boxes_numeric" value="<? echo $row[csf('measurement')]; ?>" readonly /></th>
                        <th>
                            <input type="button" id="increaseset_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_set_tr(<? echo $i; ?>)" />
                            <input type="button" id="decreaseset_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_delete_down_tr(<? echo $i; ?> ,'tbl_set_details' );" />
                        </th>
                    </tr>
                    <?
                    $i++;
                }//count end
            }
            else
            {
                $save_update=0;
                if($color_type_id ==6 || $color_type_id ==31 || $color_type_id ==32)
                {
                    $color=$color_library[$fabric_color[$cbo_color_name]]; $dis="disabled";
                }else{
                    $color=""; $dis="";
                }
                ?>
                <tr>
                    <th><input type="text" id="txtstcolor_1" name="txtstcolor_1" style="width:140px" class="text_boxes" <? echo $onClick." ".$readonly." ".$plachoder." ".$dis; ?> value="<? echo $color;  ?>"/> </th>
                    <th><input type="text" id="txtlenth_1" name="txtlenth_1" style="width:50px" class="text_boxes_numeric" onChange="fnc_calculate_yarn_cons(1);" /> </th>
                    <th><input type="text" id="txtwidth_1" name="txtwidth_1" style="width:50px" class="text_boxes_numeric" onChange="fnc_calculate_yarn_cons(1);" /> </th>
                    <th><input type="text" id="txtyarncons_1" name="txtyarncons_1" style="width:60px" class="text_boxes_numeric"/> </th>
                    <th>
                        <input type="button" id="increaseset_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_set_tr(1)" />
                        <input type="button" id="decreaseset_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_delete_down_tr(1 ,'tbl_set_details' );" />
                    </th>
                </tr>
                <? 
            }
            ?>
            </tbody>
            <tfoot>
                <tr>
                    <th>&nbsp;</th>
                    <th><input type="text" id="txtlenth_tot" name="txtlenth_tot" style="width:50px" class="text_boxes_numeric" value="<? echo $lenth; ?>" disabled /></th>
                    <th><input type="text" id="txtwidth_tot" name="txtwidth_tot" style="width:50px" class="text_boxes_numeric" value="<? echo $width; ?>" disabled /></th>
                    <th><input type="text" id="txtyarncons_tot" name="txtyarncons_tot" style="width:60px" class="text_boxes_numeric" value="<? echo number_format($totmeasurement,4); ?>" readonly/></th>
                    <th>&nbsp;</th>
                </tr>
                <tr>
                    <td align="center" valign="middle" class="button_container" colspan="5"> 
                    <?
                    if ( count($sql_data)>0)
                    {
                        echo load_submit_buttons( $permission, "fnc_stripe_color", 1,0 ,"",1,1) ;
                    }
                    else
                    {
                        echo load_submit_buttons( $permission, "fnc_stripe_color", 0,0 ,"",1,1) ;
                    }
                    ?>  
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    </body> 
    <script>set_sum();</script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if ($action=="order_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>
	<script>
	function js_set_value( job_no )
	{
		document.getElementById('selected_job').value=job_no;
		parent.emailwindow.hide();
	}
    </script>
</head>
<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="900" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
    	<tr>
        	<td align="center" width="100%">
            	<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                    <thead>                	 
                        <th width="150">Company Name</th>
                        <th width="150">Buyer Name</th>
                        <th width="100">Job No</th>
                        <th width="150">Order No</th>
                        <th width="200">Date Range</th><th></th>           
                    </thead>
        			<tr>
                    	<td> <input type="hidden" id="selected_job">
							<? 
								echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '',"load_drop_down( 'stripe_color_measurement_controller_sweater', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
							?>
                    </td>
                   	<td id="buyer_td">
                     <? 
						echo create_drop_down( "cbo_buyer_name", 172, $blank_array,'', 1, "-- Select Buyer --" );
					?>	
                    </td>
                    <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:100px"></td>
                     <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:150px"></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
					  <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 </td> 
            		 <td align="center">
                     <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_order_search').value, 'create_po_search_list_view', 'search_div', 'stripe_color_measurement_controller_sweater', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
        		</tr>
             </table>
          </td>
        </tr>
        <tr>
            <td  align="center" height="40" valign="middle">
             <? 
			echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );		
			?>
			<? echo load_month_buttons();  ?>
            </td>
            </tr>
        <tr>
            <td align="center" valign="top" id="search_div"> 
	
            </td>
        </tr>
    </table>    
     
    </form>
   </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_po_search_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($db_type==0) $year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[5]";
	if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$data[5]";
	if (str_replace("'","",$data[4])!="") $job_cond=" and a.job_no_prefix_num='$data[4]'  $year_cond"; else  $job_cond=""; 
	if (str_replace("'","",$data[6])!="") $order_cond=" and b.po_number like '%$data[6]%'  "; else  $order_cond=""; 
	if($db_type==0)
	{
	if ($data[2]!="" &&  $data[3]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	if($db_type==2)
	{
	if ($data[2]!="" &&  $data[3]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (2=>$comp,3=>$buyer_arr);
	if($db_type==0)
	{
	$sql= "select YEAR(a.insert_date) as year, a.job_no_prefix_num,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.po_number,b.po_quantity,b.shipment_date,a.job_no,c.id as pre_id from wo_po_details_master  a, wo_po_break_down b left join wo_pre_cost_mst c on b.job_no_mst=c.job_no and c.status_active=1 and c.is_deleted=0 where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 $shipment_date $company $buyer $job_cond $order_cond order by a.job_no";  
	}
	if($db_type==2)
	{
	$sql= "select to_char(a.insert_date,'YYYY') as year, a.job_no_prefix_num,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.po_number,b.po_quantity,b.shipment_date,a.job_no,c.id as pre_id from wo_po_details_master  a, wo_po_break_down b left join wo_pre_cost_mst c on b.job_no_mst=c.job_no and c.status_active=1 and c.is_deleted=0 where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 $shipment_date $company $buyer $job_cond $order_cond order by a.job_no";  
	}
	echo  create_list_view("list_view", "Year,Job No,Company,Buyer Name,Style Ref. No,Job Qty.,PO number,PO Quantity,Shipment Date, Precost id", "60,60,120,100,100,90,140,90,80,100","1080","320",0, $sql , "js_set_value", "job_no", "", 1, "0,0,company_name,buyer_name,0,0,0,0,0", $arr , "year,job_no_prefix_num,company_name,buyer_name,style_ref_no,job_quantity,po_number,po_quantity,shipment_date,pre_id", "",'','0,0,0,0,0,1,0,1,3,0') ;
}

if ($action=="populate_data_from_job_table")
{
	$data_array=sql_select("select job_no,company_name,buyer_name,style_ref_no from wo_po_details_master where job_no='$data' and is_deleted=0 and status_active=1");
	foreach ($data_array as $row)
	{
		echo "load_drop_down( 'requires/stripe_color_measurement_controller_sweater', '".$row[csf("company_name")]."', 'load_drop_down_buyer', 'buyer_td' );\n";
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n"; 
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_name")]."';\n";  
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("style_ref_no")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_name")]."';\n";  
		echo "document.getElementById('update_id').value = '".$row[csf("job_no")]."';\n"; 
		echo "$('#cbo_buyer_name').attr('disabled','true')".";\n";
		echo "$('#cbo_company_name').attr('disabled','true')".";\n";
	}
	exit();
}

if ($action=="show_fabric_cost_listview")
{
	$data=explode("_",$data);
	?>
	<fieldset style="width:810px;">
        <form id="fabriccost_3" autocomplete="off">
            <input type="hidden" id="tr_ortder" name="tr_ortder" value="" width="500" /> 
            <table width="810" cellspacing="0" class="rpt_table" border="0" id="tbl_fabric_cost" rules="all">
                <thead>
                    <tr>
                    	<th width="415">Fabric Description</th>
                        <th width="150">Gmts Item</th>
                        <th width="115">Fab Nature</th>
                        <th width="125">Color</th>
                    </tr>
                </thead>
                <tbody>
                <?
					$gmts_item_id=return_field_value("gmts_item_id", "wo_po_details_master", "job_no='$data[0]'");
				
					$fab_description=array();
					$fab_description_array=sql_select("select id, body_part_id, color_type_id, fabric_description from wo_pre_cost_fabric_cost_dtls where job_no='$data[0]'  and status_active=1 and is_deleted=0");
					foreach( $fab_description_array as $row_fab_description_array )
					{
						$fab_description[$row_fab_description_array[csf("id")]]=	$body_part[$row_fab_description_array[csf("body_part_id")]].', '.$color_type[$row_fab_description_array[csf("color_type_id")]].', '.$row_fab_description_array[csf("fabric_description")];
					}
						//echo count($garments_item);
					?>
					<tr id="fabriccosttbltr_<? echo $i; ?>" align="center">
                        <td>
                            <input type="hidden" id="libyarncountdeterminationid"  name="libyarncountdeterminationid" class="text_boxes" style="width:10px"/>
                            <? echo create_drop_down( "fabricdescription", 415, $fab_description, "",1," -- Select--","", "set_data(this.value)","","" ); ?> 
                        </td>
                        <td><? echo create_drop_down( "cbogmtsitem", 150, $garments_item,"", 1, "Display", "", "",1,$gmts_item_id ); ?></td>
                        <td><? echo create_drop_down( "cbofabricnature", 115, $item_category,"", 1, "Display", "", "",1,"2,3,100" ); ?></td>
                        <td id="color_td"> 
							<?  echo create_drop_down( "cbo_color_name", 125, $blank_array,"", 1, "-- Select Color --", $selected, "open_color_popup()" );?>
                            <input type="hidden" id="updateid" name="updateid"  class="text_boxes" style="width:20px"  />   
                        </td>
					</tr>
                </tbody>
            </table>
        </form>
	</fieldset>
	<?
	exit();
}

if ($action=="set_data")
{
	
	$data_array=sql_select("select item_number_id, fab_nature_id from wo_pre_cost_fabric_cost_dtls where id='$data' and is_deleted=0 and status_active=1");
	foreach ($data_array as $row){
		echo "document.getElementById('cbogmtsitem').value = '".$row[csf("item_number_id")]."';\n"; 
		echo "document.getElementById('cbofabricnature').value = '".$row[csf("fab_nature_id")]."';\n";
	}
	exit();
}

if ($action=="load_drop_down_color")
{
	$color_arr=array();
	$data=explode('_',$data);
	$sql_data=sql_select("select c.color_number_id from wo_po_color_size_breakdown c, wo_pre_cost_fabric_cost_dtls d, wo_pre_cos_fab_co_avg_con_dtls e where 1=1 and c.job_no_mst='$data[0]' and c.item_number_id=$data[1] and d.id=$data[2] and c.job_no_mst=d.job_no and c.job_no_mst=e.job_no and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and e.cons !=0 and d.is_deleted=0 and d.status_active=1 and c.is_deleted=0 and c.status_active=1 order by c.po_break_down_id, d.id");
	foreach($sql_data as $row){
		$color_arr[$row[csf('color_number_id')]]=$color_library[$row[csf('color_number_id')]];
	}
	echo create_drop_down( "cbo_color_name", 125, $color_arr,"", 1, "-- Select Color --", $selected, "open_color_popup()" );   
	exit();  	 
}

if ($action=="open_color_list_view")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
	var permission='<? echo $permission; ?>';
		function fnc_image_upload(i)
		{
			var img_ref_id = $("#img_ref_id_"+i).val();
				//alert(img_ref_id);
			file_uploader ( '../../../', img_ref_id,'', 'stripe_color_img', 0,1);
		}
		   function window_close(){
		   parent.emailwindow.hide();
		   }
	function add_break_down_set_tr( i )
	{
		var row_num=$('table#tbl_set_details tbody tr').length;
		if (row_num!=i)
		{
			return false;
		}
		if (form_validation('stcolor_'+i+'*measurement_'+i+'*cboorderuom_'+i,'Stripe Color*Measurement*UOM')==false)
		{
			return;
		}
		else
		{
			i++;
			 $("table#tbl_set_details tbody tr:last").clone().find("input,select,a").each(function() {
				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				  'name': function(_, name) { return name + i },
				  'value': function(_, value) { return value }              
				});
			  }).end().appendTo("table#tbl_set_details tbody");
			 // $('#txtsetitemratio_'+i).removeAttr("onChange").attr("onChange","calculate_set_smv("+i+")");
			  $('#measurement_'+i).removeAttr("onChange").attr("onChange","calculate_fidder("+i+")");
			  $('#totfidder_'+i).removeAttr("onChange").attr("onChange","calculate_fidder("+i+")");
			  $('#increaseset_'+i).removeAttr("onClick").attr("onClick","add_break_down_set_tr("+i+")");
			  $('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_delete_down_tr("+i+",'tbl_set_details')");
			  $('#stcolor_'+i).val(''); 
			  $('#measurement_'+i).val(''); 
			  $('#cboorderuom_'+i).val(''); 
			  $('#totfidder_'+i).val('');
		}
	}
	
	function fn_delete_down_tr(rowNo,table_id) 
	{   
		if(table_id=='tbl_set_details')
		{
			var numRow = $('table#tbl_set_details tbody tr').length; 
			if(numRow==rowNo && rowNo!=1)
			{
				$('#tbl_set_details tbody tr:last').remove();
				set_sum();
				calculate_fab_req()
			}
		}
	}
	
	function color_select_popup(buyer_name,texbox_id)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'stripe_color_measurement_controller_sweater.php?action=color_popup&buyer_name='+buyer_name, 'Color Select Pop Up', 'width=250px,height=200px,center=1,resize=1,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var color_name=this.contentDoc.getElementById("color_name");
			if (color_name.value!="")
			{
				$('#'+texbox_id).val(color_name.value);
				fnc_calculate_yarn_cons(texbox_id);
				fnc_actual_cons(texbox_id);
			}
		}
	}
	
	function set_sum()
	{
		var cons=0; var actualcons=0;
		var row_num=$('table#tbl_set_details tbody tr').length;
		for (var i=1; i<=row_num; i++)
		{
			cons+=$('#txtyarncons_'+i).val()*1;
			actualcons+=$('#txtactualcons_'+i).val()*1;
		}
		$('#totcons').val( number_format(cons,4,".","") );
		$('#totactualcons').val( number_format(actualcons,4,".","") );
		
	}
	
	function fnc_calculate_yarn_cons(inc)
	{
		var consdzn=$('#consdzn').val()*1;
		
		var row_num=$('table#tbl_set_details tbody tr').length;
		for (var i=1; i<=row_num; i++)
		{
			var sample_per=0; var yarn_cons=0;
			sample_per=$('#txtsmper_'+i).val()*1;
			//$color_per=($color_per*100)/$totcolor_qty;
			yarn_cons=(sample_per/100)*consdzn;
		
			$('#txtyarncons_'+i).val( number_format(yarn_cons,4,".","") );
		}
		set_sum();
	}
	
	function fnc_stripe_color( operation )
	{
		/*if(operation==2)
		{
			alert("Delete Restricted")
			return;
		}*/
		
		var qccons=$('#consdzn').attr('qccons')*1;
		var stripecons=$('#totactualcons').val()*1;
		//alert(qccons+'='+stripecons)
		if(stripecons>qccons)//ISD-23-27297
		{
			var al_magg="QC Actual Cons is= "+qccons+" .\n Stripe Actual Color Cons= "+stripecons+" .\n Stripe Actual Color Cons is Greater then QC Actual Cons.";
			var r=confirm(al_magg);
			if(r==false)
			{
				release_freezing();
				return;
			}
			else
			{
			}
		}
		
		var row_num=$('table#tbl_set_details tbody tr').length;
		var data_all="";
		for (var i=1; i<=row_num; i++)
		{
			if (form_validation('txtsmcolor_'+i+'*txtsmper_'+i+'*txtstcolor_'+i+'*txtyarncons_'+i+'*txtactualcons_'+i,'Sample Color*Sample Percent*Stripe Color*Cons')==false)
			{
				return;
			}
			data_all=data_all+get_submitted_data_string('txt_job_no*cbogmtsitem*fabric_cost_id*cbo_color_name*txtsmcolor_'+i+'*txtsmper_'+i+'*txtstcolor_'+i+'*txtyarncons_'+i+'*txtexcessper_'+i+'*txtactualcons_'+i,"../../../",i);
		}
		
		var data="action=save_update_delete&operation="+operation+'&total_row='+row_num+data_all;
		freeze_window(operation);
		http.open("POST","stripe_color_measurement_controller_sweater.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_stripe_color_reponse;
	}
	
	function fnc_stripe_color_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			if (reponse[0].length>2) reponse[0]=10;
			release_freezing();
			if (reponse[0]==0) alert("Data is Save Successfully");
			else if (reponse[0]==1) alert("Data is Update Successfully");
			else if (reponse[0]==2) alert("Data is Deleted Successfully");
			else if (reponse[0]==11) alert("This Information is used in another Table.");
			
			if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
			{
				parent.emailwindow.hide();
			}
			
		}
	}
	
	function fnc_actual_cons(inc_id)
	{
		var row_num=$('table#tbl_set_details tbody tr').length;
		for (var i=1; i<=row_num; i++)
		{
			var excessper=0; var actual_yarn_cons=0; var yarn_cons=0; var actual_yarn_per=0;
			excessper=$('#txtexcessper_'+i).val()*1;
			yarn_cons=$('#txtyarncons_'+i).val()*1;
			//$color_per=($color_per*100)/$totcolor_qty;
			actual_yarn_per=(excessper/100)*yarn_cons;
			actual_yarn_cons=yarn_cons+actual_yarn_per;
		
			$('#txtactualcons_'+i).val( number_format(actual_yarn_cons,4,".","") );
		}
		set_sum();
	}
	
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    <div style="display:none"><? echo load_freeze_divs ("../../../",$permission); ?></div>
    <?
	
    $condition= new condition();
	if(str_replace("'","",$txt_job_no) !=''){
		$condition->job_no("='$txt_job_no'");
	}
	$condition->init();
	$GmtsitemRatioArr=$condition->getGmtsitemRatioArr();
	//print_r($GmtsitemRatioArr);
	$fabric= new fabric($condition);
	$fabric_costing_arr=$fabric->getQtyArray_by_FabriccostidAndGmtscolor_knitAndwoven_greyAndfinish();	
	$TotalGreyreq=array_sum($fabric_costing_arr['sweater']['grey'][$fabric_cost_id][$cbo_color_name]);
	$fabric_color=array();
	$color_type_id=0; $fab_des=''; $plan_cut_qnty=0; $msmnt_break_down="";
	$sql_data=sql_select("select c.job_no_mst as job_no, c.po_break_down_id as id, c.item_number_id, c.country_id, c.color_number_id, c.order_quantity, c.plan_cut_qnty, d.id as pre_cost_dtls_id, d.body_part_id, d.fab_nature_id, d.fabric_source, d.color_type_id, d.fabric_description, d.color_size_sensitive, d.rate, d.msmnt_break_down, d.uom, d.quotdtlsid, d.company_id, e.cons, e.requirment, f.contrast_color_id  
	from wo_po_color_size_breakdown c, wo_pre_cost_fabric_cost_dtls d, wo_pre_cos_fab_co_avg_con_dtls e left join wo_pre_cos_fab_co_color_dtls f 
	on e.pre_cost_fabric_cost_dtls_id=f.pre_cost_fabric_cost_dtls_id and e.color_number_id=f.gmts_color_id 
	where 1=1 and d.id=$fabric_cost_id and c.color_number_id=$cbo_color_name and c.job_no_mst=d.job_no and c.job_no_mst=e.job_no and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and  c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and e.cons !=0 and d.is_deleted=0 and d.status_active=1 and c.is_deleted=0 and c.status_active=1 order by c.po_break_down_id, d.id");//
	/*echo "select c.job_no_mst as job_no, c.po_break_down_id as id, c.item_number_id, c.country_id, c.color_number_id, c.order_quantity, c.plan_cut_qnty, d.id as pre_cost_dtls_id, d.body_part_id, d.fab_nature_id, d.fabric_source, d.color_type_id, d.fabric_description, d.color_size_sensitive, d.rate, d.msmnt_break_down, e.cons, e.requirment, f.contrast_color_id  
	from wo_po_color_size_breakdown c, wo_pre_cost_fabric_cost_dtls d, wo_pre_cos_fab_co_avg_con_dtls e left join wo_pre_cos_fab_co_color_dtls f 
	on e.pre_cost_fabric_cost_dtls_id=f.pre_cost_fabric_cost_dtls_id and e.color_number_id=f.gmts_color_id 
	where 1=1 and d.id=$fabric_cost_id and c.color_number_id=$cbo_color_name and c.job_no_mst=d.job_no and c.job_no_mst=e.job_no and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and  c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and e.cons !=0 and d.is_deleted=0 and d.status_active=1 and c.is_deleted=0 and c.status_active=1 order by c.po_break_down_id, d.id";*/
	$plan_cut_sql="select sum(plan_cut_qnty) as plan_cut_qnty , item_number_id ,po_break_down_id ,color_number_id from wo_po_color_size_breakdown where color_number_id=$cbo_color_name and job_no_mst='$txt_job_no' and status_active=1 and is_deleted=0 group by item_number_id ,po_break_down_id ,color_number_id";
	//echo $plan_cut_sql;
	$sql_plan_cut=sql_select($plan_cut_sql);
	$data_plan_cut_qnty=array();
	foreach ($sql_plan_cut as $row) {
		$data_plan_cut_qnty[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]+=$row[csf('plan_cut_qnty')];
	}
	
	$cost_per_qty_arr=$condition->getCostingPerArr();
	$cost_per_qty=$cost_per_qty_arr[str_replace("'","",$txt_job_no)];
	$requirement=0; $uom=0;  $quotdtlsid=$quotdtlsid=0;
	$po_item_color=array();
	foreach($sql_data as $row)
	{
		//$plan_cut_qnty+=$row[csf('plan_cut_qnty')];
		$po_it_col=$row[csf('id')]."**".$row[csf('item_number_id')]."**".$row[csf('color_number_id')];
		if(!in_array($po_it_col, $po_item_color))
		{
			$plan_cut_qnty+=$data_plan_cut_qnty[$row[csf('id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]];
			array_push($po_item_color, $po_it_col);
		}
		
		$fab_des=$body_part[$row[csf("body_part_id")]].', '.$color_type[$row[csf("color_type_id")]].', '.$row[csf("fabric_description")];
		$color_type_id=$row[csf("color_type_id")];
		if($row[csf('color_size_sensitive')]==1) $fabric_color[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		else $fabric_color[$row[csf('color_number_id')]]=$row[csf('contrast_color_id')];
		if($row[csf('msmnt_break_down')]!="") $msmnt_break_down=$row[csf('msmnt_break_down')]->load();
		$uom=$row[csf('uom')];
		
		$quotdtlsid=$row[csf('quotdtlsid')];
		$companyid=$row[csf('company_id')];
	}
	
	$mandatory_sql=sql_select("select variable_list, consumption_basis from variable_order_tracking where status_active=1 and variable_list in (103) and company_name='$companyid'");
	$qcconsfrom=2;//ISD-23-27297
	foreach($mandatory_sql as $row){
		if($row[csf('variable_list')]==103) $qcconsfrom=$row[csf('consumption_basis')];
	}
	//echo $qcconsfrom;
	if($quotdtlsid>0)//ISD-23-27297
	{
		$sql_qc="select id as quotdtlsid, consumption, rate, value as amount, ex_percent, tot_cons from qc_cons_rate_dtls where id='$quotdtlsid' and type=1 and tot_cons>0 and status_active=1 and is_deleted=0";
		
		$sql_qc_arr=sql_select($sql_qc);
		
		foreach($sql_qc_arr as $row)
		{
			if($uom==12)//KG
			{
				if($qcconsfrom==1 || $qcconsfrom==2)
				{
					$qcdata=number_format(($row[csf('consumption')]*0.453592),4);
				}
				else if($qcconsfrom==3)
				{
					$qcdata=number_format(($row[csf('tot_cons')]*0.453592),4);
				}
				$qcdata=number_format(($row[csf('tot_cons')]*0.453592),4);
			}
			else if($uom==15)//Lbs
			{
				if($qcconsfrom==1 || $qcconsfrom==2)
				{
					$qcdata=$row[csf('consumption')];
				}
				else if($qcconsfrom==3)
				{
					$qcdata=$row[csf('tot_cons')];
				}
				$qcdata=$row[csf('tot_cons')];
			}
		}
	}
	//echo $qcdata.'==';
	
	$GmtsitemRatio=$GmtsitemRatioArr[$txt_job_no][$cbogmtsitem];
	
	$costing_per="Pcs";
	if($cost_per_qty>1) $costing_per=($cost_per_qty/12)."Dzn";
	//$TotalGreyreq=($plan_cut_qnty/$GmtsitemRatio)*($requirement/$cost_per_qty);
	//echo $plan_cut_qnty.'='.$GmtsitemRatio.'='.$requirement.'='.$cost_per_qty;
	//1000=1=8.4587=12 
	//echo $color_type_id;
 ?>
    <table width="460" cellspacing="0" class="rpt_table" border="1" rules="all">
        <tr>
            <td width="150">Cons/<?=$costing_per; ?></td>
            <td width="150" align="right" bgcolor="#CCFF99">
                <input type="hidden" id="TotalGreyreq" value="<?=$TotalGreyreq;?> "/>
                <input type="hidden" id="consdzn" qccons="<?=$qcdata; ?>" value="<?=number_format(($TotalGreyreq/$plan_cut_qnty)*$cost_per_qty*$GmtsitemRatio,4);?> "/>
                <?=number_format(($TotalGreyreq/$plan_cut_qnty)*$cost_per_qty*$GmtsitemRatio,4); ?>
            </td>
            <td width="60" align="center" bgcolor="#CCCCFF"><?=$unit_of_measurement[$uom]; ?></td>
        </tr>
        <tr>
            <td width="150">Fabric Desc</td>
            <td width="150" colspan="2"><?=$fab_des; ?></td>
        </tr>
        <tr>
            <td width="150" >Body Color</td>
            <td width="150" colspan="2" bgcolor="#FFFF00"><?=$color_library[$cbo_color_name]; ?></td>
        </tr>
    </table>
    <br/>
    <input type="hidden" id="txt_job_no" name="txt_job_no" style="width:150px" class="text_boxes" value="<?=$txt_job_no; ?>"/>
    <input type="hidden" id="cbogmtsitem" name="cbogmtsitem" style="width:150px" class="text_boxes" value="<?=$cbogmtsitem; ?>"/>
    <input type="hidden" id="fabric_cost_id" name="fabric_cost_id" style="width:150px" class="text_boxes" value="<?=$fabric_cost_id; ?>"/>
    <input type="hidden" id="cbo_color_name" name="cbo_color_name" style="width:150px" class="text_boxes" value="<?=$cbo_color_name; ?>"/>

    <table width="530" cellspacing="0" class="rpt_table" border="0" id="tbl_set_details" rules="all">
        <thead>
            <tr>
            	<th width="100">Sample Color</th>
                <th width="80">Color %</th>
            	<th width="150">Yarn Color</th>
                <th width="100">Cons</th>
                <th width="100">Excess %</th>
                <th width="100">Actual Cons</th>
                <th>Attached Image</th>
            </tr>
        </thead>
        <tbody>
			<?
			$sqlPurReq=sql_select("select a.requ_no, b.count_id, b.composition_id, b.com_percent,b.color_id, b.yarn_type_id from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id and b.job_no='$txt_job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		 
			$purReqColorChkArr=array();
				//Issue Id=19778 for Design tex
			foreach($sqlPurReq as $prow)
			{
				 
				$purReqColorChkArr[$prow[csf('color_id')]]=$prow[csf('color_id')];
			}

			
			$color_arr=return_library_array( "select id,color_name from lib_color ", "id", "color_name");
            $color_from_library=return_field_value("color_from_library", "variable_order_tracking", "company_name=$cbo_company_name  and variable_list=23  and status_active=1 and is_deleted=0");
            if($color_from_library==1)
            {
				$readonly="readonly='readonly'"; $plachoder="placeholder='Click'"; $onClick="onClick='color_select_popup($cbo_buyer_name,this.id)'";
            }
            else
            {
				$readonly=""; $plachoder=""; $onClick="onBlur='fnc_calculate_yarn_cons(this.id); fnc_actual_cons(this.id);'";
            }
            $save_update=1;
			$msmnt_break_down_arr=explode("_",$msmnt_break_down);
			$sample_id=$msmnt_break_down_arr[7];
            //echo "select color_id, bodycolor from sample_development_rf_color where mst_id='$sample_id' and color_id!=0 and is_deleted=0 and status_active=1 order by id asc";
			$sample_color_per_arr=array(); $totcolor_qty=0;
			$sql_sample=sql_select("select color_id, bodycolor from sample_development_rf_color where mst_id='$sample_id' and color_id!=0 and is_deleted=0 and status_active=1 order by id asc");
			foreach($sql_sample as $row)
			{
				$sample_color_per_arr[$row[csf("color_id")]]+=$row[csf("bodycolor")];
				$totcolor_qty+=$row[csf("bodycolor")];
			}
			unset($sql_sample);
			
			$sql_data=sql_select("select sample_color, sample_per, stripe_color, cons, excess_per, measurement from wo_pre_stripe_color where pre_cost_fabric_cost_dtls_id=$fabric_cost_id and color_number_id=$cbo_color_name and is_deleted=0 and status_active=1 order by id");
            if(count($sql_data)>0)
            {
				$i=1; $tot_cons=0;
				foreach($sql_data as $row)
				{
					$img_ref=$row[csf('sample_color')].'_'.$row[csf('stripe_color')];
					$pur_stripe_color=$purReqColorChkArr[$row[csf('stripe_color')]];
					$pur_disabled="";
					if($pur_stripe_color)
					{
						$pur_disabled=1;
					}
					?>
					<tr>
                    	<th><input type="text" id="txtsmcolor_<?=$i; ?>" name="txtsmcolor_<?=$i; ?>" style="width:90px" class="text_boxes" value="<?=$color_arr[$row[csf('sample_color')]]; ?>" readonly />
						<input type="hidden" name="img_ref_id_<? echo $i; ?>" id="img_ref_id_<? echo $i; ?>" class="text_boxes" value="<? echo $img_ref; ?>" style="width:20px" /></th>
					</th>
                        <th><input type="text" id="txtsmper_<?=$i; ?>" name="txtsmper_<?=$i; ?>" style="width:70px" class="text_boxes_numeric" value="<?=number_format($row[csf('sample_per')],4,".",""); ?>" readonly /></th>
						<th><input type="text" id="txtstcolor_<?=$i; ?>" name="txtstcolor_<?=$i; ?>" style="width:140px" class="text_boxes" <?=$onClick." ".$readonly." ".$plachoder." ".$dis; ?> value="<?=$color_arr[$row[csf('stripe_color')]]; ?>" <? if($bomfyarn_approval_id==1 || $pur_disabled==1) { echo "disabled";} else{ echo "";}?>/></th>
						<th><input type="text" id="txtyarncons_<?=$i; ?>" name="txtyarncons_<?=$i; ?>" style="width:90px" class="text_boxes_numeric" value="<?=number_format( $row[csf('cons')], 4,".",""); ?>" readonly /> </th>
                        <th><input type="text" id="txtexcessper_<?=$i; ?>" name="txtexcessper_<?=$i; ?>" style="width:90px" class="text_boxes_numeric" value="<?=$row[csf('excess_per')]; ?>" onBlur="fnc_actual_cons(<?=$i; ?>);" placeholder="Write" <? if($bomfyarn_approval_id==1) { echo "disabled";} else{ echo "";}?>/> </th>
						<th><input type="text" id="txtactualcons_<?=$i; ?>" name="txtactualcons_<?=$i; ?>" style="width:90px" class="text_boxes_numeric" value="<?=number_format($row[csf('measurement')],4,".",""); ?>" readonly /> </th>
						
						<th align="center"><p><input type="button" class="image_uploader" id="uploader_<?=$i; ?>" style="width:60px" value="ADD" onClick="fnc_image_upload(<? echo $i;?>);"></p></th>
						<th style="display:none">
						<?
						if($color_type_id !=6 && $color_type_id !=31 && $color_type_id !=32)
						{
							?>
							<input type="button" id="increaseset_<?=$i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_set_tr(<?=$i; ?>)" <? if($bomfyarn_approval_id==1) { echo "disabled";} else{ echo "";}?>/>
							<input type="button" id="decreaseset_<?=$i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_delete_down_tr(<?=$i; ?> ,'tbl_set_details' );"<? if($bomfyarn_approval_id==1) { echo "disabled";} else{ echo "";}?> />
							<?
						}
						?>
						</th>
					</tr> 
					<?
					$i++;
					$tot_percent+=$row[csf('sample_per')];
					$tot_cons+=$row[csf('cons')];
					$tot_actualcons+=$row[csf('measurement')];
				}
            }
            else
            {
				$save_update=0; $k=1;
				foreach($sample_color_per_arr as $colorid=>$color_per)
				{
					$img_ref=$row[csf('sample_color')].'_'.$row[csf('stripe_color')];
					if($color_type_id ==6 || $color_type_id ==31 || $color_type_id ==32) { $color=$color_library[$fabric_color[$cbo_color_name]]; $dis="disabled"; }else{ $color=""; $dis=""; }
					$color_per=($color_per*100)/$totcolor_qty;
					?>
					<tr>
                    	<th><input type="text" id="txtsmcolor_<?=$k; ?>" name="txtsmcolor_<?=$k; ?>" style="width:90px" class="text_boxes" value="<?=$color_arr[$colorid]; ?>" readonly />
						<input type="hidden" name="img_ref_id_<? echo $k; ?>" id="img_ref_id_<? echo $k; ?>" class="text_boxes" value="<? echo $img_ref; ?>" style="width:20px" /></th>
                        <th><input type="text" id="txtsmper_<?=$k; ?>" name="txtsmper_<?=$k; ?>" style="width:70px" class="text_boxes_numeric" value="<?=number_format($color_per,4,".",""); ?>" readonly /></th>
						<th><input type="text" id="txtstcolor_<?=$k; ?>" name="txtstcolor_<?=$k; ?>" style="width:140px" class="text_boxes" <?=$onClick." ".$readonly." ".$plachoder." ".$dis; ?> value="<?=$color; ?>" <? if($bomfyarn_approval_id==1) { echo "disabled";} else{ echo "";}?>/></th>
						<th><input type="text" id="txtyarncons_<?=$k; ?>" name="txtyarncons_<?=$k; ?>" style="width:90px" class="text_boxes_numeric" readonly /> </th>
                        <th><input type="text" id="txtexcessper_<?=$k; ?>" name="txtexcessper_<?=$k; ?>" style="width:90px" class="text_boxes_numeric" value="" onBlur="fnc_actual_cons(<?=$k; ?>);" placeholder="Write" <? if($bomfyarn_approval_id==1) { echo "disabled";} else{ echo "";}?> /> </th>
						<th><input type="text" id="txtactualcons_<?=$k; ?>" name="txtactualcons_<?=$k; ?>" style="width:90px" class="text_boxes_numeric" value="" readonly /> </th>
						<th align="center"><p><input type="button" class="image_uploader" id="uploader_<?=$k; ?>" style="width:60px" value="ADD" onClick="fnc_image_upload(<? echo $k;?>);"></p></th>
						<th style="display:none">
						<?
						if($color_type_id !=6 && $color_type_id !=31 && $color_type_id !=32)
						{
							?>
							<input type="button" id="increaseset_<?=$k; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_set_tr(<?=$k; ?>);" <? if($bomfyarn_approval_id==1) { echo "disabled";} else{ echo "";}?>/>
							<input type="button" id="decreaseset_<?=$k; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_delete_down_tr(<?=$k; ?> ,'tbl_set_details');" <? if($bomfyarn_approval_id==1) { echo "disabled";} else{ echo "";}?> />
							<?
						}
						?>
						</th>
					</tr>
					<? 
					$k++;
					$tot_percent+=$color_per;
				}
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
            	<th>&nbsp;</th>
            	<th><input type="text" id="totpercent" name="totpercent" style="width:70px" class="text_boxes_numeric" value="<?=number_format($tot_percent,4,".",""); ?>" readonly/></th>
                <th>&nbsp;</th>
                <th><input type="text" id="totcons" name="totcons" style="width:90px" class="text_boxes_numeric" value="<?=number_format($tot_cons,4); ?>" readonly/></th>
                <th>&nbsp;</th>
                <th><input type="text" id="totactualcons" name="totactualcons" style="width:90px" class="text_boxes_numeric" value="<?=number_format($tot_actualcons,4); ?>" readonly/></th>
            </tr>
            <tr>
                <td align="center" valign="middle" class="button_container" colspan="7"> 
					<?
						if ( count($sql_data)>0) echo load_submit_buttons( $permission, "fnc_stripe_color", 1,0 ,"",1,1) ;
						else echo load_submit_buttons( $permission, "fnc_stripe_color", 0,0 ,"",1,1) ;
                    ?>  
                </td>
            </tr>
        </tfoot>
    </table>
    </div>
    </body> 
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="color_popup")
{
	echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script> 
		function js_set_value(data)
		{
			document.getElementById('color_name').value=data;
			parent.emailwindow.hide();
		}
	</script> 
	</head>
	<body>
        <div align="center">
            <form>
                <input type="hidden" id="color_name" name="color_name" />
                <?
                if($buyer_name=="" || $buyer_name==0)
                {
                    $sql="select color_name,id FROM lib_color  WHERE status_active=1 and is_deleted=0"; 
                }
                else
                {
                    $sql="select a.id, a.color_name FROM lib_color a, lib_color_tag_buyer b WHERE a.id=b.color_id and b.buyer_id=$buyer_name and status_active=1 and is_deleted=0"; 
                }
                echo  create_list_view("list_view", "Color Name", "160","210","180",0, $sql , "js_set_value", "color_name", "", 1, "0", $arr , "color_name", "requires/sample_booking_non_order_controller",'setFilterGrid("list_view",-1);','0,0') ;
                ?>
            </form>
        </div>
	</body>
	</html>
	<?
	exit();
}

if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$job_id=return_field_value("id", "wo_po_details_master", "job_no=$txt_job_no");
	if ($operation==0){
		$con = connect();
		if($db_type==0) mysql_query("BEGIN");
		
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0";disconnect($con); die;}	
		$new_array_color=array();
		$id=return_next_id( "id", "wo_pre_stripe_color", 1 ) ;
		$field_array="id, job_no, job_id, item_number_id, pre_cost_fabric_cost_dtls_id, color_number_id, sample_color, sample_per, stripe_color, measurement, excess_per, cons, inserted_by, insert_date, status_active, is_deleted";
		
		//txtsmcolor_'+i+'*txtsmper_'+i+'*txtstcolor_'+i+'*measurement_'+i
		for ($i=1;$i<=$total_row;$i++)
		{
			$txtsmcolor="txtsmcolor_".$i;
			$txtsmper="txtsmper_".$i;
			$txtstcolor="txtstcolor_".$i;
			$yarncons="txtyarncons_".$i;
			
			$excessper="txtexcessper_".$i;
			$measurement="txtactualcons_".$i;
			
			if(str_replace("'","",$$txtsmcolor)!="")
			{
				if (!in_array(str_replace("'","",$$txtsmcolor),$smnew_array_color))
				{
					$smcolor_id = return_id( str_replace("'","",$$txtsmcolor), $color_library, "lib_color", "id,color_name","158");  
					$smnew_array_color[$smcolor_id]=str_replace("'","",$$txtsmcolor);
				}
				else $smcolor_id =  array_search(str_replace("'","",$$txtsmcolor), $smnew_array_color);
			}
			else $smcolor_id=0;
			
			if(str_replace("'","",$$txtstcolor)!="")
			{
				if (!in_array(str_replace("'","",$$txtstcolor),$new_array_color))
				{
					$color_id = return_id( str_replace("'","",$$txtstcolor), $color_library, "lib_color", "id,color_name","158");  
					$new_array_color[$color_id]=str_replace("'","",$$txtstcolor);
				}
				else $color_id =  array_search(str_replace("'","",$$txtstcolor), $new_array_color);
			}
			else $color_id=0;
			
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$txt_job_no.",'".$job_id."',".$cbogmtsitem.",".$fabric_cost_id.",".$cbo_color_name.",".$smcolor_id.",".$$txtsmper.",".$color_id.",".$$measurement.",".$$excessper.",".$$yarncons.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$id=$id+1;
		}
		$rID=sql_insert("wo_pre_stripe_color",$field_array,$data_array,1);
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0){
			if($rID ){
				mysql_query("COMMIT");  
				echo "0";
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10";
			}
		}
		else if($db_type==2 || $db_type==1 ){
			if($rID ){
				oci_commit($con);  
				echo "0";
			}
			else{
				oci_rollback($con);  
				echo "10";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)  // Update Here
	{
		$con = connect();
		if($db_type==0) mysql_query("BEGIN");
		 
		$yarn_booking=0;
		$yarn_booking_sql=sql_select("select id from wo_yarn_dyeing_dtls where job_no =$txt_job_no and status_active=1 and is_deleted=0");
		foreach($yarn_booking_sql as $yarn_booking_row){
			$yarn_booking=$yarn_booking_row[csf('id')];
		}
		if($yarn_booking>0){
			echo 11;
			disconnect($con);die;
		}
		
		 if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con);die;}	
		 $new_array_color=array();
		 $id=return_next_id( "id", "wo_pre_stripe_color", 1 ) ;
		 $field_array="id, job_no, job_id, item_number_id, pre_cost_fabric_cost_dtls_id, color_number_id, sample_color, sample_per, stripe_color, measurement, excess_per, cons, inserted_by, insert_date, status_active, is_deleted";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			$txtsmcolor="txtsmcolor_".$i;
			$txtsmper="txtsmper_".$i;
			$txtstcolor="txtstcolor_".$i;
			$yarncons="txtyarncons_".$i;
			
			$excessper="txtexcessper_".$i;
			$measurement="txtactualcons_".$i;
			
			if(str_replace("'","",$$txtsmcolor)!="")
			{
				if (!in_array(str_replace("'","",$$txtsmcolor),$smnew_array_color))
				{
					$smcolor_id = return_id( str_replace("'","",$$txtsmcolor), $color_library, "lib_color", "id,color_name","158");  
					$smnew_array_color[$smcolor_id]=str_replace("'","",$$txtsmcolor);
				}
				else $smcolor_id =  array_search(str_replace("'","",$$txtsmcolor), $smnew_array_color);
			}
			else $smcolor_id=0;
			
			if(str_replace("'","",$$txtstcolor)!="")
			{
				if (!in_array(str_replace("'","",$$txtstcolor),$new_array_color))
				{
					$color_id = return_id( str_replace("'","",$$txtstcolor), $color_library, "lib_color", "id,color_name","158");  
					$new_array_color[$color_id]=str_replace("'","",$$txtstcolor);
				}
				else $color_id =  array_search(str_replace("'","",$$txtstcolor), $new_array_color);
			}
			else $color_id=0;
			
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$txt_job_no.",'".$job_id."',".$cbogmtsitem.",".$fabric_cost_id.",".$cbo_color_name.",".$smcolor_id.",".$$txtsmper.",".$color_id.",".$$measurement.",".$$excessper.",".$$yarncons.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$id=$id+1;
		 }
		 //$rID_de3=execute_query( "delete from wo_pre_stripe_color where pre_cost_fabric_cost_dtls_id =".$fabric_cost_id." and color_number_id=$cbo_color_name",0);
		 $rID_de3=execute_query( "update wo_pre_stripe_color set status_active=0,is_deleted=1, updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where pre_cost_fabric_cost_dtls_id =$fabric_cost_id and color_number_id=$cbo_color_name and status_active=1 and is_deleted =0",0);	
		 $rID=sql_insert("wo_pre_stripe_color",$field_array,$data_array,1);
		 check_table_status( $_SESSION['menu_id'],0);
		 if($db_type==0)
		 {
			if($rID ){
				mysql_query("COMMIT");  
				echo "1";
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10";
			}
		 }
		 else if($db_type==2 || $db_type==1 )
		 {
			if($rID ){
				oci_commit($con);  
				echo "1";
			}
			else{
				oci_rollback($con);  
				echo "10";
			}
		 }
		disconnect($con);
		die;
	}
	else if ($operation==2)  // Delete Here
	{
		$con = connect();
		if($db_type==0) mysql_query("BEGIN");
		 
		$yarn_booking=0;
		$yarn_booking_sql=sql_select("select id from wo_yarn_dyeing_dtls where job_no =$txt_job_no and status_active=1 and is_deleted=0");
		foreach($yarn_booking_sql as $yarn_booking_row){
			$yarn_booking=$yarn_booking_row[csf('id')];
		}
		if($yarn_booking>0){
			echo 11;
			disconnect($con);die;
		}
		$rID_de3=execute_query( "update wo_pre_stripe_color set status_active=0,is_deleted=1, updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where pre_cost_fabric_cost_dtls_id =$fabric_cost_id and color_number_id=$cbo_color_name and status_active=1 and is_deleted =0",0);	

		if($db_type==0)
		{
			if($rID_de3){
				mysql_query("COMMIT");  
				echo 2;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo 10;
			}
		}
		else if($db_type==2 || $db_type==1 ){
			if($rID_de3){
				oci_commit($con);  
				echo 2;
			}
			else{
				oci_rollback($con);  
				echo 10;
			}
		}
		disconnect($con);
	}
}

if($action=="delete_row")
{
	$data=explode("_",$data);
	$yarn_booking=0;
	$yarn_booking_sql=sql_select("select id from wo_yarn_dyeing_dtls where job_no ='$data[2]' and status_active=1 and is_deleted=0");
	foreach($yarn_booking_sql as $yarn_booking_row){
		$yarn_booking=$yarn_booking_row[csf('id')];
	}
	if($yarn_booking>0){
		echo 11;
		disconnect($con);die;
	}
	$con = connect();
	if($db_type==0){
		mysql_query("BEGIN");
	}
	$rID_de3=execute_query( "delete from wo_pre_stripe_color where  pre_cost_fabric_cost_dtls_id =".$data[0]." and color_number_id=$data[1]",0);
	if($db_type==0){
		if($rID_de3){
			mysql_query("COMMIT");  
			echo 1;
		}
		else{
			mysql_query("ROLLBACK"); 
			echo 10;
		}
	}
	else if($db_type==2 || $db_type==1 ){
		if($rID_de3){
			oci_commit($con);  
			echo 1;
		}
		else{
			oci_rollback($con);  
			echo 10;
		}
	}
	disconnect($con);
}

if($action=="stripe_color_list_view")
{
	$data=explode("_",$data);
	$fab_description=array();
	$fab_description_array=sql_select("select id, body_part_id, color_type_id, fabric_description from wo_pre_cost_fabric_cost_dtls where job_no='$data[0]' and status_active=1 and is_deleted=0");
	foreach( $fab_description_array as $row_fab_description_array ){
	  $fab_description[$row_fab_description_array[csf("id")]]=	$body_part[$row_fab_description_array[csf("body_part_id")]].', '.$color_type[$row_fab_description_array[csf("color_type_id")]].', '.$row_fab_description_array[csf("fabric_description")];
	}
	unset($fab_description_array);
	
	$color_arr=array();
	$sql_data=sql_select("select a.color_number_id from  wo_po_color_size_breakdown a, wo_po_break_down b where a.job_no_mst=b.job_no_mst and a.po_break_down_id=b.id and a.job_no_mst='$data[0]' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.color_number_id order by a.color_number_id");
	foreach($sql_data as $row){
		$color_arr[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
	}
	unset($sql_data);
	
	$sql_data=sql_select("select pre_cost_fabric_cost_dtls_id, color_number_id from wo_pre_stripe_color where job_no='$data[0]' and is_deleted=0 and status_active=1 group by pre_cost_fabric_cost_dtls_id, color_number_id");
	$i=1;
	
	foreach($sql_data as $row){
		?>
        <div style="width:90%; float:left">
            <h3 align="left" class="accordion_h" onClick="show_content_data(<? echo $row[csf('pre_cost_fabric_cost_dtls_id')]; ?>, <? echo $row[csf('color_number_id')]; ?>)"><div style="width:75%; float:left"><? echo $fab_description[$row[csf('pre_cost_fabric_cost_dtls_id')]].", ". $color_library[$row[csf('color_number_id')]];  ?></div> <div style="width:25%; float:left; text-align:right; color:#F00"><? if($row[csf('color_number_id')] !=$color_arr[$row[csf('color_number_id')]]){ echo "This Color is deleted From Color Size Break Down";} ?></div></h3>
        </div>
        <div style="width:10%; float:left">
           <input type="button" id="decreaseyarn_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $row[csf('pre_cost_fabric_cost_dtls_id')]; ?>, <? echo $row[csf('color_number_id')]; ?> );" />
        </div>
        <?
		$i++;
	}
	exit();
}
?>

<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
//$user_name=$_SESSION['logic_erp']['user_id'];
$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

/*$buyer_short_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
$country_arr=return_library_array( "select id,country_name from lib_country", "id", "country_name");
$lib_prod_floor=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name");
$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
$line_library = return_library_array("select id,line_name from lib_sewing_line", "id", "line_name");
$resource_alocate_line = return_library_array("select id, line_number from prod_resource_mst", "id", "line_number");
*/
//------------------------------------------------------------------------------------------
if ($action=="load_drop_down_buyer")
{
    echo create_drop_down( "cbo_buyer_id", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );   
    exit();  
}


if($action=="report_generate")
{ 	
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process )); 
	
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$hidden_job_id=str_replace("'","",$hidden_job_id);
	$txt_po_no=str_replace("'","",$txt_po_no);
	$hidden_po_id=str_replace("'","",$hidden_po_id);
	$cbo_date_type=str_replace("'","",$cbo_date_type);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);

	
	if($txt_date_from && $txt_date_to)
	{
		if($cbo_date_type==1){
			$whereConExfac .= " and b.EX_FACTORY_DATE between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
		}
		else{
			$whereConDeliver .= " and a.DELIVERY_DATE between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
		}
	}

	if($cbo_company_id) $whereCon .= " and a.company_id='$cbo_company_id'";
	if($cbo_buyer_id) $whereCon .= " and a.buyer_id='$cbo_buyer_id'";
	if($txt_job_no) $whereCon .= " and f.JOB_NO like('%$txt_job_no')";
	if($hidden_job_id) $whereCon .= " and f.id ='$hidden_job_id'";
	if($txt_po_no) $whereCon .= " and e.PO_NUMBER like('%$txt_po_no')";
	if($hidden_po_id) $whereCon .= " and e.id ='$hidden_po_id'";
	
	
	
	$exfactory_type=return_field_value("ex_factory","variable_settings_production","VARIABLE_LIST =1 AND COMPANY_NAME = $cbo_company_id");
	

	
	
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$country_arr=return_library_array( "select id,country_name from lib_country", "id", "country_name");

	$size_library=return_library_array( "select id,size_name from lib_size where IS_DELETED=0 and STATUS_ACTIVE=1", "id", "size_name" );
	$color_library=return_library_array( "select id,color_name from lib_color where IS_DELETED=0 and STATUS_ACTIVE=1", "id", "color_name" );


		if($exfactory_type==3){
			$selectStr=",c.PRODUCTION_QNTY,e.COUNTRY_ID,e.SIZE_NUMBER_ID,e.COLOR_NUMBER_ID";
			$tableStr=",PRO_EX_FACTORY_DTLS c,WO_PO_COLOR_SIZE_BREAKDOWN e";
			$conditionStr=" and a.id = c.mst_id AND  e.PO_BREAK_DOWN_ID = d.id AND c.status_active = 1  AND c.is_deleted = 0 "; //AND c.color_size_break_down_id = e.id
		}
		else if($exfactory_type==2){
			$selectStr=",c.PRODUCTION_QNTY,e.COUNTRY_ID,e.SIZE_NUMBER_ID,e.COLOR_NUMBER_ID";
			$tableStr=",PRO_EX_FACTORY_DTLS c,WO_PO_COLOR_SIZE_BREAKDOWN e";
			$conditionStr=" and a.id = c.mst_id AND  e.PO_BREAK_DOWN_ID = d.id AND c.status_active = 1  AND c.is_deleted = 0 AND c.color_size_break_down_id = e.id ";
		}


	
	if($exfactory_type==1){
		if($cbo_date_type==1){
		$exFactorySql = "SELECT a.ENTRY_FORM,a.DELIVERY_DATE,
		   a.SYS_NUMBER,
		   a.CHALLAN_NO,
		   a.BUYER_ID,
		   a.REMARKS,
		   e.PO_NUMBER,
		   f.STYLE_REF_NO,
		   f.JOB_NO,
		   b.EX_FACTORY_QNTY  AS EXFACTPRU_QTY
	  FROM pro_ex_factory_delivery_mst  a,
		   pro_ex_factory_mst           b,
		   wo_po_break_down             e,
		   WO_PO_DETAILS_MASTER         f
	 WHERE     a.id = b.delivery_mst_id
		   AND b.po_break_down_id = e.id
		   AND f.job_no = e.JOB_NO_MST
		   AND a.status_active = 1
		   AND a.is_deleted = 0 and a.entry_form <> 85 
			$whereConExfac $whereCon";
			
		//echo $exFactorySql;
			
		$exFactorySqlResult = sql_select($exFactorySql);
		$challanArr=array();
		foreach($exFactorySqlResult as $rows){
				
				if($exfactory_type==3){
					$key=$rows[SYS_NUMBER].$rows[COLOR_NUMBER_ID].$rows[SIZE_NUMBER_ID];
				}
				else if($exfactory_type==2){
					$key=$rows[SYS_NUMBER].$rows[COLOR_NUMBER_ID];
				}
				else{
					$key=$rows[SYS_NUMBER];
				}
				
				$qtyArr[EXFACTPRU_QTY][$key]+=$rows[EXFACTPRU_QTY];
				$challanArr[$rows[SYS_NUMBER]]=$rows[SYS_NUMBER];
		}
	}
	}
	else
	{
		if($cbo_date_type==1){
		$exFactorySql = "SELECT a.ENTRY_FORM,a.DELIVERY_DATE,
		   d.COUNTRY_ID,
		   d.SIZE_NUMBER_ID,
		   d.COLOR_NUMBER_ID,
		   a.SYS_NUMBER,
		   a.CHALLAN_NO,
		   a.BUYER_ID,
		   a.REMARKS,
		   e.PO_NUMBER,
		   f.STYLE_REF_NO,
		   f.JOB_NO,
		   c.production_qnty  AS EXFACTPRU_QTY
	  FROM pro_ex_factory_delivery_mst  a,
		   pro_ex_factory_mst           b,
		   pro_ex_factory_dtls          c,
		   wo_po_color_size_breakdown   d,
		   wo_po_break_down             e,
		   WO_PO_DETAILS_MASTER         f
	 WHERE     a.id = b.delivery_mst_id
		   AND b.id = c.mst_id
		   AND b.po_break_down_id = e.id
		   AND f.job_no = d.JOB_NO_MST
		   AND d.id = c.color_size_break_down_id
		   AND b.po_break_down_id = d.po_break_down_id
		   AND a.status_active = 1
		   AND a.is_deleted = 0 and a.entry_form <> 85 
			$whereConExfac $whereCon";
			
		//echo $exFactorySql;
			
		$exFactorySqlResult = sql_select($exFactorySql);
		$challanArr=array();
		foreach($exFactorySqlResult as $rows){
				
				if($exfactory_type==3){
					$key=$rows[SYS_NUMBER].$rows[COLOR_NUMBER_ID].$rows[SIZE_NUMBER_ID];
				}
				else if($exfactory_type==2){
					$key=$rows[SYS_NUMBER].$rows[COLOR_NUMBER_ID];
				}
				else{
					$key=$rows[SYS_NUMBER];
				}
				
				$qtyArr[EXFACTPRU_QTY][$key]+=$rows[EXFACTPRU_QTY];
				$challanArr[$rows[SYS_NUMBER]]=$rows[SYS_NUMBER];
		}
	}
	}
	
	if(count($challanArr)>0){$whereConData=where_con_using_array($challanArr,1,'a.CHALLAN_NO');}
	
	
	if($exfactory_type==1){
		$exFactoryReturnSql = "SELECT a.ENTRY_FORM,a.DELIVERY_DATE,
		   a.SYS_NUMBER,
		   a.CHALLAN_NO,
		   a.BUYER_ID,
		   a.REMARKS,
		   e.PO_NUMBER,
		   f.STYLE_REF_NO,
		   f.JOB_NO,
		   b.EX_FACTORY_QNTY AS RETURN_QTY
	  FROM pro_ex_factory_delivery_mst  a,
		   pro_ex_factory_mst           b,
		   wo_po_break_down             e,
		   WO_PO_DETAILS_MASTER         f
	 WHERE     a.id = b.delivery_mst_id
		   AND b.po_break_down_id = e.id
		   AND f.job_no = e.JOB_NO_MST
		   AND a.status_active = 1
		   AND a.is_deleted = 0 and a.entry_form = 85
			$whereConDeliver $whereConData $whereCon";
	}
	else{
		$exFactoryReturnSql = "SELECT a.ENTRY_FORM,a.DELIVERY_DATE,
       
		   d.COUNTRY_ID,
		   d.SIZE_NUMBER_ID,
		   d.COLOR_NUMBER_ID,
		   a.SYS_NUMBER,
		   a.CHALLAN_NO,
		   a.BUYER_ID,
		   a.REMARKS,
		   e.PO_NUMBER,
		   f.STYLE_REF_NO,
		   f.JOB_NO,
		   c.production_qnty AS RETURN_QTY
	  FROM pro_ex_factory_delivery_mst  a,
		   pro_ex_factory_mst           b,
		   pro_ex_factory_dtls          c,
		   wo_po_color_size_breakdown   d,
		   wo_po_break_down             e,
		   WO_PO_DETAILS_MASTER         f
	 WHERE     a.id = b.delivery_mst_id
		   AND b.id = c.mst_id
		   AND b.po_break_down_id = e.id
		   AND f.job_no = d.JOB_NO_MST
		   AND d.id = c.color_size_break_down_id
		   AND b.po_break_down_id = d.po_break_down_id
		   AND a.status_active = 1
		   AND a.is_deleted = 0 and a.entry_form = 85
			$whereConDeliver $whereConData $whereCon";
	}
        //echo $exFactoryReturnSql; die;
	
	$exFactoryReturnSqlResult = sql_select($exFactoryReturnSql);
	foreach($exFactoryReturnSqlResult as $rows){
		
		if($rows[ENTRY_FORM]==85){
			if($exfactory_type==3){
				$key=$rows[CHALLAN_NO].$rows[COLOR_NUMBER_ID].$rows[SIZE_NUMBER_ID];
			}
			else if($exfactory_type==2){
				$key=$rows[CHALLAN_NO].$rows[COLOR_NUMBER_ID];
			}
			else{
				$key=$rows[CHALLAN_NO];
			}
			
			$dataArr[$key]=$rows;
			$qtyArr[RET_QTY][$key]+=$rows[RETURN_QTY];
			
			$retdateArr[RET_DATE][$key][$rows[DELIVERY_DATE]]=change_date_format($rows[DELIVERY_DATE]);
			$retidArr[RET_ID][$key][$rows[SYS_NUMBER]]=$rows[SYS_NUMBER];
			
			$colorArr[$key][$rows[COLOR_NUMBER_ID]]=$color_library[$rows[COLOR_NUMBER_ID]];
			$sizeArr[$key][$rows[SIZE_NUMBER_ID]]=$size_library[$rows[SIZE_NUMBER_ID]];
		}
			
			$challanArr[$rows[CHALLAN_NO]]=$rows[CHALLAN_NO];
		
	}



	if($exfactory_type==1){
		if($cbo_date_type!=1){
		$exFactorySql = "SELECT a.ENTRY_FORM,a.DELIVERY_DATE,
		   a.SYS_NUMBER,
		   a.CHALLAN_NO,
		   a.BUYER_ID,
		   a.REMARKS,
		   e.PO_NUMBER,
		   f.STYLE_REF_NO,
		   f.JOB_NO,
		   b.EX_FACTORY_QNTY  AS EXFACTPRU_QTY
	  FROM pro_ex_factory_delivery_mst  a,
		   pro_ex_factory_mst           b,
		   wo_po_break_down             e,
		   WO_PO_DETAILS_MASTER         f
	 WHERE     a.id = b.delivery_mst_id
		   AND b.po_break_down_id = e.id
		   AND f.job_no = e.JOB_NO_MST
		   AND a.status_active = 1
		   AND a.is_deleted = 0 and a.entry_form <> 85 $whereCon 
			".where_con_using_array($challanArr,1,'a.SYS_NUMBER')." ";
		$exFactorySqlResult = sql_select($exFactorySql);
			foreach($exFactorySqlResult as $rows){
					
					if($exfactory_type==3){
						$key=$rows[SYS_NUMBER].$rows[COLOR_NUMBER_ID].$rows[SIZE_NUMBER_ID];
					}
					else if($exfactory_type==2){
						$key=$rows[SYS_NUMBER].$rows[COLOR_NUMBER_ID];
					}
					else{
						$key=$rows[SYS_NUMBER];
					}
					
					$qtyArr[EXFACTPRU_QTY][$key]+=$rows[EXFACTPRU_QTY];
			
			}
		}
	}
	else
	{
	
			if($cbo_date_type!=1){
			$exFactorySql = "SELECT a.ENTRY_FORM,a.DELIVERY_DATE,
			   d.COUNTRY_ID,
			   d.SIZE_NUMBER_ID,
			   d.COLOR_NUMBER_ID,
			   a.SYS_NUMBER,
			   a.CHALLAN_NO,
			   a.BUYER_ID,
			   a.REMARKS,
			   e.PO_NUMBER,
			   f.STYLE_REF_NO,
			   f.JOB_NO,
			   c.production_qnty  AS EXFACTPRU_QTY
		  FROM pro_ex_factory_delivery_mst  a,
			   pro_ex_factory_mst           b,
			   pro_ex_factory_dtls          c,
			   wo_po_color_size_breakdown   d,
			   wo_po_break_down             e,
			   WO_PO_DETAILS_MASTER         f
		 WHERE     a.id = b.delivery_mst_id
			   AND b.id = c.mst_id
			   AND b.po_break_down_id = e.id
			   AND f.job_no = d.JOB_NO_MST
			   AND d.id = c.color_size_break_down_id
			   AND b.po_break_down_id = d.po_break_down_id
			   AND a.status_active = 1
			   AND a.is_deleted = 0 and a.entry_form <> 85 $whereCon 
				".where_con_using_array($challanArr,1,'a.SYS_NUMBER')." ";
			
			
			$exFactorySqlResult = sql_select($exFactorySql);
			foreach($exFactorySqlResult as $rows){
					
					if($exfactory_type==3){
						$key=$rows[SYS_NUMBER].$rows[COLOR_NUMBER_ID].$rows[SIZE_NUMBER_ID];
					}
					else if($exfactory_type==2){
						$key=$rows[SYS_NUMBER].$rows[COLOR_NUMBER_ID];
					}
					else{
						$key=$rows[SYS_NUMBER];
					}
					
					$qtyArr[EXFACTPRU_QTY][$key]+=$rows[EXFACTPRU_QTY];
			}
		}
	}
	
	
	
	
	
	
	
	
	$width=1150;
	
	ob_start();

    ?>

    <div style="width:<?=$width+25;?>px;" >
        <table width="<?=$width;?>" cellspacing="0" border="1" align="left" class="rpt_table" rules="all" id="table_header" >
            <thead>                
                <th width="30">Sl</th>    
                <th width="80">Return Date</th>                    
                <th width="80">Buyer</th>	
                <th width="80">Job no</th>
                <th width="80">Style no</th>
                <th width="80">Order No</th>
                <th width="100">Return ID</th>
                <th width="80">Country</th>
                <th width="80">Color</th>
                <th width="80">Size</th>
                <th width="80">Return Qty</th>
                <th width="100">Ex-Factory ID</th>
                <th width="80">Ex-Factory Qty</th>
                <th width="80">Actual Shipped Qty</th> 
                <th width='100'>Remarks</th> 
            </thead>
        </table>
        <div style="max-height:400px; overflow-y:scroll; width:<?=$width+18;?>px; float:left;" id="scroll_body">
            <table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="<?=$width;?>" rules="all" id="table_body" align="left"> 
            <tbody>
            <?
                $i=1;
				foreach($dataArr as $key=>$rows){
				$bgcolor = ($i % 2 == 0)?"#E9F3FF":"#FFFFFF";
             ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="30">&nbsp;<? echo $i;?></td>
                        <td width="80" align="center"><p><?= implode(', ',$retdateArr[RET_DATE][$key]);?></p></td>                    
                        <td width="80"><?= $buyer_arr[$rows[BUYER_ID]];?></td>	
                        <td width="80" align="center"><?= $rows[JOB_NO];?></td>
                        <td width="80"><p><?= $rows[STYLE_REF_NO];?></p></td>
                        <td width="80"><p><?= $rows[PO_NUMBER];?></p></td>
                        <td width="100" align="center"><p><?= implode(', ',$retidArr[RET_ID][$key]);?></p></td>
                        <td width="80"><?= $country_arr[$rows[COUNTRY_ID]];?></td>
                        <td width="80" align="center"><p><?= implode(', ',$colorArr[$key]);?></p></td>
                        <td width="80" align="center"><p><?= implode(', ',$sizeArr[$key]);?></p></td>
                        <td width="80" align="right"><?= $qtyArr[RET_QTY][$key];?></td>
                        <td width="100" align="center"><?= $rows[CHALLAN_NO];?></td>
                        <td width="80" align="right"><?= $qtyArr[EXFACTPRU_QTY][$key]; ?></td>
                        <td width="80" align="right"><?= round($qtyArr[EXFACTPRU_QTY][$key]-$qtyArr[RET_QTY][$key]); ?></td> 
                        <td width='100'><p><?= $rows[REMARKS];?></p></td>
                    </tr>
                    
                    <?
                $i++;
				}
           
        ?>
                </tbody>
                
        </table>
        </div>
       </div>
    <?
    foreach (glob("$user_id*.xls") as $filename) 
    {
        if( @filemtime($filename) < (time()-$seconds_old) )
        @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc,ob_get_contents());
    //$filename=$user_id."_".$name.".xls";
    echo "$total_data####$filename";

    exit();
}


if($action=="search_by_popup")
{
    echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
    extract($_REQUEST);
    ?>
     
    <script>
        
        var selected_job_no = new Array; 
        var selected_job_id = new Array;
        
        function check_all_data()
        {
            var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
            tbl_row_count = tbl_row_count - 1;

            for( var i = 1; i <= tbl_row_count; i++ )
            {
                $('#tr_'+i).trigger('click'); 
            }
        }
        
        function toggle( x, origColor ) {
            var newColor = 'yellow';
            if ( x.style ) {
                x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
            }
        }
        
        function js_set_value( str ) {
            if (str!="") str=str.split("_");
            toggle( document.getElementById( 'tr_' + str[1] ), '#FFFFCC' );
            if( jQuery.inArray( str[1], selected_job_id ) == -1 ) 
            {
                selected_job_no.push( str[0] );
                selected_job_id.push( str[1] );
            }
            else 
            {
                for( var i = 0; i < selected_job.length; i++ ) 
                {
                    if( selected_job_id[i] == str[1] ) break;
                }
                selected_job_no.splice( i, 1 );
                selected_job_id.splice( i, 1 );
            }
            
			var job = ''; 
            var style = '';
            for( var i = 0; i < selected_job_id.length; i++ ) 
            {
                job += selected_job_no[i] + '*';
                style += selected_job_id[i] + '*';
            }
            
            job_no = job.substr( 0, job.length - 1 );
            job_id = style.substr( 0, style.length - 1 );
            
            $('#selected_job_no').val( job_no );
            $('#selected_job_id').val( job_id );
        }

        function dynamic_ttl_change(data)
        {
            var titles="";
            if(data==1)
            {
                titles="Job No";
            }
            else if(data==2)
            {
                titles="Style Ref."
            }
            else if(data==3)
            {
                titles="Po No.";
            }
            else if(data==4)
            {
                titles="Cut No.";
            }
            else
            {
                titles="Job No";
            }
            $("#dynamic_ttl").html(titles);
        }
    
    </script>

    </head>

    <body>
        <div align="center" style="width:100%;" >
            <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
                <table width="600" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
                    <thead>
                        
                        <tr>                     
                            <th width="150" class="must_entry_caption">Company Name</th>
                            <th width="130" class="">Buyer Name</th>
                            <th width="100">Search By</th>
                            <th width="100" id="dynamic_ttl">Job No</th>
                             <th>&nbsp;</th>
                        </tr>           
                    </thead>
                    <tr class="general">
                        <td>
                        <input type="hidden" id="selected_job_no">
                        <input type="hidden" id="selected_job_id"> 
                            <?
                            $search_by_arr=array(1=>"Job No",2=>"Style Ref.",3=>"Po No",4=>"Cut No");
                             echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $cbo_company_id order by company_name","id,company_name",1, "-- Select Company --", '',"load_drop_down( 'garments_ex_feactory_return_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td_popup' );" );

                             ?>
                        </td>
                        <td id="buyer_td_popup"><? asort($buyer_arrs);echo create_drop_down( "cbo_buyer_name", 130, $buyer_arrs,'', 1, "-- Select Buyer --" ); ?></td>
                        <td>
                            <? echo create_drop_down( "cbo_search_by", 100, $search_by_arr,'',1, "-- Select--", 1,"dynamic_ttl_change(this.value);" );
                            ?>
                            
                        </td>
                        <td><input type="text" style="width:100px" class="text_boxes"  name="txt_job_po_style_no" id="txt_job_po_style_no" /></td>
                        <input type="hidden" name="hidden_job_year" id="hidden_job_year" value="<? echo $job_year;?>">
                        
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_job_po_style_no').value+'_'+document.getElementById('hidden_job_year').value, 'search_by_popup_list_view', 'search_div', 'garments_ex_feactory_return_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;" /></td>
                    </tr>
                    
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

if($action=="search_by_popup_list_view")
{
    $data=explode('_',$data);
    if(!$data[0])
    {
        echo "Select Company Name !!";die;
    }
    $str_cond="";
    $str_cond.=($data[0])? " and a.company_name='$data[0]' " : "";
    $str_cond.=($data[1])? " and a.buyer_name='$data[1]' " : "";
    if($data[3])
    {
        if($data[2]==1)
        {
            $str_cond.= " and a.job_no_prefix_num='$data[3]'";

        }
        else if($data[2]==2)
        {
            $str_cond.= " and a.style_ref_no like '%$data[3]%'";

        }
        else if($data[2]==3)
        {
            $str_cond.= " and b.po_number like '%$data[3]%'";

        }
        else if($data[2]==4)
        {
            $str_cond.= " and c.cut_no like '%$data[3]%'";

        }
    }
    if($data[4])
    {
       if($db_type==2)
       {
         $str_cond.=" and to_char(a.insert_date,'YYYY')='$data[4]'";
       }
       else
       {
            $str_cond.=" and year(a.insert_date)='$data[4]'";
       }
    }

    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    $comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
    $arr=array (0=>$comp,1=>$buyer_arr);
    $sql= "SELECT a.id,b.po_number,a.job_no_prefix_num as job_no,a.style_ref_no,a.company_name,a.buyer_name,c.cut_no from wo_po_details_master a,wo_po_break_down b,pro_garments_production_mst c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 $str_cond  group by a.id,b.po_number,a.job_no_prefix_num,a.style_ref_no,a.company_name,a.buyer_name,c.cut_no";
     // echo $sql;die;         
    echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Style Ref. No, Po No, Cut No.", "120,100,100,100,140,140","740","290",0, $sql , "js_set_value", "job_no,style_ref_no,po_number,cut_no","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,job_no,style_ref_no,po_number,cut_no","",'','0,0,0,0,0,0','',1) ;
   exit(); 
}

if($action=="floor_popup")
{
    echo load_html_head_contents("Search By Popup", "../../../", 1, 1,'','','');
    extract($_REQUEST);
    //$im_data=explode('_',$data);
    //print_r ($im_data);
    ?>
    <script>
        
        var selected_id = new Array; var selected_name = new Array;
        
        function check_all_data()
        {
            var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
            tbl_row_count = tbl_row_count - 1;

            for( var i = 1; i <= tbl_row_count; i++ )
            {
                $('#tr_'+i).trigger('click'); 
            }
        }
        
        function toggle( x, origColor ) {
            var newColor = 'yellow';
            if ( x.style ) {
                x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
            }
        }
        
        function js_set_value( str ) {
            
            if (str!="") str=str.split("_");
             
            toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
             
            if( jQuery.inArray( str[1], selected_id ) == -1 ) {
                selected_id.push( str[1] );
                selected_name.push( str[2] );
                
            }
            else {
                for( var i = 0; i < selected_id.length; i++ ) {
                    if( selected_id[i] == str[1] ) break;
                }
                selected_id.splice( i, 1 );
                selected_name.splice( i, 1 );
            }
            var id = ''; var name = '';
            for( var i = 0; i < selected_id.length; i++ ) {
                id += selected_id[i] + ',';
                name += selected_name[i] + '*';
            }
            
            id = id.substr( 0, id.length - 1 );
            name = name.substr( 0, name.length - 1 );
            
            $('#hid_floor_id').val( id );
            $('#hid_floor_name').val( name );
        }
        
        function hidden_field_reset()
        {
            $('#hid_floor_id').val('');
            $('#hid_floor_name').val( '' );
            selected_id = new Array();
            selected_name = new Array();
        }
    </script>
    </head>
    <input type="hidden" name="hid_floor_id" id="hid_floor_id" />
    <input type="hidden" name="hid_floor_name" id="hid_floor_name" />
    <?  
    $sql = "select a.id,a.floor_name from lib_prod_floor a where a.status_active =1 and a.is_deleted=0 and a.company_id in ($cbo_company) and production_process=5 and status_active=1 group by a.id,a.floor_name order by a.id";
    //echo  $sql;
    
    echo create_list_view("tbl_list_search", "Floor Name", "200","250","320",0, $sql , "js_set_value", "id,floor_name", "", 1, "0,0,0", $arr , "floor_name", "",'setFilterGrid(\'tbl_list_search\',-1);','0,0,0','',1) ;
    
   exit(); 
}
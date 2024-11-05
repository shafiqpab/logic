<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$user_id=$_SESSION['logic_erp']['user_id'];
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');

if($action=="open_set_list_view")
{
  echo load_html_head_contents("Item Details","../../../", 1, 1, $unicode,'','');
  extract($_REQUEST);
  //echo $set_smv_id;
  ?>
  <script>

  var set_smv_id='<? echo $set_smv_id; ?>';
  function add_break_down_set_tr( i )
  {
    var unit_id= document.getElementById('unit_id').value;
    if(unit_id==1)
    {
      alert('Only One Item');
      return false;
    }
    var row_num=$('#tbl_set_details tr').length-1;
    if (row_num!=i)
    {
      return false;
    }

    if (form_validation('cboitem_'+i+'*txtsetitemratio_'+i,'Gmts Items*Set Ratio')==false)
    {
      return;
    }
    else
    {
      i++;

       $("#tbl_set_details tr:last").clone().find("input,select,a").each(function() {
        $(this).attr({
          'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
          'name': function(_, name) { return name + i },
          'value': function(_, value) { return value }
        });
        }).end().appendTo("#tbl_set_details");

        $('#cboitem_'+i).removeAttr("onChange").attr("onChange","check_duplicate("+i+",this.id);check_smv_set("+i+");check_smv_set_popup("+i+");");

        $('#txtsetitemratio_'+i).removeAttr("onChange").attr("onChange","calculate_set_smv("+i+")");
        $('#smv_'+i).removeAttr("onChange").attr("onChange","calculate_set_smv("+i+")");

        $('#increaseset_'+i).removeAttr("onClick").attr("onClick","add_break_down_set_tr("+i+")");
        $('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_delete_down_tr("+i+",'tbl_set_details')");
        $('#cboitem_'+i).val('');
        set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
        set_sum_value_smv( 'tot_smv_qnty', 'smvset_' );
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
      }
       set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
       set_sum_value_smv( 'tot_smv_qnty', 'smvset_' );
    }
  }

  function calculate_set_smv(i)
  {
    var txtsetitemratio=document.getElementById('txtsetitemratio_'+i).value;
    var smv=document.getElementById('smv_'+i).value;
    var set_smv=txtsetitemratio*smv;
    document.getElementById('smvset_'+i).value=set_smv;
    set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
    set_sum_value_smv( 'tot_smv_qnty', 'smvset_' );
  }

  function set_sum_value_set(des_fil_id,field_id)
  {
    var rowCount = $('#tbl_set_details tr').length-1;
    math_operation( des_fil_id, field_id, '+', rowCount );
  }

  function set_sum_value_smv(des_fil_id,field_id)
  {
    var rowCount = $('#tbl_set_details tr').length-1;
    var ddd={ dec_type:1, comma:0, currency:1}
    math_operation( des_fil_id, field_id, '+', rowCount,ddd );
    //math_operation( des_fil_id, field_id, '+', rowCount );
  }

  function js_set_value_set()
  {
    var rowCount = $('#tbl_set_details tr').length-1;
    var set_breck_down="";
    var item_id=""
    for(var i=1; i<=rowCount; i++)
    {
      if (form_validation('cboitem_'+i+'*txtsetitemratio_'+i+'*smv_'+i,'Gmts Items*Set Ratio*Smv')==false)
      {
        return;
      }
      if($('#hidquotid_'+i).val()=='') $('#hidquotid_'+i).val(0);
      if(set_breck_down=="")
      {
        set_breck_down+=$('#cboitem_'+i).val()+'_'+$('#txtsetitemratio_'+i).val()+'_'+$('#smv_'+i).val()+'_'+$('#smvset_'+i).val()+'_'+$('#hidquotid_'+i).val();
        item_id+=$('#cboitem_'+i).val();
      }
      else
      {
        set_breck_down+="__"+$('#cboitem_'+i).val()+'_'+$('#txtsetitemratio_'+i).val()+'_'+$('#smv_'+i).val()+'_'+$('#smvset_'+i).val()+'_'+$('#hidquotid_'+i).val();
        item_id+=","+$('#cboitem_'+i).val();
      }

    }
    document.getElementById('set_breck_down').value=set_breck_down;
    document.getElementById('item_id').value=item_id;

    parent.emailwindow.hide();
  }

  function check_duplicate(id,td)
  {
    var item_id=(document.getElementById('cboitem_'+id).value);
    var row_num=$('#tbl_set_details tr').length-1;
    for (var k=1;k<=row_num; k++)
    {
      if(k==id)
      {
        continue;
      }
      else
      {
        if(item_id==document.getElementById('cboitem_'+k).value)
        {
          alert("Same Gmts Item Duplication Not Allowed.");
          document.getElementById(td).value="0";
          document.getElementById(td).focus();
        }
      }
    }
  }

  function check_smv_set(id)
  {
    var smv=(document.getElementById('smv_'+id).value);
    var row_num=$('#tbl_set_details tr').length-1;
    //alert(item_id);
    var txt_style_ref='<? echo $txt_style_ref ?>';

    var item_id=$('#cboitem_'+id).val();
    //alert(td);
    //get_php_form_data(company_id,'set_smv_work_study','requires/style_ref_controller' );
    var response=return_global_ajax_value(txt_style_ref+"**"+item_id, 'set_smv_work_study', '', 'buyer_inquiry_sweater_controller');
    var response=response.split("_");
    if(response[0]==1)
    {
      if(set_smv_id==1)
      {
        $('#smv_'+id).val(response[1]);
        $('#tot_smv_qnty').val(response[1]);
        /*for (var k=1;k<=row_num; k++)
        {
          $('#smv_'+k).val(response[1]);
        }*/
      }
    }
  }

  function check_smv_set_popup(id)
  {
    var smv=(document.getElementById('smv_'+id).value);
    var row_num=$('#tbl_set_details tr').length-1;

    var txt_style_ref='<? echo $txt_style_ref ?>';
    var cbo_company_name='<? echo $cbo_company_name ?>';
    var cbo_buyer_name='<? echo $cbo_buyer_name ?>';
    var item_id=$('#cboitem_'+id).val();
      //alert(set_smv_id);
    if(set_smv_id==4 || set_smv_id==6)
    {
      $('#smv_'+id).val('');
      $('#tot_smv_qnty').val('');
      $('#hidquotid_'+id).val('');

      var page_link="buyer_inquiry_sweater_controller.php?action=open_smv_list&txt_style_ref="+txt_style_ref+"&set_smv_id="+set_smv_id+"&item_id="+item_id+"&id="+id+"&cbo_company_name="+cbo_company_name+"&cbo_buyer_name="+cbo_buyer_name;
    }
    else
    {
      return;
    }

    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'SMV Pop Up', 'width=650px,height=220px,center=1,resize=1,scrolling=0','../../')
    emailwindow.onclose=function()
    {
      var theform=this.contentDoc.forms[0];
      var selected_smv_data=this.contentDoc.getElementById("selected_smv").value;
      var smv_data=selected_smv_data.split("_");
      var row_id=smv_data[3];

      $("#smv_"+row_id).val(smv_data[0]);
      $("#smv_"+row_id).attr('readonly','readonly');
      $("#hidquotid_"+row_id).val(smv_data[4]);

      calculate_set_smv(row_id);
    }
  }
  </script>
  </head>
  <body>
         <div id="set_details"  align="center">
        <fieldset>
            <form id="setdetails_1" autocomplete="off">
              <input type="hidden" id="set_breck_down" />
              <input type="hidden" id="item_id" />
              <input type="hidden" id="unit_id" value="<? echo $unit_id;  ?>" />

              <table width="800" cellspacing="0" class="rpt_table" border="0" id="tbl_set_details" rules="all">
                    <thead>
                        <tr>
                            <th width="250">Item</th><th width="80">Item Ratio</th><th width="80">SMV/Pcs</th><th width=""></th>
                          </tr>
                      </thead>
                      <tbody>
                      <?

            $data_array=explode("__",$set_breck_down);
            if($data_array[0]=="")
            {
              $data_array=array();
            }
            if ( count($data_array)>0)
            {
              $i=0;
              foreach( $data_array as $row )
              {
                $i++;
                $data=explode('_',$row);
                $gmt_item_id_s=$data[0];
                if(empty($gmt_item_id_s))
                {
                  $gmt_item_id_s=$item_id;
                }
                

                ?>
                    <tr id="settr_1" align="center">
                          <td>
                          <?
                          echo create_drop_down( "cboitem_".$i, 250, get_garments_item_array(100), "",1," -- Select Item --", $gmt_item_id_s, "check_duplicate(".$i.",this.id ); check_smv_set(".$i."); check_smv_set_popup(".$i.");",'','' );
                          ?>

                          </td>
                          <td>
                          <input type="text" id="txtsetitemratio_<? echo $i;?>"   name="txtsetitemratio_<? echo $i;?>" style="width:70px"  class="text_boxes_numeric" onChange="calculate_set_smv(<? echo $i;?>)"  value="<? echo $data[1] ?>" <? if ($unit_id==1){echo "readonly";} else{echo "";}?> />
                          </td>

                         <td>
                          <input type="text" id="smv_<? echo $i;?>"   name="smv_<? echo $i;?>" style="width:70px"  class="text_boxes_numeric" onChange="calculate_set_smv(<? echo $i;?>)"  value="<? echo $data[2] ?>" />
                          <input type="hidden" id="smvset_<? echo $i;?>"   name="smvset_<? echo $i;?>" style="width:70px"  class="text_boxes_numeric"  value="<? echo $data[3] ?>" />
                          </td>
                          <td>
                          <input type="hidden" id="hidquotid_<? echo $i;?>" name="hidquotid_<? echo $i;?>" style="width:30px" class="text_boxes_numeric" value="<? echo $data[4]; ?>" readonly/>
                          <input type="button" id="increaseset_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_set_tr(<? echo $i; ?> )" />
                          <input type="button" id="decreaseset_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_delete_down_tr(<? echo $i; ?> ,'tbl_set_details' );" />
                           </td>
                      </tr>
                  <?
              }
            }
            else
            {
               //$sql=sql_select("select a.id,a.item_name from sample_development_mst a,sample_development_dtls b where  a.quotation_id='$txt_inquery_id' and  a.id=b.sample_mst_id");

               $item_name = return_field_value("item_name" ," sample_development_mst","quotation_id='$txt_inquery_id'");
               $gmt_item_id_s=$item_name;
              if(empty($gmt_item_id_s))
              {
                $gmt_item_id_s=$item_id;
              }

              ?>
              <tr id="settr_1" align="center">
                     <td>
                      <?
                      echo create_drop_down( "cboitem_1", 240, get_garments_item_array(100), "",1,"--Select--", $gmt_item_id_s, 'check_duplicate(1,this.id ); check_smv_set(1); check_smv_set_popup(1);','','' );
                      ?>
                      </td>
                       <td>
                      <input type="text" id="txtsetitemratio_1" name="txtsetitemratio_1" style="width:70px" class="text_boxes_numeric" onChange="calculate_set_smv(1)" value="<? if ($unit_id==1) {echo "1";} else{echo "";}?>"  <? if ($unit_id==1){echo "readonly";} else{echo "";}?>  />
                       </td>
                       <td>
                      <input type="text" id="smv_1"   name="smv_1" style="width:70px"  class="text_boxes_numeric" onChange="calculate_set_smv(1)"  value="<? //echo $smv_pcs_precost; ?>" />
                      <input type="hidden" id="smvset_1"   name="smvset_1" style="width:70px"  class="text_boxes_numeric"  value="<? //echo $smv_set_precost; ?>" />
                      </td>
                      <td>
                      <input type="hidden" id="hidquotid_1" name="hidquotid_1" style="width:30px" class="text_boxes_numeric" value="" readonly/>
                      <input type="button" id="increaseset_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_set_tr(1)" />
                      <input type="button" id="decreaseset_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_delete_down_tr(1 ,'tbl_set_details' );" />
                      </td>
                </tr>
              <?
            }
            ?>
                  </tbody>
                  </table>
                  <table width="800" cellspacing="0" class="rpt_table" border="0" rules="all">
                  <tfoot>
                        <tr>
                              <th width="250">Total</th>
                              <th  width="80"><input type="text" id="tot_set_qnty" name="tot_set_qnty"  class="text_boxes_numeric" style="width:70px"  value="<? if($tot_set_qnty !=''){ echo $tot_set_qnty;} else{ echo 1;} ?>" readonly  /></th>
                                <th  width="80">
                                  <input type="text" id="tot_smv_qnty" name="tot_smv_qnty" class="text_boxes_numeric" style="width:70px"  value="<? //if($tot_smv_qnty !=''){ echo $tot_smv_qnty;} else{ echo 1;} ?>" readonly />
                              </th>
                              <th width=""></th>
                          </tr>
                      </tfoot>
                  </table>

                  <table width="800" cellspacing="0" class="" border="0">

                    <tr>
                          <td align="center" height="15" width="100%"> </td>
                      </tr>
                    <tr>
                          <td align="center" width="100%" class="button_container">

                      <input type="button" class="formbutton" value="Close" onClick="js_set_value_set()"/>

                          </td>
                      </tr>
                  </table>

              </form>
          </fieldset>
          </div>
   </body>
  <script>
    set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
    set_sum_value_smv( 'tot_smv_qnty', 'smvset_' );
  </script>
  <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
  </html>
  <?
  exit();
}

if($action=="open_smv_list")
{
  echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
  extract($_REQUEST);

  $item_id=$item_id;
  $style_id=$txt_style_ref;
  $set_smv_id=$set_smv_id;
  $row_id=$id;
  $set_smv_id=$set_smv_id;
  $cbo_buyer_name=$cbo_buyer_name;
  $cbo_company_name=$cbo_company_name;
  //echo $cbo_company_name;
  ?>
  <script type="text/javascript">
      function js_set_value(id)
      {   //alert(id);
      document.getElementById('selected_smv').value=id;
      parent.emailwindow.hide();
      }
    </script>

    </head>
    <body>
    <div align="center" style="width:100%;" >
  <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="400" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <th width="150">Buyer Name</th>
                <th width="100">Style Ref </th>
                <th>
                    <input type="hidden" id="selected_job">
                    <input type="hidden" id="item_id" value="<?  echo $item_id;?>">
                    <input type="hidden" id="row_id" value="<?  echo $row_id;?>">
                    <input type="hidden" id="company_id" value="<?  echo $cbo_company_name;?>">
                &nbsp;</th>
            </thead>
            <tr>
                <td id=""><? echo create_drop_down( "cbo_buyer_name", 172, "select id,buyer_name from lib_buyer  where status_active =1 and is_deleted=0 order by buyer_name",'id,buyer_name', 1, "-- Select Buyer --",$cbo_buyer_name,"",1 ); ?></td>
                <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:100px" value="<? echo $txt_style_ref;?>" disabled></td>
                <td align="center">
                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('company_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('item_id').value+'_'+document.getElementById('row_id').value, 'create_item_smv_search_list_view', 'search_div', 'buyer_inquiry_sweater_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
            </tr>
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

if($action=="create_item_smv_search_list_view")
{
  $data=explode('_',$data);
  $company=$data[0];
  $buyer_id=$data[1];
  $style=$data[2];
  $item_id=$data[3];
  $row_id=$data[4];

  //if ($company!=0) $company_con=" and a.company_id='$company'";else $company_con="";
  if ($buyer_id!=0) $buyer_id_con=" and a.buyer_id='$buyer_id'";else $buyer_id_con="";
  if ($style!="") $style_con=" and a.style_ref ='$style'";else $style_con="";
  if ($item_id!=0) $gmts_item_con=" and a.gmts_item_id='$item_id'";else $gmts_item_con="";
  if ($item_id!=0) $gmts_item_con2=" and a.gmt_item_id='$item_id'";else $gmts_item_con2="";
  ?>
  <input type="hidden" id="selected_smv" name="selected_smv" />
  <?
  $sewing_sql="select a.id as lib_sewing_id, a.gmt_item_id, a.bodypart_id, a.operation_name, a.department_code as dcode from lib_sewing_operation_entry a where a.is_deleted=0 $gmts_item_con2  order by a.id Desc";
  $result = sql_select($sewing_sql);
  foreach($result as $row)
  {
    $code_smv_arr[$row[csf('lib_sewing_id')]]['dcode']=$row[csf('dcode')];
    $code_smv_arr[$row[csf('lib_sewing_id')]][$row[csf('bodypart_id')]]['operation_name']=$row[csf('operation_name')];
  }
  // print_r($code_smv_arr);b.lib_sewing_id
  if($db_type==0)
  {
    $group_con="group_concat(b.lib_sewing_id)  as lib_sewing_id";
    $id_group_con="group_concat(a.id)";
  }
  else
  {
    $group_con="listagg(b.lib_sewing_id,',') within group (order by b.lib_sewing_id) as lib_sewing_id";
    $id_group_con="listagg(a.id,',') within group (order by a.id)";
  }

  $sql="select a.id, a.style_ref, a.operation_count, a.gmts_item_id, b.operator_smv, b.helper_smv, b.body_part_id, b.lib_sewing_id from ppl_gsd_entry_mst a, ppl_gsd_entry_dtls b where a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 $gmts_item_con $style_con $buyer_id_con
  order by id DESC";

  $sql_result=sql_select($sql);
  foreach($sql_result as $row)
  {
    //$operation_name=$code_smv_arr[$row[csf('lib_sewing_id')]][$row[csf('body_part_id')]]['operation_name'];
    $smv_dtls_arr['str']['style_ref']=$row[csf('style_ref')];
    $smv_dtls_arr['str']['operation_count']=$row[csf('operation_count')];
    $smv_dtls_arr['str']['id'].=$row[csf('id')].',';
    //$smv_dtls_arr[$row[csf('id')]]['gmts_item_id']=$row[csf('gmts_item_id')];
    $smv_dtls_arr['str']['lib_sewing_id'].=$row[csf('lib_sewing_id')].',';
    //$smv_dtls_arr[$row[csf('id')]]['body_part_id']=$row[csf('body_part_id')];
    //$smv_dtls_arr[$row[csf('id')]]['operation_name']=$operation_name;
    $code_id=$code_smv_arr[$row[csf('lib_sewing_id')]]['dcode'];
    $smv=0;
    $smv=$row[csf('operator_smv')]+$row[csf('helper_smv')];

    $smv_sewing_arr[$code_id][$row[csf('lib_sewing_id')]]['operator_smv']+=$smv;
  }
  //print_r($smv_dtls_arr);
  ?>
  <table width="600" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table " >
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="100">Sys. ID.</th>
                <th width="200">Style</th>
                <th width="60">Avg. Sewing SMV</th>
                <th width="60">Avg. Cuting SMV</th>
                <th width="60">Avg. Finish SMV</th>
                <th>No of Operation</th>
            </tr>
        </thead>
        <tbody id="list_view">
        <?
        $i=1;
    foreach($smv_dtls_arr as $arrdata)
    {
      if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
      $lib_sewing_id=rtrim($arrdata['lib_sewing_id'],',');
      $lib_sewing_ids=array_unique(explode(",",$lib_sewing_id));

      $finish_smv=$cut_smv=$sewing_smv=0;
      foreach($lib_sewing_ids as $lsid)
      {
        $finish_smv+=$smv_sewing_arr[4][$lsid]['operator_smv'];
        $cut_smv+=$smv_sewing_arr[7][$lsid]['operator_smv'];
        $sewing_smv+=$smv_sewing_arr[8][$lsid]['operator_smv'];
      }
      $sys_id=rtrim($arrdata['id'],',');
      $ids=array_filter(array_unique(explode(",",$sys_id)));
      //print_r($ids);
      $id_str=""; $k=0;
      foreach($ids as $idstr)
      {
        if($id_str=="") $id_str=$idstr; else $id_str.=','.$idstr;
        $k++;
      }
      $finish_smv=$finish_smv/$k;
      $cut_smv=$cut_smv/$k;
      $sewing_smv=$sewing_smv/$k;

      $data=$sewing_smv."_".$cut_smv."_".$finish_smv."_".$row_id."_".$id_str;
      ?>
      <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" style="cursor:pointer" onClick="js_set_value('<? echo $data; ?>')">
                <td width="30"><? echo $i;//.'='.$k ?></td>
                <td width="140" style="word-break:break-all"><? echo $id_str; ?></td>
                <td width="160" style="word-break:break-all"><? echo $arrdata['style_ref']; ?></td>
                <td width="60" align="right"><p><? echo number_format($sewing_smv,2); ?></p></td>
                <td width="60" align="right"><p><? echo number_format($cut_smv,2); ?></p></td>
                <td width="60" align="right"><p><? echo number_format($finish_smv,2); ?></p></td>
                <td><p><? echo $arrdata['operation_count']; ?></p></td>
      </tr>
      <?
      $i++;
    }
        ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3">&nbsp; </th>
                <th>&nbsp; </th>
                <th>&nbsp; </th>
                <th>&nbsp; </th>
                <th>&nbsp; </th>
            </tr>
        </tfoot>
  </table>
  <?
  exit();
}

if($action=="open_yarn_count_list_view")
{
	echo load_html_head_contents("Yarn Count Info", "../../../", 1, 1, '', '1', '');
	extract($_REQUEST);
	?>
	<script>

		$(document).ready(function (e) {
			setFilterGrid('tbl_list_search', -1);
		});

		var selected_id = new Array();
		var selected_name = new Array();

		function check_all_data() {
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;

			tbl_row_count = tbl_row_count - 1;
			for (var i = 1; i <= tbl_row_count; i++) {
				js_set_value(i);
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
			}
		}

		function set_all() {
			var old = document.getElementById('txt_yarn_count_row_id').value;
			//alert(old);
			if (old != "") {
				old = old.split(",");
				
				for (var k = 0; k < old.length; k++) {
					js_set_value(old[k]);
				}
			}
		}

	function js_set_value(str) {
         toggle(document.getElementById('search' + str), '#FFFFCC');

         if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1) {
         	selected_id.push($('#txt_individual_id' + str).val());
         	selected_name.push($('#txt_individual' + str).val());
         }
         else {
         	for (var i = 0; i < selected_id.length; i++) {
         		if (selected_id[i] == $('#txt_individual_id' + str).val()) break;
         	}
         	selected_id.splice(i, 1);
         	selected_name.splice(i, 1);
         }

         var id = '';
         var name = '';
         for (var i = 0; i < selected_id.length; i++) {
         	id += selected_id[i] + ',';
         	name += selected_name[i] + ',';
         }

         id = id.substr(0, id.length - 1);
         name = name.substr(0, name.length - 1);

         $('#hidden_yarn_count_id').val(id);
         $('#hidden_yarn_count_name').val(name);
     }

    function window_close(){
		parent.emailwindow.hide();
	}

	</script>

</head>

<body>
	<div align="center">
		<fieldset style="width:230px;margin-left:10px">
			<input type="hidden" name="hidden_yarn_count_id" id="hidden_yarn_count_id" class="text_boxes" value="">
			<input type="hidden" name="hidden_yarn_count_name" id="hidden_yarn_count_name" class="text_boxes" value="">
			<form name="searchprocessfrm_1" id="searchprocessfrm_1" autocomplete="off">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="220" class="rpt_table">
					<thead>
						<th width="50">SL</th>
						<th>Yarn Count</th>
					</thead>
				</table>
				<div style="width:220px; overflow-y:scroll; max-height:345px;" id="buyer_list_view" align="center">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="200" class="rpt_table" id="tbl_list_search">
				<?
				$i = 1;
				//echo $yarn_count_id;
				$yarn_countArr=sql_select("select ID, YARN_COUNT from lib_yarn_count where status_active=1 and is_deleted=0 order by YARN_COUNT");
				$hidden_yarncount_id = explode(",", $yarn_count_id); $yarn_count_row_id="";
					foreach ($yarn_countArr as $row) {
						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
	
						if (in_array($row['ID'], $hidden_yarncount_id)) {
							if($yarn_count_row_id == "") $yarn_count_row_id = $i; else $yarn_count_row_id .= "," . $i;
						}
							
						?>
						<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i; ?>" onClick="js_set_value(<?=$i; ?>);">
							<td width="50" align="center"><?=$i; ?>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<?=$i; ?>" value="<?=$row['ID']; ?>"/>
							<input type="hidden" name="txt_individual" id="txt_individual<?=$i; ?>" value="<?=$row['YARN_COUNT']; ?>"/>
						</td>
						<td style="word-break:break-all"><p><?=$row['YARN_COUNT']; ?></p></td>
					</tr>
					<?
					$i++;
				}
				?>
				<input type="hidden" name="txt_yarn_count_row_id" id="txt_yarn_count_row_id" value="<?=$yarn_count_row_id; ?>"/>
			</table>
		</div>
		<table width="220" cellspacing="0" cellpadding="0" style="border:none" align="center">
			<tr>
				<td align="center" valign="bottom">
                    <div style=" float:left" align="left">
                        <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data();"/>
                        Check / Uncheck All
                    </div>
				</td>
			</tr>
            <tr>
				<td align="center" height="30" valign="bottom">
                    <div>
                        <input type="button" name="close" onClick="window_close();" class="formbutton" value="Close" style="width:100px"/>
                    </div>
				</td>
			</tr>
		</table>
	</form>
</fieldset>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	set_all();
</script>
</html>
<?
exit();
}

if($action=="generate_cad_la_consting")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
		<script>
			function js_set_value(mrr)
			{
		 		$("#hidden_system_number").val(mrr);
				parent.emailwindow.hide();
			}
		</script>
	</head>

	<body>
	<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="500" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
        <thead>
            <tr>
                <th colspan="8" style="display: none;"><? echo create_drop_down( "cbo_string_search_type", 160, $string_search_type,'', 1, "--Searching Type--" ); ?></th>
            </tr>
            <tr>
                <th width="150" class="must_entry_caption">Company Name</th>
                <th width="150">Buyer Name</th>
                <th width="100">System NO.</th>
                <th width="150" >Master Style Ref</th>
                <th width="100">Costing Date </th>
                <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:80px" class="formbutton"  /></th>
            </tr>
        </thead>
        <tbody>
            <tr class="general">
                <td><? echo create_drop_down( "cbo_company_name", 150, "select comp.id,comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company, "load_drop_down( 'consumption_la_costing_controller', this.value, 'load_drop_down_buyer_popup', 'buyer_td' );"); ?></td>
                <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ); ?></td>
                <td><input type="text" style="width:80px" class="text_boxes"  name="txt_system_no" id="txt_system_no" /></td>
                <td><input type="text" style="width:120px" class="text_boxes"  name="txt_master_style" id="txt_master_style" /></td>
                <!-- <td><? echo create_drop_down( "cbo_year", 70, $year,"", 1, "- Select- ", date('Y'), "" ); ?></td>                
                <td><input type="text" style="width:80px" class="text_boxes"  name="txt_requst_no" id="txt_requst_no" /></td> -->
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="Date" /></td>
                <td><input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_system_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_master_style').value, 'create_consumption_search_list_view', 'search_div', 'consumption_la_costing_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
                </td>
            </tr>
            <tr>
                <td align="center" valign="middle" colspan="7"><input type="hidden" id="hidden_system_number" value="" /></td>
            </tr>
        </tbody>
    </table>
    <div align="center" valign="top" id="search_div"> </div>
    </form>
	</div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action == "populate_data_from_consumption")
{
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
	$brand_arr=return_library_array("select id,brand_name from lib_buyer_brand","id","brand_name");
	$fabrication_arr=return_library_array("select id,fab_composition from lib_yarn_count_determina_mst","id","fab_composition");
	$system_data = sql_select("SELECT  a.id as system_id, a.system_no, a.inquiry_id, a.la_costing_date, a.merch_style, a.style_des, a.pattern_master, a.bom_no, a.comments, b.company_id, b.buyer_id, b.season_buyer_wise, b.season_year, b.brand_id, b.style_refernce, b.fabrication,b.offer_qty from consumption_la_costing_mst a join wo_quotation_inquery b on a.inquiry_id=b.id where a.id='$data' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id");
	foreach ($system_data as $row) {		
		echo "document.getElementById('txt_system_id').value = '".$row[csf("system_no")]."';\n";
		echo "document.getElementById('inquery_id').value = '".$row[csf("inquiry_id")]."';\n";
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("style_refernce")]."';\n";
		echo "document.getElementById('txt_buyer_name').value = '".$buyer_arr[$row[csf("buyer_id")]]."';\n";
		echo "document.getElementById('txt_season').value = '".$season_arr[$row[csf("season_buyer_wise")]]."';\n";
		echo "document.getElementById('txt_season_year').value = '".$row[csf("season_year")]."';\n";
		echo "document.getElementById('txt_brand_name').value = '".$brand_arr[$row[csf("brand_id")]]."';\n";
		echo "document.getElementById('txt_fabrication').value = '".$fabrication_arr[$row[csf("fabrication")]]."';\n";
		// echo "document.getElementById('txt_fabrication').value = '".$row[csf("fabrication")]."';\n";
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";		
		echo "document.getElementById('txt_consumption_date').value = '".change_date_format($row[csf("la_costing_date")],"dd-mm-yyyy","-")."';\n";
		echo "document.getElementById('txt_merch_style').value = '".$row[csf("merch_style")]."';\n";
		echo "document.getElementById('txt_style_desc').value = '".$row[csf("style_des")]."';\n";
		echo "document.getElementById('txt_pattern_master').value = '".$row[csf("pattern_master")]."';\n";
		echo "document.getElementById('txt_bom_no').value = '".$row[csf("bom_no")]."';\n";
		echo "document.getElementById('txt_comments').value = '".$row[csf("comments")]."';\n";
		echo "document.getElementById('txt_offer_qty').value = '".$row[csf("offer_qty")]."';\n";
		echo "document.getElementById('update_id').value = '".$row[csf("system_id")]."';\n";
		echo "$('#txt_style_ref').attr('disabled',true);\n";
		//echo "$('#txt_style_ref').attr('ondblclick', '').unbind('click');\n";
		echo "$('#txt_style_ref').removeAttr('ondblclick');\n";
	}
}

if($action == "create_consumption_search_list_view")
{
	$ex_data = explode("_",$data);
	$txt_buyer = $ex_data[0];
	$system_no = $ex_data[1];
	$system_date = $ex_data[2];
	$company = $ex_data[3];
	$master_style = $ex_data[5];

	if($company==0) $company_name_cond=""; else $company_name_cond=" and a.company_id=$company";
	if($txt_buyer==0) $buyer_name_cond=""; else $buyer_name_cond="and b.buyer_id=$txt_buyer";
	if($master_style=='') $master_style_cond=""; else $master_style_cond="and b.style_refernce like '%".$master_style."%' ";
	if( $system_date!="" )  $system_date_cond.= " and a.la_costing_date='".change_date_format($inq_date,'yyyy-mm-dd',"-",1)."'";
	if(str_replace("'","",$system_no)!="")  $system_no_cond="and  system_no_prefix_num like '%".str_replace("'","",$system_no)."' ";
	$season_buyer_wise_arr = return_library_array("select id,season_name from  lib_buyer_season ","id","season_name");
	$buyer_arr = return_library_array("select id,buyer_name from  lib_buyer ","id","buyer_name");
	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	$arr=array(0=>$company_arr,1=>$buyer_arr,5=>$season_buyer_wise_arr);
	$sql = "SELECT a.id as system_id, a.company_id, a.system_no, a.la_costing_date, b.style_refernce, b.buyer_id, b.brand_id, b.season_buyer_wise from consumption_la_costing_mst a join wo_quotation_inquery b on a.inquiry_id=b.id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name_cond $buyer_name_cond $system_date_cond $system_no_cond $master_style_cond";
	echo create_list_view("list_view", "Company Name, Buyer Name, System NO, Master Style Ref., Costing Date, Season","150,120,120,100,90,60","720","260",0, $sql , "js_set_value", "system_id", "", 1, "company_id,buyer_id,0,0,0,season_buyer_wise", $arr, "company_id,buyer_id,system_no,style_refernce,la_costing_date,season_buyer_wise", "",'','0') ;
	?>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="generate_buyer_inquery")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
		<script>
			function js_set_value(mrr)
			{
		 		$("#hidden_issue_number").val(mrr);
				parent.emailwindow.hide();
			}
		</script>
	</head>

	<body>
	<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="950" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
        <thead>
            <tr>
                <th colspan="10"><? echo create_drop_down( "cbo_string_search_type", 160, $string_search_type,'', 1, "--Searching Type--" ); ?></th>
            </tr>
            <tr>
                <th width="150" class="must_entry_caption">Company Name</th>
                <th width="150">Buyer Name</th>
                <th width="90">Brand</th>
                <th width="70">Inquiry ID</th>
                <th width="60">Year</th>
                <th width="90">Season</th>
                <th width="100">Style Ref.</th>
                <th width="70">BOM</th>
                <th width="70">Inquiry Date </th>
                <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:70px" class="formbutton" /><input type="hidden" id="hidden_issue_number" value="" /></th>
            </tr>
        </thead>
        <tbody>
            <tr class="general">
                <td><? echo create_drop_down( "cbo_company_name", 150, "select comp.id,comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company, "load_drop_down( 'buyer_inquiry_sweater_controller', this.value, 'load_drop_down_buyer_popup', 'buyer_td');"); ?></td>
                <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ); ?></td>
                <td id="brand_td"><? echo create_drop_down( "cbo_brand_id", 90, $blank_array,"", 1, "Select Brand", $selected, "" ); ?></td>  
                <td><input type="text" style="width:60px" class="text_boxes"  name="txt_inquery_id" id="txt_inquery_id" /></td>
                <td><? echo create_drop_down( "cbo_year", 60, $year,"", 1, "- Select- ", date('Y'), "" ); ?></td>
                <td id="season_td"><? echo create_drop_down( "cbo_season_id", 90, $blank_array,'', 1, "Select Season",$selected, "" ); ?></td>
                <td><input type="text" style="width:90px" class="text_boxes"  name="txt_style" id="txt_style" /></td>
                <td><input type="text" style="width:60px" class="text_boxes"  name="txt_bom" id="txt_bom" /></td>
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="Date" /></td>
                <td><input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_inquery_id').value+'_'+document.getElementById('cbo_year').value+'_'+document.getElementById('cbo_brand_id').value+'_'+document.getElementById('cbo_season_id').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_bom').value, 'create_inquery_search_list_view', 'search_div', 'buyer_inquiry_sweater_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
                </td>
            </tr>
        </tbody>
    </table>
    <div align="center" valign="top" id="search_div"> </div>
    </form>
	</div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_inquery_search_list_view")
{
	$ex_data = explode("_",$data);
	$txt_buyer = $ex_data[0];
	$txt_style = $ex_data[1];
	$inq_date = $ex_data[2];
	$company = $ex_data[3];
	$brand = $ex_data[6];
	$sesson = $ex_data[7];
	$bom = $ex_data[9];
    if($company==0) $company_name=""; else $company_name=" and company_id=$company";
	if($txt_buyer==0) $buyer_name=""; else $buyer_name="and buyer_id=$txt_buyer";
	if($brand==0) $brand_cond=""; else $buyer_name="and brand_id=$brand";
	if($bom=="") $bom_con=""; else $bom_con=" and bom_no='$bom' ";
	if($sesson==0) $season_cond=""; else $buyer_name="and season_buyer_wise=$sesson";
	if($db_type==0) $year_cond=" and SUBSTRING_INDEX(`insert_date`, '-', 1)=$ex_data[5]";
	if($db_type==2) $year_cond=" and to_char(insert_date,'YYYY')=$ex_data[5]";
	if( $inq_date!="" )  $inquery_date.= " and inquery_date='".change_date_format($inq_date,'yyyy-mm-dd',"-",1)."'";

	$sql_cond=''; $inquery_id_cond=''; $request_no='';
	if($ex_data[8]==1)
	{
	   if(str_replace("'","",$txt_style)!="")  $sql_cond="and  style_refernce='".str_replace("'","",$txt_style)."'";
	   if (trim($ex_data[4])!="")  $inquery_id_cond=" and system_number_prefix_num='$ex_data[4]'  $year_cond";
	}
	else if($ex_data[8]==4 || $ex_data[8]==0)
	{
	  if(str_replace("'","",$txt_style)!="")  $sql_cond="and  style_refernce like '%".str_replace("'","",$txt_style)."%' ";
	  if (trim($ex_data[4])!="") $inquery_id_cond=" and system_number_prefix_num like '%$ex_data[4]%' $year_cond";
	}
	else if($ex_data[8]==2)
	{
	  if(str_replace("'","",$txt_style)!="")  $sql_cond="and  style_refernce like '".str_replace("'","",$txt_style)."%' ";
	  if (trim($ex_data[4])!="") $inquery_id_cond=" and system_number_prefix_num like '$ex_data[4]%' $year_cond";
	}
	else if($ex_data[8]==3)
	{
	  if(str_replace("'","",$txt_style)!="")  $sql_cond="and  style_refernce like '%".str_replace("'","",$txt_style)."' ";
	  if (trim($ex_data[4])!="") $inquery_id_cond=" and system_number_prefix_num like '%$ex_data[4]' $year_cond";
	}
	$season_buyer_wise_arr = return_library_array("select id,season_name from  lib_buyer_season ","id","season_name");
	$buyer_arr = return_library_array("select id,buyer_name from  lib_buyer ","id","buyer_name");
	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	$brand_arr=return_library_array("select id,brand_name from lib_buyer_brand","id","brand_name");
	$arr=array(0=>$company_arr,1=>$buyer_arr,2=>$brand_arr,8=>$season_buyer_wise_arr);
	 $sql = "select system_number_prefix_num,system_number,buyer_request, brand_id,company_id,buyer_id,season_buyer_wise,inquery_date,style_refernce,status_active,extract(year from insert_date) as year ,id,bom_no from wo_quotation_inquery where is_deleted=0 and entry_form=457 $company_name $buyer_name $sql_cond $inquery_id_cond $request_no $inquery_date $year_cond $brand_cond $season_cond $bom_con order by id DESC ";
	 //echo $sql;
	echo create_list_view("list_view", "Company Name,Buyer Name,Brand Name,Inquiry ID,Year,Style Ref.,BOM,Inquiry Date,Season","110,120,70,100,50,120,80,90,70,70","940","320",0, $sql , "js_set_value", "id", "", 1, "company_id,buyer_id,brand_id,0,0,0,0,0,season_buyer_wise", $arr, "company_id,buyer_id,brand_id,system_number,year,style_refernce,bom_no,inquery_date,season_buyer_wise", "",'','0,0,0,0,0,0,0,3');
	?>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="populate_data_from_data")
{
	$sql = sql_select("select id, company_id, system_number, buyer_id, season_buyer_wise, season_year, brand_id, inquery_date, style_refernce, actual_sam_send_date,  actual_req_quot_date, buyer_request, status_active, remarks, dealing_marchant, gmts_item, est_ship_date, fabrication, offer_qty, color, color_id, req_quotation_date, target_sam_sub_date, product_dept, buyer_target_price, buyer_submit_price, priority, con_rec_target_date, cutable_width, style_description, gauge, bom_no, no_of_ends, order_uom, set_break_down, total_set_qnty, set_smv, yarn_count_id from wo_quotation_inquery where id='$data' and entry_form=457 order by id");
	$composition_arr=array();
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	$sql_q="select mst_id, copmposition_id, percent, count_id, type_id from lib_yarn_count_determina_dtls where is_deleted=0 order by id";
						
	$data_array=sql_select($sql_q);
	if (count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('mst_id')],$composition_arr))
			{
				$composition_arr[$row[csf('mst_id')]]=$composition_arr[$row[csf('mst_id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].", ";
			}
			else
			{
				$composition_arr[$row[csf('mst_id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]];
			}
		}
	}

	foreach($sql as $row)
	{
		$com_sql="select a.id, a.fab_nature_id, a.type, a.construction, a.gsm_weight, a.weight_type, a.design, a.fabric_ref, a.fab_composition, a.yarn_type, a.color_range_id, a.rd_no, a.inserted_by, a.status_active from  lib_yarn_count_determina_mst a where a.is_deleted=0 and a.fab_nature_id=100 and a.id in(".$row[csf("fabrication")].") order by a.id ASC";
		$sql_com=sql_select($com_sql);
		$text_fab="";
		foreach ($sql_com as $val) {
			$text_fab.=$composition_arr[$val[csf('id')]];//$yarn_type_for_entry[$val[csf("yarn_type")]]." ".$val[csf("fab_composition")];
			// $text_fab.=$val[csf("type")]." ".$val[csf("construction")]." ".$val[csf("design")]." ".$val[csf("id")] ." , ";
		}
		
		$text_fab=chop($text_fab,", ");
		
		$exyarnCount=array_filter(array_unique(explode(",",$row[csf("yarn_count_id")])));
		$yarnCountTxt=""; $yarnCountId="";
		foreach($exyarnCount as $ycountid)
		{
			if($yarnCountTxt=="") $yarnCountTxt=$lib_yarn_count[$ycountid]; else $yarnCountTxt.=','.$lib_yarn_count[$ycountid];
			if($yarnCountId=="") $yarnCountId=$ycountid; else $yarnCountId.=','.$ycountid;
		}
		
		echo "load_drop_down( 'requires/buyer_inquiry_sweater_controller','".$row[csf("company_name")]."', 'load_drop_down_buyer', 'buyer_td') ;";
		echo "load_drop_down( 'requires/buyer_inquiry_sweater_controller','".$row[csf("buyer_id")]."', 'load_drop_down_brand', 'brand_td') ;";
		echo "load_drop_down( 'requires/buyer_inquiry_sweater_controller','".$row[csf("buyer_id")]."', 'load_drop_down_season', 'season_td') ;";

		echo "document.getElementById('txt_system_id').value = '".$row[csf("system_number")]."';\n";
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("style_refernce")]."';\n";
		echo "document.getElementById('txt_inq_rcvd_date').value = '".change_date_format($row[csf("inquery_date")],"dd-mm-yyyy","-")."';\n";		
		echo "document.getElementById('txt_bom').value = '".$row[csf("bom_no")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('cbo_brand_id').value = '".$row[csf("brand_id")]."';\n";
		echo "document.getElementById('cbo_season_id').value = '".$row[csf("season_buyer_wise")]."';\n";
		echo "document.getElementById('cbo_season_year').value = '".$row[csf("season_year")]."';\n";
		echo "document.getElementById('cbo_order_uom').value = '".$row[csf("order_uom")]."';\n";
		echo "document.getElementById('set_breck_down').value = '".$row[csf("set_break_down")]."';\n";
		echo "document.getElementById('tot_set_qnty').value = '".$row[csf("total_set_qnty")]."';\n";
		echo "document.getElementById('txt_sew_smv').value = '".$row[csf("set_smv")]."';\n";
		echo "document.getElementById('cbo_dealing_merchant').value = ".$row[csf("dealing_marchant")].";\n";
		echo "document.getElementById('cbo_product_department').value = ".$row[csf("product_dept")].";\n";
		echo "document.getElementById('txt_fabrication_id').value = '".$row[csf("fabrication")]."';\n";
		echo "document.getElementById('txt_fabrication').value = '".$text_fab."';\n";
		echo "document.getElementById('hidd_yarn_count_id').value = '".$yarnCountId."';\n";
		echo "document.getElementById('txt_yarn_count').value = '".$yarnCountTxt."';\n";
		
		echo "document.getElementById('cbo_gmt_item').value = '".$row[csf("gmts_item")]."';\n";
		echo "document.getElementById('cbo_priority').value = '".$row[csf("priority")]."';\n";
		echo "document.getElementById('cbo_gauge').value = '".$row[csf("gauge")]."';\n";
		echo "document.getElementById('txt_no_of_ends').value = '".$row[csf("no_of_ends")]."';\n";
		echo "document.getElementById('update_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_comments').value = '".$row[csf("style_description")]."';\n";
		echo "document.getElementById('txt_offer_qty').value = '".$row[csf("offer_qty")]."';\n";
	}
	exit();
}

if($action == "show_fabrication_list")
{
	extract($_REQUEST);
	$inquery_id=str_replace("'","",$inquery_id);
	$fabrication_id=str_replace("'","",$txt_fabrication);
	$update_id=str_replace("'","",$update_id);
	//$fabrication_arr =explode(",", $fabrication_id);
	//$imp = "'" . implode( "','", $fabrication_arr ) . "'";
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	$lib_body_part=return_library_array( "select body_part_full_name,id from lib_body_part", "id", "body_part_full_name");
	$yarn_count_determina_dtls=sql_select("SELECT mst_id, copmposition_id, percent, count_id, type_id from lib_yarn_count_determina_dtls where is_deleted=0 and mst_id in ($fabrication_id) order by id");												
	if (count($yarn_count_determina_dtls)>0)
	{
		foreach( $yarn_count_determina_dtls as $row )
		{
			if(array_key_exists($row[csf('mst_id')],$composition_arr))
			{
				$composition_arr[$row[csf('mst_id')]]=$composition_arr[$row[csf('mst_id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
			else
			{
				$composition_arr[$row[csf('mst_id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]];
			}
		}
	}
	if($update_id=='')
	{
		$yarn_count_determina_mst= sql_select("SELECT a.id as yarn_count_id, a.fab_nature_id, a.type, a.construction, a.gsm_weight, a.weight_type, a.design, a.fabric_ref, a.color_range_id, a.rd_no, a.inserted_by, a.status_active, a.full_width ,a.cutable_width from  lib_yarn_count_determina_mst a where a.is_deleted=0 and a.entry_form=426 and a.id in(".$fabrication_id.") order by a.id ASC");
		$save_update=0;
	}
	else{
		$yarn_count_determina_mst= sql_select("SELECT a.id, a.fab_nature_id, a.type, a.construction, a.gsm_weight, a.weight_type, a.design, a.fabric_ref, a.color_range_id, a.rd_no, a.full_width ,a.cutable_width, b.id as dtls_id, b.body_part_id, b.effi_per, b.fabric_cons, b.shrinkage_l, b.shrinkage_w, b.nested_pieces, b.bundles, b.yarn_count_id, b.cuttable_width,b.size_ratio,b.remarks from  lib_yarn_count_determina_mst a join consumption_la_costing_dtls b on b.yarn_count_id=a.id where a.is_deleted=0 and a.entry_form=426 and a.id in(".$fabrication_id.") and b.mst_id=$update_id order by a.id ASC");
		$save_update=1;
	}
	
	$i=1;
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table" id="fabric_dtls_tbl">
    	<thead>
    		<tr>
				<th width="50" rowspan="2">Fab RD No</th>
	    		<th width="50" rowspan="2">Ref No</th>
	    		<th width="350" rowspan="2">Fabric Description</th>
	    		<th width="60" rowspan="2">Fabric Usage</th>
	    		<th width="50" rowspan="2">Full Width</th>
	    		<th width="50" rowspan="2">Cuttable Width</th>
	    		<th width="40" rowspan="2">Efficiency &percnt;</th>
	    		<th width="120" rowspan="2">Size Ratio</th>
	    		<th width="40" rowspan="2" style="color: blue;">Fabric Cons Yds/Dzn</th>
	    		<th width="40" colspan="2">Shrinkage</th>
	    		<th width="40" rowspan="2">Nested Pieces</th>
	    		<th width="40" rowspan="2">Bundles</th>
	    		<th width="120" rowspan="2">Remarks</th>
    		</tr>
    		<tr>
    			<th width="20">L &percnt;</th>
    			<th width="20">W &percnt;</th>
    		</tr>
    	</thead>
    	<? 

    	if($update_id=='')
		{
			$save_update=0;
    		$fabrication_id_arr = explode(",", $fabrication_id);
    		foreach ($fabrication_id_arr as $fid) { 			
				$yarn_count_determina_mst= sql_select("SELECT a.id as yarn_count_id, a.fab_nature_id, a.type, a.construction, a.gsm_weight, a.weight_type, a.design, a.fabric_ref, a.color_range_id, a.rd_no, a.inserted_by, a.status_active, a.full_width ,a.cutable_width from  lib_yarn_count_determina_mst a where a.is_deleted=0 and a.entry_form=426 and a.id in(".$fid.") order by a.id ASC");
				
			
				foreach ($yarn_count_determina_mst as $row) {
		    		$fabricationData ='';
		    		$fabricationData=$row[csf('type')].', '.$row[csf('construction')].', '.$row[csf('design')].', '.$row[csf('gsm_weight')].', '.$fabric_weight_type[$row[csf('weight_type')]].', '.$color_range[$row[csf('color_range_id')]].', '.$composition_arr[$row[csf('id')]];
			    	?> 	
					<tr>
						<td width="50"><input style="width: 50px" class="text_boxes" type="text" id="txtrdno_<?= $i?>" value="<?= $row[csf('rd_no')] ?>" disabled></td>
						<td width="50"><input style="width: 50px" class="text_boxes" type="text" id="fabricref_<?= $i?>" value="<?= $row[csf('fabric_ref')] ?>" disabled></td>
						<td title="<?= $fabricationData ?>" width="350"><input style="width: 350px" class="text_boxes" type="text" id="fabricdes_<?= $i?>" name="" value="<?= $fabricationData ?>" disabled></td>
						<td width="60">
							<input style="width: 60px" class="text_boxes" type="text" id="fabricusage_<?= $i?>" onDblClick="open_body_part_popup(<?= $i; ?>)" readonly placeholder="Browse" value="<?= $lib_body_part[$row[csf('body_part_id')]]?>"  >
							<input type="hidden" id="fabricusageid_<?= $i?>" value="<?= $row[csf('body_part_id')] ?>">
						</td>
						<td width="50"><input style="width: 50px" class="text_boxes" type="text" id="txtfullwidth_<?= $i?>" placeholder="Write" value="<?= $row[csf('full_width')] ?>" disabled></td>
						<td width="50"><input style="width: 50px" class="text_boxes" type="text" id="txtcuttablewidth_<?= $i?>" placeholder="Write" value="<?= $row[csf('cutable_width')] ?>" disabled></td>
						<td width="40"><input style="width: 40px" class="text_boxes" type="text" id="txteffiper_<?= $i?>" placeholder="Write" value="<?= $row[csf('effi_per')] ?>" ></td>
						<td width="120"><input style="width: 120px" class="text_boxes" type="text" id="txtsizeration_<?= $i?>" placeholder="Write" value="<?= $row[csf('size_ratio')] ?>" ></td>
						<td width="40"><input style="width: 40px" class="text_boxes" type="text" id="txtfabriccons_<?= $i?>" placeholder="Write" value="<?= $row[csf('fabric_cons')] ?>"></td>
						<td width="40"><input style="width: 40px" class="text_boxes" type="text" id="shrinkagelength_<?= $i?>" placeholder="Write" value="<?= $row[csf('shrinkage_l')]?>"></td>
						<td width="40"><input style="width: 40px" class="text_boxes" type="text" id="shrinkagewidth_<?= $i?>" placeholder="Write" value="<?= $row[csf('shrinkage_w')]?>"></td>
						<td width="40"><input style="width: 40px" class="text_boxes" type="text" id="nestedpieces_<?= $i?>" placeholder="Write" value="<?= $row[csf('nested_pieces')] ?>"></td>
						<td  width="40">
							<input style="width: 40px" class="text_boxes" type="text" id="txttotal_<?= $i?>" value="<?= $row[csf('bundles')] ?>" placeholder="">
							<input type="hidden" id="updateiddtls_<?= $i?>" value="<?= $row[csf('dtls_id')]?>">
							<input type="hidden" id="yarncountid_<?= $i?>" value="<?= $row[csf('yarn_count_id')]?>">
						</td>
						<td>				
							<input style="width: 120px" class="text_boxes" type="text" id="txtremarks_<?= $i?>" value="<?= $row[csf('remarks')] ?>" placeholder="Browse" onDblClick="remarks_popup(<?= $i; ?>)" >
						</td>
					</tr>
					<? 
				}
				$i++;
			}
		}
		else{
			$yarn_count_determina_mst= sql_select("SELECT a.id, a.fab_nature_id, a.type, a.construction, a.gsm_weight, a.weight_type, a.design, a.fabric_ref, a.color_range_id, a.rd_no, a.full_width ,a.cutable_width, b.id as dtls_id, b.body_part_id, b.effi_per, b.fabric_cons, b.shrinkage_l, b.shrinkage_w, b.nested_pieces, b.bundles, b.yarn_count_id, b.cuttable_width,b.size_ratio,b.remarks from  lib_yarn_count_determina_mst a join consumption_la_costing_dtls b on b.yarn_count_id=a.id where a.is_deleted=0 and a.entry_form=426 and a.id in(".$fabrication_id.") and b.mst_id=$update_id order by b.id ASC");
			$save_update=1;
			foreach ($yarn_count_determina_mst as $row) {
	    		$fabricationData ='';
	    		$fabricationData=$row[csf('type')].', '.$row[csf('construction')].', '.$row[csf('design')].', '.$row[csf('gsm_weight')].', '.$fabric_weight_type[$row[csf('weight_type')]].', '.$color_range[$row[csf('color_range_id')]].', '.$composition_arr[$row[csf('id')]];
		    	?> 	
				<tr>
					<td width="50"><input style="width: 50px" class="text_boxes" type="text" id="txtrdno_<?= $i?>" value="<?= $row[csf('rd_no')] ?>" disabled></td>
					<td width="50"><input style="width: 50px" class="text_boxes" type="text" id="fabricref_<?= $i?>" value="<?= $row[csf('fabric_ref')] ?>" disabled></td>
					<td title="<?= $fabricationData ?>" width="350"><input style="width: 350px" class="text_boxes" type="text" id="fabricdes_<?= $i?>" name="" value="<?= $fabricationData ?>" disabled></td>
					<td width="60">
						<input style="width: 60px" class="text_boxes" type="text" id="fabricusage_<?= $i?>" onDblClick="open_body_part_popup(<?= $i; ?>)" readonly placeholder="Browse" value="<?= $lib_body_part[$row[csf('body_part_id')]]?>"  >
						<input type="hidden" id="fabricusageid_<?= $i?>" value="<?= $row[csf('body_part_id')] ?>">
					</td>
					<td width="50"><input style="width: 50px" class="text_boxes" type="text" id="txtfullwidth_<?= $i?>" placeholder="Write" value="<?= $row[csf('full_width')] ?>" disabled></td>
					<td width="50"><input style="width: 50px" class="text_boxes" type="text" id="txtcuttablewidth_<?= $i?>" placeholder="Write" value="<?= $row[csf('cutable_width')] ?>" disabled></td>
					<td width="40"><input style="width: 40px" class="text_boxes" type="text" id="txteffiper_<?= $i?>" placeholder="Write" value="<?= $row[csf('effi_per')] ?>" ></td>
					<td width="120"><input style="width: 120px" class="text_boxes" type="text" id="txtsizeration_<?= $i?>" placeholder="Write" value="<?= $row[csf('size_ratio')] ?>" ></td>
					<td width="40"><input style="width: 40px" class="text_boxes" type="text" id="txtfabriccons_<?= $i?>" placeholder="Write" value="<?= $row[csf('fabric_cons')] ?>"></td>
					<td width="40"><input style="width: 40px" class="text_boxes" type="text" id="shrinkagelength_<?= $i?>" placeholder="Write" value="<?= $row[csf('shrinkage_l')]?>"></td>
					<td width="40"><input style="width: 40px" class="text_boxes" type="text" id="shrinkagewidth_<?= $i?>" placeholder="Write" value="<?= $row[csf('shrinkage_w')]?>"></td>
					<td width="40"><input style="width: 40px" class="text_boxes" type="text" id="nestedpieces_<?= $i?>" placeholder="Write" value="<?= $row[csf('nested_pieces')] ?>"></td>
					<td  width="40">
						<input style="width: 40px" class="text_boxes" type="text" id="txttotal_<?= $i?>" value="<?= $row[csf('bundles')] ?>" placeholder="">
						<input type="hidden" id="updateiddtls_<?= $i?>" value="<?= $row[csf('dtls_id')]?>">
						<input type="hidden" id="yarncountid_<?= $i?>" value="<?= $row[csf('yarn_count_id')]?>">
					</td>
					<td>				
						<input style="width: 120px" class="text_boxes" type="text" id="txtremarks_<?= $i?>" value="<?= $row[csf('remarks')] ?>" placeholder="Browse" onDblClick="remarks_popup(<?= $i; ?>)" >
					</td>
				</tr>
				<? $i++;
			}
			
		}
		?>
	</table>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table">       
        <tr>
            <td align="center" valign="middle" style="max-height:820px; min-height:15px;" id="size_color_breakdown11">
                <? echo load_submit_buttons( $permission, "fnc_consumption_entry", $save_update,0 ,"reset_form('consumption_form','','')",1); ?>                        
                <input class="formbutton" type="button" onClick="sendMail()" value="Mail Send" style="width:80px;">
                <input class="formbutton" type="button" onClick="generate_report()" value="Print" style="width:80px;">
        	</td>
       </tr>
    </table>
	<?
	exit();
}

if ($action=="remarks_popup")
{
	echo load_html_head_contents("Remarks","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=$data;
	?>
    <script>
		$( document ).ready(function() {
			document.getElementById("description").value='<? echo $data; ?>';
		});
    </script>
    <body>
		<div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission,1); ?>
        <form>
			<fieldset style="width:450px;">
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="size_tbl" >
                <tr id="row_1">
                    <td width="150" align="center" >
                    	<textarea name="description" id="description" class="text_area" style="width:430px; height:100px;" maxlength="500" title="Maximum 500 Character"></textarea>
                    </td>
                </tr>
            </table>

            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="" >
                <tr>
                    <td align="center">
                    	<input type="hidden" name="hidden_appv_cause" id="hidden_appv_cause" class="text_boxes /">
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="parent.emailwindow.hide();" style="width:100px" />
                    </td>
                </tr>
            </table>
            </fieldset>
            </form>
        </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>
		$("#description" ).focus();
	</script>
    </html>
    <?
	exit();
}

if ($action=="load_drop_down_buyer_popup"){
	if($data != 0)
	{
		echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'buyer_inquiry_sweater_controller', this.value+'_1', 'load_drop_down_season', 'season_td'); load_drop_down( 'buyer_inquiry_sweater_controller', this.value+'*1', 'load_drop_down_brand', 'brand_td');" );
		exit();
	}
	else{
		echo create_drop_down( "cbo_buyer_name", 150, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'buyer_inquiry_sweater_controller', this.value+'*1', 'load_drop_down_season', 'season_td'); load_drop_down( 'buyer_inquiry_sweater_controller', this.value+'*1', 'load_drop_down_brand', 'brand_td');" );
		exit();
	}
}

if($action=="body_part_popup")
{
	echo load_html_head_contents("Item Group Select","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	?>
	<script>
	function js_set_value(id, name,type)
	{
		document.getElementById('gid').value=id;
		document.getElementById('gname').value=name;
		document.getElementById('gtype').value=type;
		parent.emailwindow.hide();
	}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;">
        <input type="hidden" id="gid" name="gid"/>
        <input type="hidden" id="gname" name="gname"/>
        <input type="hidden" id="gtype" name="gtype"/>
        <?
        $sql_tgroup=sql_select( "select body_part_full_name,body_part_short_name,body_part_type,id from lib_body_part where  is_deleted=0  and  status_active=1 order by body_part_short_name");
        ?>
        <table width="420" cellspacing="0" class="rpt_table" border="0" rules="all">
            <thead>
            <th width="40">SL</th><th width="300">Item Group</th><th>Type</th>
            </thead>
        </table>
        <table width="420" cellspacing="0" class="rpt_table" border="0" rules="all" id="item_table">
            <tbody>
            <?
            $i=1;
            foreach($sql_tgroup as $row_tgroup)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr onClick="js_set_value(<? echo $row_tgroup[csf('id')]; ?>, '<? echo $row_tgroup[csf('body_part_full_name')]; ?>', '<? echo $row_tgroup[csf('body_part_type')]; ?>')" bgcolor="<? echo $bgcolor; ?>" style="cursor: pointer;"  >
					<td width="40"><? echo $i; ?></td><td width="300"><? echo $row_tgroup[csf('body_part_full_name')]; ?></td><td width=""><? echo $body_part_type[$row_tgroup[csf('body_part_type')]]; ?></td>
				</tr>
				<?
				$i++;
            }
            ?>
            </tbody>
        </table>
        </div>
	</body>
	<script>
	setFilterGrid('item_table',-1)
	</script>
	</html>
	<?
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if($operation==0){
		$con = connect();
		

		$id=return_next_id( "id", "wo_quotation_inquery", 1 ) ;
		$new_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'QIN', date("Y",time()), 5, "select system_number_prefix, system_number_prefix_num from wo_quotation_inquery where company_id=$cbo_company_name and entry_form=457 and extract(year from insert_date)=".date('Y',time())." order by id desc ", "system_number_prefix", "system_number_prefix_num" ));

		$field_array="id, system_number_prefix, system_number_prefix_num, system_number, entry_form, company_id, style_refernce, inquery_date, bom_no, buyer_id, brand_id, season_buyer_wise, season_year, dealing_marchant, product_dept, fabrication, gmts_item, priority, gauge, no_of_ends, style_description, offer_qty, order_uom, set_break_down, total_set_qnty, set_smv, yarn_count_id, insert_by, insert_date, status_active, is_deleted";

		$data_array ="(".$id.",'".$new_system_id[1]."',".$new_system_id[2].",'".$new_system_id[0]."',457,".$cbo_company_name.",".$txt_style_ref.",".$txt_inq_rcvd_date.",".$txt_bom.",".$cbo_buyer_name.",".$cbo_brand_id.",".$cbo_season_id.",".$cbo_season_year.",".$cbo_dealing_merchant.",".$cbo_product_department.",".$txt_fabrication_id.",".$cbo_gmt_item.",".$cbo_priority.",".$cbo_gauge.",".$txt_no_of_ends.",".$txt_comments.",".$txt_offer_qty.",".$cbo_order_uom.",".$set_breck_down.",". $tot_set_qnty.",".$txt_sew_smv.",".$hidd_yarn_count_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		
		$field_array1="id, quot_id, gmts_item_id, set_item_ratio, smv_pcs, smv_set, ws_id";
		$id1=return_next_id( "id", "  WO_QUOTATION_SET_DETAILS", 1 );
		$set_breck_down_array=explode('__',str_replace("'",'',$set_breck_down));
		for($c=0;$c < count($set_breck_down_array);$c++)
		{
			$set_breck_down_arr=explode('_',$set_breck_down_array[$c]);
			if ($add_comma!=0) $data_array1 .=",";
			$data_array1 .="(".$id1.",".$id.",'".$set_breck_down_arr[0]."','".$set_breck_down_arr[1]."','".$set_breck_down_arr[2]."','".$set_breck_down_arr[3]."','".$set_breck_down_arr[4]."')";
			$add_comma++;
			$id1=$id1+1;
		}
		
		$flag=1;
		
		// echo "10** insert into wo_quotation_inquery (".$field_array.") values ".$data_array; die;
		$rID=sql_insert("wo_quotation_inquery",$field_array,$data_array,0);
		if($rID==1) $flag=1; else $flag=0;
		$rID5=sql_insert("WO_QUOTATION_SET_DETAILS",$field_array1,$data_array1,1);
		if($rID5==1 && $flag==1) $flag=1; else $flag=0;

		if($db_type==2 || $db_type==1 ){
			if($flag==1){
				oci_commit($con);
				echo "0**".$new_system_id[0].'**'.$id;
			}
			else{
				oci_rollback($con);
				echo "10**".$new_system_id[0].'**'.$id;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)
	{
		$con = connect();
		
		$update_id=str_replace("'","",$update_id);

		$image_mdt=return_field_value("image_mandatory", "variable_order_tracking", "company_name=$cbo_company_name and variable_list=30");
		$image=return_field_value("id", "common_photo_library", "master_tble_id=$update_id and form_name='inquery_sweater_front_image' and file_type=1");
		if($image_mdt==1 && $image=="")
		{
			echo "24**0"; 
			disconnect($con);
			die;
		}
		//$system_no=return_library_array( "select system_no,id from wo_quotation_inquery where id=$update_id", "id", "system_no");
		//$field_array="la_costing_date*merch_style*comments*style_des*pattern_master*bom_no*updated_by*update_date";
		//$data_array ="".$txt_consumption_date."*".$txt_merch_style."*".$txt_comments."*".$txt_style_desc."*".$txt_pattern_master."*".$txt_bom_no."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$field_array="style_refernce*inquery_date*bom_no*buyer_id*brand_id*season_buyer_wise*season_year*dealing_marchant*product_dept*fabrication*gmts_item*priority*gauge*no_of_ends*style_description*offer_qty*order_uom*set_break_down*total_set_qnty*set_smv*yarn_count_id*update_by*update_date";

		$data_array ="".$txt_style_ref."*".$txt_inq_rcvd_date."*".$txt_bom."*".$cbo_buyer_name."*".$cbo_brand_id."*".$cbo_season_id."*".$cbo_season_year."*".$cbo_dealing_merchant."*".$cbo_product_department."*".$txt_fabrication_id."*".$cbo_gmt_item."*".$cbo_priority."*".$cbo_gauge."*".$txt_no_of_ends."*".$txt_comments."*".$txt_offer_qty."*".$cbo_order_uom."*".$set_breck_down."*".$tot_set_qnty."*".$txt_sew_smv."*".$hidd_yarn_count_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		
		$field_array1="id, quot_id, gmts_item_id, set_item_ratio, smv_pcs, smv_set, ws_id";
		$add_comma=0;
	 	$id1=return_next_id( "id", "  WO_QUOTATION_SET_DETAILS", 1 ) ;
		$set_breck_down_array=explode('__',str_replace("'",'',$set_breck_down));
		for($c=0;$c < count($set_breck_down_array);$c++)
		{
			$set_breck_down_arr=explode('_',$set_breck_down_array[$c]);
			if ($add_comma!=0) $data_array1 .=",";
			$data_array1 .="(".$id1.", ".$update_id.", '".$set_breck_down_arr[0]."', '".$set_breck_down_arr[1]."', '".$set_breck_down_arr[2]."', '".$set_breck_down_arr[3]."', '".$set_breck_down_arr[4]."')";
			$add_comma++;
			$id1=$id1+1;
			//$item_ids.=$set_breck_down_arr[0].',';
		}
		
		$flag=1;
		$rID5=execute_query( "delete from WO_QUOTATION_SET_DETAILS where quot_id =".$update_id."",0);
		if($rID5==1 && $flag==1) $flag=1; else $flag=0;
		
		$rID=sql_update("wo_quotation_inquery",$field_array,$data_array,"id",$update_id,0);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		
		$rID6=sql_insert("WO_QUOTATION_SET_DETAILS",$field_array1,$data_array1,1);
		if($rID6==1 && $flag==1) $flag=1; else $flag=0;

		//echo "10**".$rID.'--'.$rID1; die;
		if($db_type==2 || $db_type==1 ){
			if($flag==1){
				oci_commit($con);
				echo "1**".str_replace("'","",$txt_system_id).'**'.str_replace("'","",$update_id);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_system_id).'**'.str_replace("'","",$update_id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation ==2)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//$system_no=return_library_array( "select system_no,id from wo_quotation_inquery", "id", "system_no");
		$update_id=str_replace("'","",$update_id);
		$is_price_quot="";
		$sql=sql_select("select id from wo_price_quotation where inquery_id=$update_id and status_active=1 and is_deleted=0");
		foreach($sql as $row){
			if($is_price_quot=="") $is_price_quot=$row[csf('id')]; else $is_price_quot.=', '.$row[csf('id')];
		}
		if($is_price_quot!=""){
			echo "pricequotation**".$is_price_quot;
			disconnect($con);die;
		}
		$jobno="";
		$sql=sql_select("select job_no from wo_po_details_master where inquiry_id=$update_id and status_active=1 and is_deleted=0");
		foreach($sql as $row){
			if($jobno=="") $jobno=$row[csf('job_no')]; else $jobno.=', '.$row[csf('job_no')];
		}
		if($jobno!=""){
			echo "jobno**".$jobno;
			disconnect($con);die;
		}
		$costsheet="";
		$sql=sql_select("select cost_sheet_no from qc_mst where inquery_id=$update_id and status_active=1 and is_deleted=0");
		foreach($sql as $row){
			if($costsheet=="") $costsheet=$row[csf('cost_sheet_no')]; else $costsheet.=', '.$row[csf('cost_sheet_no')];
		}
		if($costsheet!=""){
			echo "costsheet**".$costsheet;
			disconnect($con);die;
		}
		$field_arrmst="update_by*update_date*status_active*is_deleted";
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID=sql_delete("wo_quotation_inquery",$field_arrmst,$data_array,"id","".$update_id."",1);
		$rID1=sql_delete("wo_quotation_inquery_fab_dtls",$field_array,$data_array,"mst_id","".$update_id."",1);
		
		if($db_type==2 || $db_type==1 ){
			if($rID && $rID1){
				oci_commit($con);
				echo "2**".str_replace("'","",$txt_system_id).'**'.str_replace("'","",$update_id);
			}
			else{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_system_id).'**'.str_replace("'","",$update_id);
			}
		}
		disconnect($con);
		die;
	}
}

if($action == "consumption_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$update_id = str_replace("'","",$update_id);
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
	$brand_arr=return_library_array("select id,brand_name from lib_buyer_brand","id","brand_name");
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	$lib_body_part=return_library_array( "select body_part_full_name,id from lib_body_part", "id", "body_part_full_name");
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$system_data = sql_select("SELECT  a.id as system_id, a.system_no, a.inquiry_id, a.la_costing_date, a.merch_style, a.style_des, a.pattern_master, a.bom_no, a.comments, a.fabrication_id, b.company_id, b.buyer_id, b.season_buyer_wise, b.season_year, b.brand_id, b.style_refernce, b.fabrication from consumption_la_costing_mst a join wo_quotation_inquery b on a.inquiry_id=b.id where a.id='$update_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id");
	$master_attribute = array('system_no', 'inquiry_id', 'la_costing_date', 'merch_style', 'style_des', 'pattern_master', 'bom_no', 'comments', 'company_id', 'buyer_id', 'season_buyer_wise', 'season_year', 'brand_id', 'style_refernce','fabrication_id');
	foreach ($system_data as $row) {
		foreach ($master_attribute as $attr) {
			$$attr = $row[csf($attr)];
		}
	}
	$yarn_count_determina_dtls=sql_select("SELECT mst_id, copmposition_id, percent, count_id, type_id from lib_yarn_count_determina_dtls where is_deleted=0 and mst_id in ($fabrication_id) order by id");												
	if (count($yarn_count_determina_dtls)>0)
	{
		foreach( $yarn_count_determina_dtls as $row )
		{
			if(array_key_exists($row[csf('mst_id')],$composition_arr))
			{
				$composition_arr[$row[csf('mst_id')]]=$composition_arr[$row[csf('mst_id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
			else
			{
				$composition_arr[$row[csf('mst_id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]];
			}
		}
	}

	$yarn_count_determina_mst= sql_select("SELECT a.id, a.fab_nature_id, a.type, a.construction, a.gsm_weight, a.weight_type, a.design, a.fabric_ref, a.color_range_id, a.rd_no, a.full_width, a.cutable_width, b.id as dtls_id, b.body_part_id, b.effi_per, b.fabric_cons, b.shrinkage_l, b.shrinkage_w, b.nested_pieces, b.bundles, b.yarn_count_id, b.cuttable_width, b.size_ratio, b.remarks from  lib_yarn_count_determina_mst a join consumption_la_costing_dtls b on b.yarn_count_id=a.id where a.is_deleted=0 and a.entry_form=426 and a.id in(".$fabrication_id.") and b.mst_id=$update_id order by a.id ASC");
	$i=1;

	$company_des=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company_id");
	?>

	<table width="100%">
		<tr><th style="text-align: center;"><? echo $company_arr[$company_id] ?></th></tr>
		<tr><th style="text-align: center;">Consumption [CAD] For LA Costing</th></tr>		
	</table>
	<table width="100%">
		<tr>
			<th align="left">System No.</th>
			<th>:</th>
			<td><? echo $system_no ?></td>
			<th align="left">Master Style Ref</th>
			<th>:</th>
			<td><? echo $style_refernce ?></td>
			<th align="left">Costing Date</th>
			<th>:</th>
			<td><? echo change_date_format($la_costing_date,'yyyy-mm-dd','-'); ?></td>

		</tr>
		<tr>
			<th align="left">Buyer Name</th>
			<th>:</th>
			<td><? echo $buyer_arr[$buyer_id] ?></td>
			<th align="left">Merch Style</th>
			<th>:</th>
			<td colspan="4"><? echo $merch_style ?></td>
		</tr>
		<tr>
			<th align="left">Season</th>
			<th>:</th>
			<td><? echo $season_arr[$season_buyer_wise] ?></td>
			<th align="left">Season Year</th>
			<th>:</th>
			<td><? echo $season_year; ?></td>
			<th align="left">Brand</th>
			<th>:</th>
			<td><? echo $brand_arr[$brand_id] ?></td>
		</tr>
		<tr>
			<th align="left">Style Desc.</th>
			<th>:</th>
			<td><? echo $style_des ?></td>
			<th align="left">Pattern Master Name</th>
			<th>:</th>
			<td><? echo $pattern_master; ?></td>
			<th align="left">BOM No</th>
			<th>:</th>
			<td><? echo $bom_no ?></td>
		</tr>
		<tr>
			<th align="left">Comments</th>
			<th>:</th>
			<td colspan="7"><? echo $comments ?></td>
		</tr>
	</table>
	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="margin-top: 10px" rules="all" width="100%">
		<thead>
			<tr>
				<th width="50" rowspan="2">SL.</th>
				<th width="80" rowspan="2">Fab RD No</th>
				<th width="80" rowspan="2">Ref No</th>
				<th width="450" rowspan="2">Fabric Description</th>
				<th width="80" rowspan="2">Fabric Usage</th>
				<th width="60" rowspan="2">Full Width</th>
				<th width="60" rowspan="2">Cuttable Width</th>
				<th width="80" rowspan="2">Efficiency &percnt;</th>
				<th width="100" rowspan="2">Size Ratio</th>
				<th width="90" rowspan="2">Fabric Cons Yds/Dzn</th>
				<th width="50" colspan="2">Shrinkage</th>
				<th width="90" rowspan="2">Nested Pieces</th>
				<th width="60" rowspan="2">Bundles</th>
				<th rowspan="2">Remarks</th>
			</tr>
			<tr>
				<th width="25">L &percnt;</th>
				<th width="25">W &percnt;</th>
			</tr>
			
    	</thead>
    	<? foreach ($yarn_count_determina_mst as $row) {
    		$fabricationData ='';
    		$fabricationData=$row[csf('type')].', '.$row[csf('construction')].', '.$row[csf('design')].', '.$row[csf('gsm_weight')].', '.$fabric_weight_type[$row[csf('weight_type')]].', '.$color_range[$row[csf('color_range_id')]].', '.$composition_arr[$row[csf('id')]];
    	?> 	
		<tr>
			<td width="50" align="center"><?= $i ?></td>
			<td width="80" align="center"><?= $row[csf('rd_no')] ?></td>
			<td width="80" align="center"><?= $row[csf('fabric_ref')] ?></td>
			<td title="<?= $fabricationData ?>" width="450" align="left"><?= $fabricationData ?></td>
			<td width="80" align="left"><?= $lib_body_part[$row[csf('body_part_id')]]?></td>
			<td width="60" align="center"><?= $row[csf('full_width')] ?></td>
			<td width="60" align="center"><?= $row[csf('cutable_width')] ?></td>
			<td width="80" align="center"><?= $row[csf('effi_per')] ?></td>
			<td width="80" align="center"><?= $row[csf('size_ratio')] ?></td>
			<td width="90" align="center"><?= $row[csf('fabric_cons')] ?></td>
			<td width="25" align="center"><?= $row[csf('shrinkage_l')]?></td>
			<td width="25" align="center"><?= $row[csf('shrinkage_w')]?></td>
			<td width="90" align="center"><?= $row[csf('nested_pieces')] ?></td>
			<td width="60" align="center"><?= $row[csf('bundles')] ?></td>
			<td width="60" align="left"><?= $row[csf('remarks')] ?></td>
		</tr>
		<? 
			$i++;
		} 
		?>
	</table>
	<?
	echo signature_table(109, $company_id, "850px");
}

if ($action=="load_drop_down_buyer")
{
	if($data != 0)
	{
		echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "Select Buyer", $selected, "load_drop_down( 'requires/buyer_inquiry_sweater_controller', this.value, 'load_drop_down_season', 'season_td'); load_drop_down( 'requires/buyer_inquiry_sweater_controller', this.value, 'load_drop_down_brand', 'brand_td');" );
		exit();
	}
	else
	{
		echo create_drop_down( "cbo_buyer_name", 150, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "Select Buyer", $selected, "" );
		exit();
	}
}

if ($action=="load_drop_down_season")
{
	$data_arr = explode("*", $data);
	if($data_arr[1] == 1) $width=90; else $width=150;
	echo create_drop_down( "cbo_season_id", $width, "select id, season_name from lib_buyer_season where buyer_id='$data_arr[0]' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "Select Season", "", "" );
	exit();
}

if ($action=="load_drop_down_brand")
{
	$data_arr = explode("*", $data);
	if($data_arr[1] == 1) $width=90; else $width=150;
	echo create_drop_down( "cbo_brand_id", $width, "select id, brand_name from lib_buyer_brand brand where buyer_id='$data_arr[0]' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "Select Brand", $selected, "" );
	exit();
}

if ($action=="buyer_inquery_fab_popup")
{
	echo load_html_head_contents("Fabric Detail Entry", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
	var permission='<? echo $permission; ?>';
	function fn_addRow_fab(i)
	{
		var row_num=$('#tbl_list tbody tr').length;
		if (row_num!=i)
		{
			return false;
		}
		else
		{
			i++;
			$("#tbl_list tbody tr:last").clone().find("input,select").each(function(){
				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				  'name': function(_, name) { return name },
				  'value': function(_, value) { return value }
				});
			}).end().appendTo("#tbl_list");

			$('#slTd_'+i).val('');
			$('#txtrdno_'+i).val('');
			$('#txtrdno_'+i).removeAttr("onBlur").attr("onBlur","fnc_librdno("+i+','+0+");");
			$('#txtrdno_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_newfab("+i+");");
			$('#txtfabref_'+i).val('');
			$('#txtfabtype_'+i).val('');
			$('#txtconstraction_'+i).val('');
			$('#txtcomposition_'+i).val('');
			$('#hiddyarnCounttxt_'+i).val('');
			$('#hiddyarnCountid_'+i).val('');
			
			
			$("#tbl_list tbody tr:last").removeAttr('id').attr('id','tr_'+i);
			$('#tr_' + i).find("td:eq(0)").removeAttr('id').attr('id','slTd_'+i);
			$('#tr_' + i).find("td:eq(0)").text(i);

			$('#increase_'+i).removeAttr("value").attr("value","+");
			$('#decrease_'+i).removeAttr("value").attr("value","-");
			$('#increase_'+i).removeAttr("onclick").attr("onclick","fn_addRow_fab("+i+");");
			$('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deleteRow("+i+");");
		}
		set_all_onclick();
	}

	function fn_deleteRow(rowNo)
	{
		var row_num=$('#tbl_list tbody tr').length;

		if(row_num!=1)
		{
			$("#tr_"+rowNo).remove();
		}
	}

	function window_close()
	{
		var save_data=''; var save_text_data=''; var yarnCountstr=''; var yarnCountidstr='';
		
		$("#tbl_list").find('tr').each(function()
		{
			var hiddFabDeterId=$(this).find('input[name="hiddFabDeterId[]"]').val();
			var txtfabtype=$(this).find('input[name="txtfabtype[]"]').val();
			//var txtconstraction=$(this).find('input[name="txtconstraction[]"]').val();
			var txtcomposition=$(this).find('input[name="txtcomposition[]"]').val();
			
			var yarnCounttxt=$(this).find('input[name="hiddyarnCounttxt[]"]').val();
			var yarnCountid=$(this).find('input[name="hiddyarnCountid[]"]').val();
			if(hiddFabDeterId)
			{
				if(save_data=="") save_data=hiddFabDeterId;
				else save_data+=","+hiddFabDeterId;
				
				if(save_text_data=="") save_text_data=txtfabtype+" "+txtcomposition;
				else save_text_data+=" , "+txtfabtype+" "+txtcomposition;
				
				if(yarnCountid!="")
				{
					if(yarnCountstr=="") yarnCountstr=yarnCounttxt;
					else yarnCountstr+=","+yarnCounttxt;
					
					if(yarnCountidstr=="") yarnCountidstr=yarnCountid;
					else yarnCountidstr+=","+yarnCountid;
				}
			}
		});
		//alert(save_data);
		$('#save_data').val( save_data );
		$('#save_text_data').val( save_text_data );
		
		$('#yarncount_data').val( yarnCountstr );
		$('#yarncountid_data').val( yarnCountidstr );
		parent.emailwindow.hide();
	}
	
	function fnc_librdno(incid, type)
	{
		var rdno=$('#txtrdno_'+incid).val();
		if(type==0) var libid=0;
		else if(type==1) var libid=$('#hiddFabDeterId_'+incid).val();
		//alert(libid);
		/*if(trim(rdno)!="" || libid!=0)
		{*/
			get_php_form_data(trim(rdno)+'___'+incid+'___'+libid, "populate_data_from_rdnolib", "buyer_inquiry_sweater_controller" );
		//}
	}
	
	function openmypage_newfab(incid)
	{
		var page_link='buyer_inquiry_sweater_controller.php?action=fabpopup&incid='+incid;
		var title="Fabric Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=550px,height=350px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];

			var fabdata=this.contentDoc.getElementById("hid_libDes").value; // mrr number
			var exfabdata = fabdata.split("_");			
			$("#hiddFabDeterId_"+incid).val(exfabdata[0]);
			$("#txtrdno_"+incid).val(exfabdata[1]);
			$("#txtfabref_"+incid).val(exfabdata[2]);
			$("#txtfabtype_"+incid).val(exfabdata[3]);
			$("#txtcomposition_"+incid).val(exfabdata[5]);
			
			$("#hiddyarnCountid_"+incid).val(exfabdata[7]);
			$("#hiddyarnCounttxt_"+incid).val(exfabdata[8]);
		}
	}
    </script>

	</head>

	<body>
	<div align="center">
	<? //echo load_freeze_divs ("../../../",$permission,1); ?>
	<form name="trimsWeight_1" id="trimsWeight_1">
        <fieldset style="width:600px;">
            <legend>Fabrication Details Pop Up</legend>
            <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="600" id="tbl_list">
            	<thead>
                    <th width="30">SL</th>
                    <th width="80">RD No</th>
                    <th width="100">Fabric Ref.</th>
                    <th width="120">Yarn</th>
                    <th width="150">Composition</th>
                    <th>
                    	<input type="hidden" name="save_data" id="save_data" class="text_boxes">
                    	<input type="hidden" name="save_text_data" id="save_text_data" class="text_boxes">
                        
                        <input type="hidden" name="yarncount_data" id="yarncount_data" class="text_boxes">
                        <input type="hidden" name="yarncountid_data" id="yarncountid_data" class="text_boxes">
                    </th>
                </thead>
                <tbody>
					<?
					
					$composition_arr=array(); $yarnCountArr=array();
					$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
					//$arr=array (0=>$item_category, 3=>$color_range,6=>$composition,8=>$lib_yarn_count,9=>$yarn_type);
					$sql="select a.fab_nature_id, a.construction, a.gsm_weight, a.color_range_id, a.stich_length, a.process_loss, b.copmposition_id, b.percent, b.count_id, b.type_id, a.id, b.id as bid from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.fab_nature_id=100 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id,b.id";
					$data_array=sql_select($sql);
					if (count($data_array)>0)
					{
						foreach( $data_array as $row )
						{
							if(array_key_exists($row[csf('id')],$composition_arr))
							{
								$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
							}
							else
							{
								$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
							}
							if($yarnCountArr[$row[csf('id')]]=="") $yarnCountArr[$row[csf('id')]]=$row[csf('count_id')];
							else $yarnCountArr[$row[csf('id')]].=','.$row[csf('count_id')];
						}
					}
					unset($data_array);
					
					$fabric_composition = return_library_array("select id, fabric_composition_name from  lib_fabric_composition where status_active=1 and is_deleted=0 order by fabric_composition_name", "id", "fabric_composition_name");
                    if($save_data!="")
                    {
                        $tot_trims_wgt=0;$k=0;
                        $explSaveData = explode(",",$save_data);
                        for($z=0; $z<count($explSaveData); $z++)
                        {
							$sql_data=sql_select("select a.id, a.fab_nature_id, a.type, a.construction, a.gsm_weight, a.weight_type, a.design, a.fabric_ref, a.color_range_id, a.rd_no, a.inserted_by, a.status_active, a.full_width, a.cutable_width, a.mill_ref, a.fab_composition from lib_yarn_count_determina_mst a where is_deleted=0 and fab_nature_id=100 and id='$explSaveData[$z]' order by a.id ASC");
							$k++;
							foreach( $sql_data as $row ){
								$exyarnCount=array_filter(array_unique(explode(",",$yarnCountArr[$row[csf('id')]])));
								$yarnCountTxt=""; $yarnCountId="";
								foreach($exyarnCount as $ycountid)
								{
									if($yarnCountTxt=="") $yarnCountTxt=$lib_yarn_count[$ycountid]; else $yarnCountTxt.=','.$lib_yarn_count[$ycountid];
									if($yarnCountId=="") $yarnCountId=$ycountid; else $yarnCountId.=','.$ycountid;
								}
								?>
								<tr id="tr_<?=$k;?>">
									<td id="slTd_<?=$k;?>" align="center"><?=$k;?></td>
									<td>
										<input type="text" name="txtrdno[]" id="txtrdno_<?=$k; ?>" class="text_boxes" style="width:70px;" placeholder="Wr/Br" value="<?= $row[csf('rd_no')]; ?>" onBlur="fnc_librdno(<?=$k; ?>,0);" onDblClick="openmypage_newfab(<?=$k; ?>);"/>
										<input type="hidden" name="hiddFabDeterId[]" id="hiddFabDeterId_<?=$k; ?>" style="width:40px;" value="<?=$explSaveData[$z]; ?>"/>
										<input type="hidden" id="btnnewfab_<?=$k; ?>" class="formbutton" style="width:20px; font-style:italic" value="N" />
                                        <input type="hidden" name="hiddyarnCounttxt[]" id="hiddyarnCounttxt_<?=$k; ?>" style="width:20px;" value="<?=$yarnCountTxt; ?>" />
                                        <input type="hidden" name="hiddyarnCountid[]" id="hiddyarnCountid_<?=$k; ?>" style="width:20px;" value="<?=$yarnCountId; ?>" />
									</td>
									<td><input type="text" name="txtfabref[]" id="txtfabref_<?=$k; ?>" class="text_boxes" style="width:70px;" value="<?= $row[csf('mill_ref')]; ?>"  readonly placeholder="Display"/></td>
									<td><input type="text" name="txtfabtype[]" id="txtfabtype_<?=$k; ?>" class="text_boxes" style="width:70px;" value="<?= $composition_arr[$row[csf('id')]]; ?>" readonly placeholder="Display" /></td>
								   
									<td><input type="text" name="txtcomposition[]" id="txtcomposition_<?=$k;?>" class="text_boxes" value="<?=$fabric_composition[$row[csf('fab_composition')]]; ?>" style="width:140px;" readonly placeholder="Display"/></td>
									<td>
										<input type="button" id="increase_<?=$k;?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fn_addRow_fab(<?=$k; ?>);" readonly/>
										<input type="button" id="decrease_<? echo $k;?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<?=$k; ?>);" readonly/>
									</td>
								</tr>
								<script>fnc_librdno(<?=$k; ?>,1);</script>
								<?
                        	}
						}
                    }
                    else
                    { 
                    	?>
                        <tr id="tr_1">
                            <td id="slTd_1" align="center">1</td>
                            <td>
                            	<input type="text" name="txtrdno[]" id="txtrdno_1" class="text_boxes" style="width:70px;" placeholder="Wr/Br" onBlur="fnc_librdno(1,0);" onDblClick="openmypage_newfab(1);" />
                                <input type="hidden" name="hiddFabDeterId[]" id="hiddFabDeterId_1" class="text_boxes" style="width:40px;"/>
                            	<input type="hidden" id="btnnewfab_1" class="formbutton" style="width:20px; font-style:italic" value="N" />
                                <input type="hidden" name="hiddyarnCounttxt[]" id="hiddyarnCounttxt_1" style="width:20px;" value="" />
                                <input type="hidden" name="hiddyarnCountid[]" id="hiddyarnCountid_1" style="width:20px;" value="" />
                            </td>
                            <td><input type="text" name="txtfabref[]" id="txtfabref_1" class="text_boxes" style="width:70px;" readonly placeholder="Display"/></td>
                            <td><input type="text" name="txtfabtype[]" id="txtfabtype_1" class="text_boxes" style="width:70px;" readonly placeholder="Display"/></td>
                            <td><input type="text" name="txtcomposition[]" id="txtcomposition_1" class="text_boxes" style="width:140px;" readonly placeholder="Display" /></td>
                            <td>
                                <input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fn_addRow_fab(1);" />
                                <input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);"/>
                            </td>
                        </tr>
                <? } ?>
            </tbody>
        </table>
        <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="600">
            <tr>
            	<td align="center"><input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="window_close();" style="width:80px" /></td>
            </tr>
        </table>
		</fieldset>
	</form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="fabpopup")
{
	echo load_html_head_contents("Fabric Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>
		function js_set_value(data)
		{
			document.getElementById('hid_libDes').value=trim(data);
			parent.emailwindow.hide();
		}
		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			document.getElementById(x).style.backgroundColor = ( newColor == document.getElementById(x).style.backgroundColor )? origColor : newColor;
		}
		</script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table cellspacing="0" cellpadding="0" width="400" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                <thead>
                    <tr>
                    	<th colspan="4" align="center"><?=create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "Searching Type" ); ?></th>
                    </tr>
                    <tr>
                    	<th width="80">RD No</th>
                        <th width="130">Construction</th>
                        <th width="80">GSM/Weight</th>
                        <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:70px;" onClick="reset_form('styleRef_form','search_div','','','','');"><input type="hidden" id="hid_libDes" name="hid_libDes" /></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="general">
                    	<td><input type="text" style="width:70px" class="text_boxes" name="txt_rdno" id="txt_rdno" /></td>
                        <td><input type="text" style="width:120px" class="text_boxes" name="txt_construction" id="txt_construction" /></td>
                        <td><input type="text" style="width:70px" class="text_boxes" name="txt_gsm_weight" id="txt_gsm_weight" /></td>
                        <td>
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<?=$fabric_nature; ?>'+'**'+'<?=$libyarncountdeterminationid; ?>'+'**'+document.getElementById('txt_construction').value+'**'+document.getElementById('txt_gsm_weight').value+'**'+document.getElementById('cbo_string_search_type').value+'**'+document.getElementById('txt_rdno').value, 'fabric_description_popup_search_list_view', 'search_div', 'buyer_inquiry_sweater_controller', 'setFilterGrid(\'list_view\',-1)'); toggle( 'tr_'+'<?=$libyarncountdeterminationid; ?>', '#FFFFCC');" style="width:70px;" />
                        </td>
                    </tr>
            	</tbody>
           	</table>
            <div id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="fabric_description_popup_search_list_view")
{
	extract($_REQUEST);
	list($fabric_nature,$libyarncountdeterminationid,$construction,$gsm_weight,$string_search_type,$rdno)=explode('**',$data);
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
	$search_con='';
	if($string_search_type==1)
	{
		if($construction!='') {$search_con .= " and a.construction='".trim($construction)."'";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight='".trim($gsm_weight)."'";}
		if($rdno!='') {$search_con .= " and a.rd_no='".trim($rdno)."'";}
	}
	else if($string_search_type==2)
	{
		if($construction!='') {$search_con .= " and a.construction like ('".trim($construction)."%')";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight like ('".trim($gsm_weight)."%')";}
		if($rdno!='') {$search_con .= " and a.rd_no like ('".trim($rdno)."%')";}
	}
	else if($string_search_type==3)
	{
		if($construction!='') {$search_con .= " and a.construction like ('%".trim($construction)."')";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight like ('%".trim($gsm_weight)."')";}
		if($rdno!='') {$search_con .= " and a.rd_no like ('%".trim($rdno)."')";}
	}
	else if($string_search_type==4 || $string_search_type==0)
	{
		if($construction!='') {$search_con .= " and a.construction like ('%".trim($construction)."%')";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight like ('%".trim($gsm_weight)."%')";}
		if($rdno!='') {$search_con .= " and a.rd_no like ('%".trim($rdno)."%')";}
	}
	?>
	</head>
	<body>
		<?
		$composition_arr=array(); $yarnCountArr=array();
		$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
		$arr=array (0=>$item_category, 3=>$color_range,6=>$composition,8=>$lib_yarn_count,9=>$yarn_type);
		$sql="select a.fab_nature_id, a.construction, a.gsm_weight, a.color_range_id, a.stich_length, a.process_loss, b.copmposition_id, b.percent, b.count_id, b.type_id, a.id, b.id as bid from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.fab_nature_id=100 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id,b.id";
		$data_array=sql_select($sql);
		if (count($data_array)>0)
		{
			foreach( $data_array as $row )
			{
				if(array_key_exists($row[csf('id')],$composition_arr))
				{
					$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
				}
				else
				{
					$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
				}
				if($yarnCountArr[$row[csf('id')]]=="") $yarnCountArr[$row[csf('id')]]=$row[csf('count_id')];
				else $yarnCountArr[$row[csf('id')]].=','.$row[csf('count_id')];
			}
		}
		unset($data_array);
		
		$fabric_composition = return_library_array("select id, fabric_composition_name from  lib_fabric_composition where status_active=1 and is_deleted=0 order by fabric_composition_name", "id", "fabric_composition_name");
		?>
	    <table class="rpt_table" width="500px" cellspacing="0" cellpadding="0" border="0" rules="all" style="position: sticky; top: 0;" >
	        <thead>
	        	<tr>
					<th width="25">SL</th>
	                <th width="70">RD No</th>
		            <th width="90">Yarn Count</th>
					<th width="100">Yarn</th>
					<th>Composition</th>
	        	</tr>
	       </thead>
	   </table>
	   <div style="max-height:230px; width:500px; overflow-y:scroll">
	       <table id="list_view" class="rpt_table" width="480px" height="" cellspacing="0" cellpadding="0" border="1" rules="all" >
	            <tbody>
	        <?
	            $sql_data=sql_select("select a.id, a.fab_nature_id, a.type, a.construction, a.gsm_weight, a.weight_type, a.design, a.fabric_ref, a.color_range_id, a.rd_no, a.inserted_by, a.status_active, a.full_width, a.cutable_width, a.mill_ref, a.fab_composition, a.gauge from lib_yarn_count_determina_mst a where is_deleted=0 and a.fab_nature_id=100 $search_con order by a.id ASC");
				
	            $i=1;
	            foreach($sql_data as $row)
	            {
	                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$exyarnCount=array_filter(array_unique(explode(",",$yarnCountArr[$row[csf('id')]])));
					$yarnCountTxt=""; $yarnCountId="";
					foreach($exyarnCount as $ycountid)
					{
						if($yarnCountTxt=="") $yarnCountTxt=$lib_yarn_count[$ycountid]; else $yarnCountTxt.=','.$lib_yarn_count[$ycountid];
						if($yarnCountId=="") $yarnCountId=$ycountid; else $yarnCountId.=','.$ycountid;
					}
	                ?>
	                    <tr id="tr_<?=$row[csf('id')] ?>" bgcolor="<?=$bgcolor; ?>" height="20" style="cursor:pointer; word-break:break-all;" onClick="js_set_value('<?=$row[csf('id')]."_".$row[csf('rd_no')]."_".$row[csf('mill_ref')]."_".$composition_arr[$row[csf('id')]]."_".$row[csf('construction')]."_".$fabric_composition[$row[csf('fab_composition')]]."_".$row[csf('gauge')]."_".$yarnCountId."_".$yarnCountTxt; ?>');">
	                        <td width="25" align="center"><?=$i; ?></td>
	                        <td width="70" style="word-break:break-all"><?=$row[csf('rd_no')]; ?></td>
	                        <td width="90" style="word-break:break-all"><?=$yarnCountTxt; ?></td>
	                        <td width="100" style="word-break:break-all"><?=$composition_arr[$row[csf('id')]];// $yarn_type_for_entry[$row[csf('yarn_type')]]; ?></td>
							<td style="word-break:break-all"><?=$fabric_composition[$row[csf('fab_composition')]]; ?></td>
	                    </tr>
	                <?
	                $i++;
	            }
	        ?>
	            </tbody>
	        </table>
	    </div>
	</body>
	</html>
	<?
	exit();
}

if($action=="populate_data_from_rdnolib")
{
	$ex_data=explode("___",$data);
	
	$composition_arr=array();  $yarnCountArr=array();
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	$sql_q="select mst_id, copmposition_id, percent, count_id, type_id from lib_yarn_count_determina_dtls where is_deleted=0 order by id";
						
	$data_array=sql_select($sql_q);
	if (count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('mst_id')],$composition_arr))
			{
				$composition_arr[$row[csf('mst_id')]]=$composition_arr[$row[csf('mst_id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
			else
			{
				$composition_arr[$row[csf('mst_id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]];
			}
			
			if($yarnCountArr[$row[csf('mst_id')]]=="") $yarnCountArr[$row[csf('mst_id')]]=$row[csf('count_id')];
			else $yarnCountArr[$row[csf('mst_id')]].=','.$row[csf('count_id')];
		}
	}
	unset($data_array);
	
	if($ex_data[2]==0) $rdCond="and rd_no='$ex_data[0]'";
	else if($ex_data[2]!=0) $rdCond="and id in($ex_data[2]) ";
	
	$sqlRd="select id, type, construction, gsm_weight, weight_type, design, fabric_ref, rd_no, color_range_id, full_width, cutable_width from lib_yarn_count_determina_mst where entry_form=426 and status_active=1 and is_deleted=0 $rdCond";
	$sqlRdData=sql_select($sqlRd);
	
	if(count($sqlRdData)>0)
	{
		$exyarnCount=array_filter(array_unique(explode(",",$yarnCountArr[$sqlRdData[0][csf('id')]])));
		$yarnCountTxt=""; $yarnCountId="";
		foreach($exyarnCount as $ycountid)
		{
			if($yarnCountTxt=="") $yarnCountTxt=$lib_yarn_count[$ycountid]; else $yarnCountTxt.=','.$lib_yarn_count[$ycountid];
			if($yarnCountId=="") $yarnCountId=$ycountid; else $yarnCountId.=','.$ycountid;
		}
		
		echo "$('#txtrdno_".$ex_data[1]."').val('".$sqlRdData[0][csf('rd_no')]."');\n";
		echo "$('#hiddFabDeterId_".$ex_data[1]."').val('".$sqlRdData[0][csf('id')]."');\n"; 
		echo "$('#txtfabref_".$ex_data[1]."').val('".$sqlRdData[0][csf('fabric_ref')]."');\n"; 
		echo "$('#txtfabtype_".$ex_data[1]."').val('".$sqlRdData[0][csf('type')]."');\n"; 
		echo "$('#txtconstraction_".$ex_data[1]."').val('".$sqlRdData[0][csf('construction')]."');\n"; 
		echo "$('#txtcomposition_".$ex_data[1]."').val('".$composition_arr[$sqlRdData[0][csf('id')]]."');\n";
		
		echo "$('#hiddyarnCounttxt_".$ex_data[1]."').val('".$yarnCountTxt."');\n"; 
		echo "$('#hiddyarnCountid_".$ex_data[1]."').val('".$yarnCountId."');\n"; 
	}	
	exit();
}


if($action == 'send_mail'){

 
	list($company,$mail_item,$mail,$mail_body,$type,$update_id) = explode('**',$data);

	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
	$brand_arr=return_library_array("select id,brand_name from lib_buyer_brand","id","brand_name");
	$gitem_arr = get_garments_item_array(100);
	$d_marchent_arr=return_library_array("select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0","id","team_member_name");
	$yarn_count_arr=sql_select("select ID, YARN_COUNT from lib_yarn_count where status_active=1 and is_deleted=0 order by YARN_COUNT");
	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");

	$sql = "select ID, COMPANY_ID, SYSTEM_NUMBER, BUYER_ID, SEASON_BUYER_WISE, SEASON_YEAR, BRAND_ID, INQUERY_DATE, STYLE_REFERNCE, ACTUAL_SAM_SEND_DATE,  ACTUAL_REQ_QUOT_DATE, BUYER_REQUEST, STATUS_ACTIVE, REMARKS, DEALING_MARCHANT, GMTS_ITEM, EST_SHIP_DATE, FABRICATION, OFFER_QTY, COLOR, COLOR_ID, REQ_QUOTATION_DATE, TARGET_SAM_SUB_DATE, PRODUCT_DEPT, BUYER_TARGET_PRICE, BUYER_SUBMIT_PRICE, PRIORITY, CON_REC_TARGET_DATE, CUTABLE_WIDTH, STYLE_DESCRIPTION, GAUGE, BOM_NO, NO_OF_ENDS, ORDER_UOM, SET_BREAK_DOWN, TOTAL_SET_QNTY, SET_SMV, YARN_COUNT_ID from wo_quotation_inquery where id='$update_id' and entry_form=457 order by id";
	//echo $sql ;die;
	$sqlRes = sql_select($sql);
	$composition_arr=array();
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	$sql_q="select mst_id, copmposition_id, percent, count_id, type_id from lib_yarn_count_determina_dtls where is_deleted=0 order by id";
						
	$data_array=sql_select($sql_q);
	if (count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('mst_id')],$composition_arr))
			{
				$composition_arr[$row[csf('mst_id')]]=$composition_arr[$row[csf('mst_id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].", ";
			}
			else
			{
				$composition_arr[$row[csf('mst_id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]];
			}
		}
	}

	foreach($sqlRes as $row)
	{
		$com_sql="select a.id, a.fab_nature_id, a.type, a.construction, a.gsm_weight, a.weight_type, a.design, a.fabric_ref, a.fab_composition, a.yarn_type, a.color_range_id, a.rd_no, a.inserted_by, a.status_active from  lib_yarn_count_determina_mst a where a.is_deleted=0 and a.fab_nature_id=100 and a.id in(".$row[csf("fabrication")].") order by a.id ASC";
		$sql_com=sql_select($com_sql);
		$text_fab="";
		foreach ($sql_com as $val) {
			$text_fab.=$composition_arr[$val[csf('id')]];
		}
		
		$text_fab=chop($text_fab,", ");
		
		$exyarnCount=array_filter(array_unique(explode(",",$row[csf("yarn_count_id")])));
		$yarnCountTxt=""; $yarnCountId="";
		foreach($exyarnCount as $ycountid)
		{
			if($yarnCountTxt=="") $yarnCountTxt=$lib_yarn_count[$ycountid]; else $yarnCountTxt.=','.$lib_yarn_count[$ycountid];
			if($yarnCountId=="") $yarnCountId=$lib_yarn_count[$ycountid]; else $yarnCountId.=','.$lib_yarn_count[$ycountid];
		}

		ob_start();
		?>

		<table border="1" rules="all">
			<tr>
				<td colspan="4" align="center">
					<strong>System Id:</strong> <?= $row['SYSTEM_NUMBER'];?>
				</td>
			</tr>
			<tr>
				<td><strong>Company Name</strong></td><td><?= $company_arr[$row['COMPANY_ID']];?></td>
				<td><strong>Master Style Ref</strong></td><td><?= $row['STYLE_REFERNCE'];?></td>
			</tr>
			<tr>
				<td><strong>Inq.Rcvd Date</strong></td><td><?= change_date_format($row["INQUERY_DATE"],"dd-mm-yyyy","-");?></td>
				<td><strong>BOM</strong></td><td><?= $row['BOM_NO'];?></td>
			</tr>
			<tr>
				<td><strong>Brand</strong></td><td><?= $brand_arr[$row['BRAND_ID']];?></td>
				<td><strong>Buyer Name</strong></td><td><?= $buyer_arr[$row['BUYER_ID']];?></td>
			</tr>
			<tr>
				<td><strong>Season</strong></td><td><?= $season_arr[$row['SEASON_BUYER_WISE']];?></td>
				<td><strong>Season Year</strong></td><td><?= $row['SEASON_YEAR'];?></td>
			</tr>
			<tr>
				<td><strong>Garments Item</strong></td><td><?= $gitem_arr[$row['GMTS_ITEM']];?></td>
				<td><strong>Order UOM</strong></td><td><?= $row['ORDER_UOM'];?></td>
			</tr>
			<tr>
				<td><strong>Gauge</strong></td><td><?= $gauge_arr[$row['GAUGE']];?></td>
				<td><strong>Prod. Dept</strong></td><td><?= $product_dept[$row['PRODUCT_DEPT']];?></td>
			</tr>
			<tr>
				<td><strong>Dealing Merchant</strong></td><td><?= $d_marchent_arr[$row['DEALING_MARCHANT']];?></td>
				<td><strong>No Of Ends</strong></td><td><?= $row['NO_OF_ENDS'];?></td>
			</tr>
			<tr>
				<td><strong>Yarn Count</strong></td><td><?= $yarnCountId;?></td>
				<td><strong>Fabrication</strong></td><td><?= $text_fab;?></td>
			</tr>
			<tr>
				<td><strong>Bulk Offer Qty</strong></td><td><?= $row['OFFER_QTY'];?></td>	
				<td><strong>Priority</strong></td><td><?= $priority_arr[$row['PRIORITY']];?></td>
			</tr>
			<tr>
				<td><strong>Remarks</strong></td><td colspan="3"><?= $row['STYLE_DESCRIPTION'];?></td>		
			</tr>
		</table>
		<?
		$mailBody = ob_get_contents();
		ob_clean();

	}
	//echo $mailBody;die;
		include('../../../mailer/class.phpmailer.php');
		include('../../../auto_mail/setting/mail_setting.php');
		$mailToArr=array();$att_file_arr=array();
		if($mail){$mailToArr[]=$mail;}

		//Att file....
		// $imgSql="select IMAGE_LOCATION,REAL_FILE_NAME from common_photo_library where is_deleted=0  and MASTER_TBLE_ID=$txt_job_no and file_type=1";
		// $imgSqlResult=sql_select($imgSql);
		// foreach($imgSqlResult as $rows){
		// 	$att_file_arr[]='../../../'.$rows['IMAGE_LOCATION'].'**'.$rows['REAL_FILE_NAME'];
		// }

		$to = implode(',',$mailToArr);
		$subject = "Buyer Inquiry [Sweater]";
		$header = mailHeader();
		if($to){echo sendMailMailer( $to, $subject, $mailBody."<br>".$mail_body, $from_mail,$att_file_arr );}


}
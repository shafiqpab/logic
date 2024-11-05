<?php


header( 'Content-type:text/html; charset=utf-8' );
session_start();
if( $_SESSION['logic_erp']['user_id']=='' ) header( 'location:login.php' );
include('../../includes/common.php');

echo load_html_head_contents("Piece Rate Bill Info","../../", 1, 1, "",'1','');
	
?>

<table cellpadding="0" width="600" cellspacing="0" border="1" rules="all" class="rpt_table">
    <thead>
        <th width="200">Name</th>
        <th width="200">Qty</th>
        <th width="200">Val</th>
    </thead>
</table>
    <table width="600" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body">
        <? foreach([1,2] as $val){?>
        <tr>
            <td width="200">Name<?= $val;?></td>
            <td width="200"><?= $val;?></td>
            <td width="200"><?= $val;?></td>
            <td width="200"><input type="text" value="<?= $val;?>" style="width:70px;"></td>
            <td width="200"><input type="text" value="<?= $val;?>" style="width:70px;"></td>
        </tr>

        <? } ?>

    </table>

   <table width="600" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
    <tfoot>
        <th width="200">Name</th>
        <th width="200"><input type="text" id="poqty" value="" style="width:70px;"></th>
        <th width="200" id="poval_value"></th>  
    </tfoot>

    <!-- <input type="text" value=" < ?= $val;?>" style="width:70px;"> -->

</table>

<script>
var tableFilters = 
 {
	col_operation: {
	id: ["poqty","poval_value"],
	col: [1,2],
	operation: ["sum","sum"],
	write_method: ["setvalue","innerHTML"]
	}
 }


 setFilterGrid("table_body",-1,tableFilters);
</script>
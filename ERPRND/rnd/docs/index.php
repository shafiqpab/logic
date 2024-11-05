
<?php
require_once('../../includes/common.php');
echo load_html_head_contents("Actual Cost Entry", "../../", 1, 1,$unicode,'','',1);
?>



<script>
  let setVal = (str) =>{
    $('#innerText'+str).text(str);
    $('.table-multi-columns').freezeTable('update');
  }
</script>





 
<div id="container" style="margin-top:5px;">

  <div class="table-multi-columns" style="max-height:400px; overflow-y:scroll;">
    <table border="1" rules="all" cellspacing="0" class=" rpt_table" style="min-width: 2400px;">
      <thead>
        <tr>
          <th rowspan="2">#</th>
          <th rowspan="2">Date</th>
          <th rowspan="2">Text</th>
          <th colspan="2">Bank (1930)</th>
          <th colspan="2">Eget kapital John Doe (2010)</th>
          <th colspan="2">Eget kapital Jane Doe (2020)</th>
          <th colspan="2">Utgående moms 25 % (2610)</th>
          <th colspan="2">Moms varuförvärv EU 25 % (2615)</th>
          <th colspan="2">Ingående moms 25 % (2640)</th>
          <th colspan="2">Ingående moms utland (2645)</th>
          <th colspan="2">Moms redovisningskonto (2650)</th>
          <th colspan="2">Momspliktiga intäkter (3000)</th>
          <th colspan="2">Inköp varor EU 25 % (4056)</th>
          <th colspan="2">Förbrukningsinventarier (5400)</th>
          <th colspan="2">Kontorsmaterial och trycksaker (6100)</th>
          <th colspan="2">Övriga externa tjänster (6500)</th>
          <th colspan="2">Bankavgifter (6570)</th>
          <th colspan="2">Årets resultat (8999)</th>
        </tr>
        <tr>
          <th>Credit</th>
          <th>Debit</th>
          <th>Credit</th>
          <th>Debit</th>
          <th>Credit</th>
          <th>Debit</th>
          <th>Credit</th>
          <th>Debit</th>
          <th>Credit</th>
          <th>Debit</th>
          <th>Credit</th>
          <th>Debit</th>
          <th>Credit</th>
          <th>Debit</th>
          <th>Credit</th>
          <th>Debit</th>
          <th>Credit</th>
          <th>Debit</th>
          <th>Credit</th>
          <th>Debit</th>
          <th>Credit</th>
          <th>Debit</th>
          <th>Credit</th>
          <th>Debit</th>
          <th>Credit</th>
          <th>Debit</th>
          <th>Credit</th>
          <th>Debit</th>
          <th>Credit</th>
          <th>Debit</th>
        </tr>
      </thead>
      <tbody>

      <tr>
          <td rowspan="2">1</td>
          <td>2014-04-11</td>
          <td>iPhone</td>
          <td>7000 SEK</td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td>1750 SEK</td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td>5250 SEK</td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
        <tr>
          <td>2014-04-11</td>
          <td>iPhone</td>
          <td>7000 SEK</td>
          <td id="innerText<?=$i;?>" class="innerText<?=$i;?>"></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td><?=$i;?></td>
          <td></td>
          <td></td>
          <td></td>
          <td>1750 SEK</td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td>5250 SEK</td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
       
        <?php for($i=1;$i<210;$i++){?>
        <tr onclick="setVal(<?=$i;?>)">
          <td><?=$i;?></td>
          <td>2014-04-11</td>
          <td>iPhone</td>
          <td>7000 SEK</td>
          <td id="innerText<?=$i;?>" class="innerText<?=$i;?>"></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td><?=$i;?></td>
          <td></td>
          <td></td>
          <td></td>
          <td>1750 SEK</td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td>5250 SEK</td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
        <?php } ?>
      </tbody>
      <tfoot>
      <tr>
        <th></th>
        <th></th>
        <th></th>
        <th>12 200 SEK</th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th>3050 SEK</th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th>8000 SEK</th>
        <th></th>
        <th>150 SEK</th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
      </tr>
      </tfoot>
    </table>
  </div>
  </div>




  

<script src="freeze-table.js"></script>
<script>
$(document).ready(function() {

   
  // 2 Columns to be fixed
  $(".table-multi-columns").freezeTable({
    'columnNum' : 6,
    'scrollable':true,
    'freezeColumnHead':true,
  });
 
});
</script>


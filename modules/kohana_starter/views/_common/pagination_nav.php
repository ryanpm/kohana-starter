<div class="pagination left">
    <? if( $page->isPrev() ){ ?>
            <span><a  class="gotopage"  href="?page=1<?= $append_data ?>" data-page="<?= 1 ?>">First</a></span>
    <? } ?>

     <? if($totalpages>1){

            $start = (($current-5)<0)?1:($current-4);
            $end = (($current+5)>$totalpages)?$totalpages:($current+5);// $totalpages;

        ?>

         <? if ($current>5): ?>
             <span><a class="gotopage" href="?page=1<?= $append_data ?>" data-page="1">...</a></span>
        <? endif ?>
        <? for( $i =$start; $i <= $end; $i++ ){ ?>
                <? if( $i == $current ){ ?>
                    <span><a id="pagi_active" class="active"  href="?page=<?= $i . $append_data ?>"><?= $i ?></a></span>
                <? }else{ ?>
                    <span><a class="gotopage" href="?page=<?= $i . $append_data ?>" data-page="<?= $i ?>" ><?= $i ?></a></span>
                <? } ?>
        <? } ?>

        <? if  (($current+5)<$totalpages): ?>
            <span><a  class="gotopage" href="?page=<?= $totalpages . $append_data ?>"  data-page="<?= $totalpages ?>">...</a></span>
        <? endif ?>

    <? } ?>

    <? if( $page->isNext() ){ ?>
        <span><a class="gotopage" href="?page=<?= $totalpages . $append_data ?>" data-page="<?= $totalpages?>"  >Last</a></span>
    <? } ?>

</div>
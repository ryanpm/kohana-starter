<? if( $page > 1 ): ?>
<a href="?page=<?= $page-1 ?>">Back</a>
<? endif ?>

<?= $page ?> of <?= $totalpage ?> 

<? if( $page < $totalpage ): ?>
<a href="?page=<?= $page+1 ?>">Next</a>
<? endif ?>
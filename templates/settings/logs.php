<?php
namespace codexpert\Share_Logins;
extract( $fields );

$table = new Logs( $name, $version );
?>
<div class="wrap">
    <form method="post">
    <?php
    $table->prepare_items();
    $table->search_box( 'Search', 'search' );
    $table->display();
    ?>
    </form>
</div>
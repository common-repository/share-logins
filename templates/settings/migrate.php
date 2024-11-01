<?php
echo '<div class="wrap">';

$tabs = array(
    'export-users' => __( 'Export Users', 'share-logins' ),
    'import-users' => __( 'Import Users', 'share-logins' ),
);

echo '<h2 class="nav-tab-wrapper">';
foreach ( $tabs as $key => $label ) {
    $_tab_active = !isset( $tab_flag ) ? 'nav-tab-active' : ''; $tab_flag = true;
    echo "<a href='#share-logins_{$key}' class='nav-tab cx-migrat-nav-tab {$_tab_active}' id='share-logins_{$key}-tab'>{$label}</a>";
}
echo '</h2>';

echo '<div id="cx-message" class="cx-alert" style="display:none"></div>';

echo '<div class="metabox-holder">';
foreach ( $tabs as $key => $label ) {
    $_div_style = isset( $div_flag ) ? 'display:none' : ''; $div_flag = true;
    echo "<div id='share-logins_{$key}' class='group' style='{$_div_style}'>";
    echo apply_filters( "share-logins-template-override-{$key}", cx_get_template( $key, 'settings/migrate' ) );
    echo "</div>";
}
echo '</div><!--metabox-holder-->';

echo '</div>';
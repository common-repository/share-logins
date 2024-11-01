<?php
        $help_url = cx_is_pro() ? 'https://help.codexpert.io' : 'https://wordpress.org/support/plugin/share-logins/';
        
        $html = "
        <div class='wrap'>
            <h2>" . __( 'Documentations', 'share-logins' ) . "<a href='{$help_url}' target='_blank' class='button button-primary cx-help-button'>" . __( 'Need Help?', 'share-logins' ) . "</a></h2>
            <div id='cx-helps'>";

            $helps = get_option( 'share-logins-docs-json', array() );
            if( is_array( $helps ) ) :
            foreach ( $helps as $help ) {
                
                $html .= "
                <div id='cx-help-{$help['id']}' class='cx-help'>
                    <h2 class='cx-help-heading' data-target='#cx-help-text-{$help['id']}'><a href='{$help['link']}' target='_blank'><span class='dashicons dashicons-admin-links'></span></a> {$help['title']['rendered']}</h2>
                    <div id='cx-help-text-{$help['id']}' class='cx-help-text' style='display:none'>{$help['content']['rendered']}</div>
                </div>";

            }
            else:
                $html .= __( 'Something is wrong! No help found!', 'share-logins' );
            endif;

        $html .= '
            </div>
        </div>';

        echo $html;
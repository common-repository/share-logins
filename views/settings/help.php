<p class="cxis-desc"><?php printf( __( 'We\'ve prepared the basic FAQs for your. If you still need assistance with this plugin or any WordPress related tasks, please <a href="%s" target="_blank">reach out to us</a>.', 'image-sizes' ), 'https://help.codexpert.io' ); ?></p>

<?php
$docs = get_option( 'image-sizes-docs-json', [] );
		
if( count( $docs ) > 0 ) :
echo '<ul id="cxis-help">';
foreach ( $docs as $doc ) {
	echo "
	<li>
		<a href='{$doc['link']}' target='_blank'>{$doc['title']['rendered']}</a>
		{$doc['content']['rendered']}
	</li>";
}
echo '</ul>';
endif; // count( $docs ) > 0
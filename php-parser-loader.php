<?php
/**
 * Plugin Name:       PHP-Parser Loader
 * Plugin URI:        https://github.com/nikic/PHP-Parser
 * Description:       A standalone plugin to load the PHP-Parser library for use by other plugins. This prevents code duplication and conflicts.
 * Version:           5.1
 * Author:            Nikita Popov
 * Author URI:        https://github.com/nikic
 * License:           BSD-3-Clause
 * License URI:       https://opensource.org/licenses/BSD-3-Clause
 * Text Domain:       php-parser-loader
 */

// Prevent direct script access for security.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Use a constant to ensure the autoloader is defined only once.
if ( ! defined( 'PHP_PARSER_LOADER_INCLUDED' ) ) {
	define( 'PHP_PARSER_LOADER_INCLUDED', true );

	/**
	 * Autoloader for the PHP-Parser library classes.
	 */
	function php_parser_library_autoloader( $class ) {
		$prefix = 'PhpParser\\';
		$base_dir = __DIR__ . '/lib/PhpParser/';
		$len = strlen( $prefix );
		if ( strncmp( $prefix, $class, $len ) !== 0 ) {
			return;
		}
		$relative_class = substr( $class, $len );
		$file = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';
		if ( file_exists( $file ) ) {
			require $file;
		}
	}

	spl_autoload_register( 'php_parser_library_autoloader' );
}

/**
 * Adds a "Settings" link to the plugin's action links on the Plugins page.
 *
 * @param array $links An array of plugin action links.
 * @return array An updated array of plugin action links.
 */
function php_parser_loader_add_settings_link( $links ) {
    $settings_link = '<a href="options-general.php?page=php-parser-loader">Settings</a>';
    array_push( $links, $settings_link );
    return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'php_parser_loader_add_settings_link' );

/**
 * Registers the settings page for the PHP-Parser Loader plugin.
 */
function php_parser_loader_register_settings_page() {
    add_options_page(
        'PHP-Parser Loader Settings',
        'PHP-Parser Loader',
        'manage_options',
        'php-parser-loader',
        'php_parser_loader_render_settings_page'
    );
}
add_action( 'admin_menu', 'php_parser_loader_register_settings_page' );

/**
 * Renders the content of the settings page.
 *
 * This page includes a simple test to verify that the PHP-Parser library
 * has been loaded correctly.
 */
function php_parser_loader_render_settings_page() {
    ?>
    <div class="wrap">
        <h1>PHP-Parser Loader</h1>
        <p>This page provides a simple test to ensure that the PHP-Parser library is loaded and working correctly.</p>

        <h2>Library Status</h2>
        <?php
        if ( class_exists( 'PhpParser\\ParserFactory' ) ) {
            echo '<p style="color: green;"><strong>Success:</strong> The <code>PhpParser\\ParserFactory</code> class is available. The library appears to be loaded correctly.</p>';
        } else {
            echo '<p style="color: red;"><strong>Error:</strong> The <code>PhpParser\\ParserFactory</code> class could not be found. The autoloader may not be working correctly.</p>';
        }
        ?>

        <h2>Parser Test</h2>
        <p>The code below attempts to parse a simple PHP string and then pretty-print it. If successful, you will see the formatted PHP code.</p>
        <hr>
        <pre><code><?php
            $code_to_parse = '<?php function hello() { echo "Hello, World!"; }';

            echo "<strong>Original Code:</strong>\n";
            echo htmlspecialchars( $code_to_parse ) . "\n\n";

            $parser = ( new \PhpParser\ParserFactory() )->createForNewestSupportedVersion();

            try {
                $ast = $parser->parse( $code_to_parse );

                $prettyPrinter = new \PhpParser\PrettyPrinter\Standard();
                $formatted_code = $prettyPrinter->prettyPrintFile( $ast );

                echo "<strong>Parsed and Pretty-Printed Code:</strong>\n";
                echo htmlspecialchars( $formatted_code );

            } catch ( \PhpParser\Error $e ) {
                echo "<strong>Test Result:</strong>\n";
                echo '<p style="color: red;"><strong>Error during parsing:</strong> ' . htmlspecialchars( $e->getMessage() ) . '</p>';
            }
        ?></code></pre>
    </div>
    <?php
}
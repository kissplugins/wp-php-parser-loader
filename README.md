# PHP-Parser Loader for WordPress

A standalone plugin to load the [PHP-Parser library](https://github.com/nikic/PHP-Parser), making it available for other WordPress plugins to use. This centralized approach prevents code duplication and potential conflicts that can arise when multiple plugins bundle the same library.

**Author:** (c) Nikita Popov
**License:** BSD-3-Clause

## Description

This plugin has no user-facing interface or settings. Its sole purpose is to safely load the PHP-Parser library and provide a shared, reliable resource for other plugins that need to parse or manipulate PHP code.

Instead of bundling the library with your own plugin, you can list this plugin as a dependency.

## Installation

1.  Download the `php-parser-loader` plugin folder.
2.  Upload the entire `php-parser-loader` folder to the `/wp-content/plugins/` directory on your WordPress site.
3.  Activate the plugin through the 'Plugins' menu in WordPress.

## Instructions for Plugin Developers

To use the PHP-Parser library from your own plugin, please follow these steps:

### 1\. Check if the Library is Available

Before using any PHP-Parser classes, it is crucial to check if this loader plugin is active. You can do this by checking for the `PHP_PARSER_LOADER_INCLUDED` constant. This prevents fatal errors if the loader plugin is disabled or not installed.

```php
if ( ! defined( 'PHP_PARSER_LOADER_INCLUDED' ) ) {
    // The library is not available. You should handle this gracefully.
    // For example, you can disable your plugin's functionality and show an admin notice.
    add_action( 'admin_notices', function() {
        echo '<div class="notice notice-error"><p>';
        echo 'Your plugin requires the "PHP-Parser Loader" plugin to be installed and activated.';
        echo '</p></div>';
    });
    return; // Stop further execution of your plugin's code.
}
```

### 2\. Use the PHP-Parser Classes

Once you've verified that the library is available, you can use any of the PHP-Parser classes by referencing their full namespaces.

**Example:**

```php
<?php
/**
 * Plugin Name: My Awesome Plugin
 * Description: This plugin relies on the PHP-Parser Loader plugin.
 */

// First, check if the loader plugin is active.
if ( ! defined( 'PHP_PARSER_LOADER_INCLUDED' ) ) {
    add_action( 'admin_notices', function() {
        echo '<div class="notice notice-error"><p>My Awesome Plugin requires the PHP-Parser Loader plugin.</p></div>';
    });
    return;
}

use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter;
use PhpParser\Error as ParserError;

// Example function that uses the parser
function my_awesome_plugin_task() {
    $php_code = '<?php echo "This is some PHP code.";';

    $parser = ( new ParserFactory() )->createForNewestSupportedVersion();

    try {
        $ast = $parser->parse( $php_code );

        // You can now work with the Abstract Syntax Tree (AST)
        $prettyPrinter = new PrettyPrinter\Standard();
        $formatted_code = $prettyPrinter->prettyPrintFile( $ast );

        // Your plugin can now use the formatted code...
        // For example, log it to the error log for debugging:
        error_log( 'Formatted Code: ' . $formatted_code );

    } catch ( ParserError $e ) {
        error_log( 'PHP-Parser Error: ' . $e->getMessage() );
    }
}

// Hook your function to a WordPress action
add_action( 'init', 'my_awesome_plugin_task' );
```

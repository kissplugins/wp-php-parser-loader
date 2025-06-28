# PHP-Parser Loader for WordPress

A standalone plugin to load the [PHP-Parser library](https://github.com/nikic/PHP-Parser), making it available for other WordPress plugins to use. This centralized approach prevents code duplication and potential conflicts that can arise when multiple plugins bundle the same library.

**Original PHP Parser Author:** (c) Nikita Popov
**Library Loader WP Plugin Author:** (c) KISS Plugins
**License for both:** BSD-3-Clause 

The currently PHP Parser bundled version is **v5.2.0**, retrieved from the GitHub master branch on June 27, 2025.

## Description

This plugin has minimal user-facing interface WP settings page to help test basic functionality only. Its sole purpose is to safely load the PHP-Parser library and provide a shared, reliable resource for other plugins that need to parse or manipulate PHP code.

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

### Upgrading the Bundled PHP-Parser Library

This WordPress plugin bundles the `PHP-Parser` library to ensure its availability for other plugins and to prevent conflicts. The currently bundled version is **v5.2.0**, retrieved from the GitHub master branch on June 27, 2025. As the original library evolves, developers may need to upgrade the bundled version to incorporate new features, performance improvements, and security fixes.

Here is a guide for a WordPress developer on how to upgrade the bundled `PHP-Parser` library at a later time:

-----

### How to Upgrade the Bundled PHP-Parser Library Independently of this Plugin

This guide outlines the process for updating the version of the `PHP-Parser` library included with this plugin.

**1. Download the New Library Version:**

Navigate to the official `PHP-Parser` GitHub repository at [https://github.com/nikic/PHP-Parser](https://github.com/nikic/PHP-Parser). From the "Releases" page, download the source code (in .zip or .tar.gz format) for the desired newer version.

**2. Replace the Existing Library Files:**

The current plugin structure places the `PHP-Parser` library files within the `/lib/PhpParser/` directory.

  * **Delete the existing contents** of the `lib/PhpParser/` directory within your plugin's folder.
  * **Extract the downloaded source code.** Inside the extracted folder, you will find a `lib/` or `src/` directory containing the library's source files.
  * **Copy the new library files** into the `lib/PhpParser/` directory of this plugin. The autoloader is configured to look for files in this specific location, so it's crucial to maintain this directory structure.

**3. Verify the Upgrade:**

After replacing the files, it is important to verify that the new version of the library is loading and functioning correctly.

  * Navigate to the WordPress admin dashboard.
  * Go to **Settings -\> PHP-Parser Loader**.
  * This settings page includes a simple test that attempts to parse a string of PHP code. If the "Library Status" shows "Success" and the "Parser Test" displays the "Parsed and Pretty-Printed Code" without any errors, the upgrade was successful.

**4. Update Plugin Documentation:**

Finally, update the plugin's main file comment block and any relevant documentation to reflect the new version of the bundled library. Change the version number and the date it was retrieved. For example:

```php
/**
 * ...
 * The current version that's bundled is 
 * "vX.X.X (Retrieved from Github on YYYY-MM-DD)"
 * ...
 */
```

By following these steps, you can ensure that your plugin remains up-to-date with the latest advancements in the `PHP-Parser` library, providing a stable and secure experience for your users.

**NO WARRANTY**
  
THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

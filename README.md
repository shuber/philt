# Philt

Generic interface to multiple PHP template engines


## Usage

### Initialization

    require 'philt/philt.php';
    $philt = new Philt;

### Basic

    // => /tmp/template.php
    Hello <?= $name ?>!

    $template = $philt->template('/tmp/template.php');
    get_class($template); // Philt\PhpTemplate
    $template->render(array('name' => 'John')); // Hello John!
    $template->render(array('name' => 'Jane')); // Hello Jane!

### Nested templates

    // => /tmp/template.haml.php
    .<?= $class ?>= $name

    $template = $philt->template('/tmp/template.haml.php');
    get_class($template); // Philt\NestedTemplate
    $template->render(array('class' => 'person', 'name' => 'John')); // <div class="person">John</div>
    $template->render(array('class' => 'person', 'name' => 'Jane')); // <div class="person">Jane</div>

### The `binding` object

#### Properties

    $template->binding->class = 'person';
    $template->binding->locals; // array('class' => 'person')
    $template->render(array('name' => 'John')); // <div class="person">John</div>
    $template->render(array('name' => 'Jane')); // <div class="person">Jane</div>

#### Methods

    class AssetHelpers {
        function asset_path($path) {
            return '/assets/'.$path;
        }
    }

    $template->binding->extend('AssetHelpers');
    $template->binding->asset_path('test.png'); // /assets/test.png

#### Overriding methods

    class S3AssetHelpers {
        function asset_path($path) {
            return 'http://example.s3.amazonaws.com'.$this->super($path);
        }
    }

    $template->binding->extend('S3AssetHelpers');
    $template->binding->ancestors; // array('S3AssetHelpers', 'AssetHelpers')
    $template->binding->asset_path('test.png'); // http://example.s3.amazonaws.com/assets/test.png

#### Missing/wildcard methods

    $template->binding->get_color(); // BadMethodCallException - Undefined method Philt\Binding::get_color()

    class Getters {
        function method_missing($method, $arguments) {
            if (preg_match('/^get_(.+)$/', $method, $matches) && isset($this->{$matches[1]})) {
                return $this->{$matches[1]};
            } else {
                return $this->super($method, $arguments);
            }
        }
    }

    $template->binding->extend('Getters');

    $template->binding->color = 'Brown';
    $template->binding->get_color(); // Brown

    $template->binding->get_size(); // BadMethodCallException - Undefined method Philt\Binding::get_size()
    $template->binding->undefined(); // BadMethodCallException - Undefined method Philt\Binding::undefined()

### Using individual template engines

    $template = new Philt\PhpTemplate('<img src="<?= $binding->asset_path($image) ?>" />');
    $template->render(array('image' => 'test.png')); // <img src="http://example.s3.amazonaws.com/assets/test.png" />
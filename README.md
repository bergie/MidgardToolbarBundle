Symfony2 Toolbar Bundle
=======================

This bundle provides simple [toolbars](http://en.wikipedia.org/wiki/Toolbar) for Symfony2 applications. The toolbars consist of buttons that may have icons, labels and accesskeys associated with them. The buttons may trigger GET or POST actions on URLs.

The toolbars have been designed to be quite similar to [the toolbar concept in the MidCOM](http://www.midgard-project.org/development/mrfc/0026/) framework.

If you are looking for navigation menus instead of action-oriented toolbars, then [KnpMenuBundle](https://github.com/knplabs/KnpMenuBundle) is a better option.

## Installation

Install this bundle by adding the following to the `deps` file and running `php bin/vendors install`:

    [MidgardToolbarBundle]
        git=git://github.com/bergie/MidgardToolbarBundle.git
        target=Midgard/ToolbarBundle

Then add the `Midgard` namespace to the `app/autoload.php`:

    'Midgard' => __DIR__.'/../vendor'

And enable this bundle in your Kernel:

    new Midgard\ToolbarBundle\MidgardToolbarBundle()

## Usage

Toolbars can be used either directly, or through a toolbar provider. Direct toolbars are useful for situations where you want for example to add a toolbar to each item in a listing. Toolbars used via the toolbar provider can be used for shared toolbars on a web application where different parts of the application may register their own actions into the toolbar.

### Direct toolbars

    use Midgard\ToolbarBundle\Toolbar\Toolbar;

    $toolbar = new Toolbar();
   
    // GET action
    $toolbar->addItem(
        array(
            'url' => '/login',
            'label' => 'Log in',
            'icon' => '/web/some-icon.png',
            'helptext' => 'Log in to the system',
        )
    );

    // POST action
    $toolbar->addItem(
        array(
            'url' => '/myformprocessor',
            'label' => 'Delete',
            'post' => true,
            'hiddenargs' => array(
                'article_id' => 1,
            ),
        )
    );

    echo $toolbar->render();

In addition to providing direct URLs to the `addItem` method, you can also have the Symfony2 router generate them for you. In that case provide the URL in array format, for example:

    $toolbar->addItem(
        array(
            'url' => array(
                'route' => '_demo_hello',
                'parameters' => array(
                    'name' => 'World',
                ),
            ),
            'label' => 'Say hello',
        )
    );

For URL generation to work, you need to pass the Dependency Injection container to the toolbar with the `setContainer` method. Centralized toolbars explained below do this step automatically.

### Centralized toolbars

Centralized toolbars are accessible from the provider by name. You can have multiple. For example, the [Midcom Compatibility Bundle](https://github.com/bergie/MidgardMidcomCompatBundle/) uses four:

* `view`: Toolbar associated with the main object of a page (actions like "edit")
* `node`: Toolbar associated with the bundle that provides the current view (actions like "add")
* `host`: Site-wide toolbar (actions like "logout")
* `help`: Access to contextual help

Quick usage example:

    $toolbar = $this->container->get('midgard.toolbar.provider')->get('main');
    $toolbar->addItem(...);
    
    echo $this->container->get('midgard.toolbar.provider')->render('main');
    // or echo $toolbar->render();

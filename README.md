Symfony2 Toolbar Bundle
=======================

This bundle provides simple toolbars for Symfony2 applications. The toolbars consist of buttons that may have icons, labels and accesskeys associated with them. The buttons may trigger GET or POST actions on URLs.

The toolbars have been designed to be quite similar to the toolbar concept in the MidCOM framework.

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

### Centralized toolbars

    $toolbar = $this->container->get('midgard.toolbar.provider')->get('main');
    $toolbar->addItem(...);
    
    echo $this->containet->get('midgard.toolbar.provider')->render('main');
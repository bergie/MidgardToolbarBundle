<?php
namespace Midgard\ToolbarBundle\Toolbar;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class ToolbarProvider extends ContainerAware
{
    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    public function get($toolbar)
    {
        $request = $this->container->get('request');
        if ($request->attributes->has('midgard_toolbars')) {
            $toolbars = $request->attributes->get('midgard_toolbars');
            if (!isset($toolbars[$toolbar])) {
                $toolbars[$toolbar] = new Toolbar();
                $request->attributes->set('midgard_toolbars', $toolbars);
            }
        } else {
            $toolbars = array();
            $toolbars[$toolbar] = new Toolbar();
            $request->attributes->set('midgard_toolbars', $toolbars);
        }
        return $toolbars[$toolbar];
    }

    public function render($toolbar)
    {
        $request = $this->container->get('request');
        if (!$request->attributes->has('midgard_toolbars')) {
            throw new \InvalidArgumentException("Rendering of toolbar {$toolbar} requested but none are available");
        }

        $toolbars = $request->attributes->get('midgard_toolbars');
        if (!isset($toolbars[$toolbar])) {
            throw new \InvalidArgumentException("Rendering of missing toolbar {$toolbar} requested");
        }

        return $toolbars[$toolbar]->render();
    }

    public function show($toolbar)
    {
        echo $this->render($toolbar);
    }
}

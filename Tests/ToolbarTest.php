<?php
namespace Midgard\ToolbarBundle\Tests;

use Midgard\ToolbarBundle\Toolbar\Toolbar;

class ToolbarTest extends \PHPUnit_Framework_TestCase
{
    public function testItems()
    {
        $toolbar = new Toolbar();

        $this->assertEquals(count($toolbar->items()), 0);

        $toolbar->addItem(
            array(
                'url' => '/login',
                'label' => 'Log in',
                'icon' => '/web/some-icon.png',
                'helptext' => 'Log in to the system',
            )
        );

        $this->assertEquals(count($toolbar->items()), 1);

        $item = $toolbar->getItem(0);
        $this->assertEquals($item['enabled'], true);
        $this->assertEquals($item['post'], false);

        $toolbar->disableItem(0);
        $item = $toolbar->getItem(0);
        $this->assertEquals($item['enabled'], false);

        $toolbar->enableItem(0);
        $item = $toolbar->getItem(0);
        $this->assertEquals($item['enabled'], true);

        $toolbar->removeItem(0);

        $this->assertEquals(count($toolbar->items()), 0);
    }
}

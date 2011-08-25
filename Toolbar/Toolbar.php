<?php
namespace Midgard\ToolbarBundle\Toolbar;

use Symfony\Component\DependencyInjection\ContainerAware;

class Toolbar extends ContainerAware
{
    private $class = '';
    private $id = '';
    private $items = array();
    private static $usedAccesskeys = array();

    public function __construct($id = null, $class = null)
    {
        if ($class) {
            $this->class = $class;
        }

        if ($id) {
            $this->id = $id;
        }
    }

    public function addItem(array $item)
    {
        $this->items[] = $this->normalizeItem($item);
    }

    private function checkIndex($index)
    {
        if (!isset($this->items[$index])) {
            throw new \InvalidArgumentException("No toolbar item {$index} found");
        }
    }

    public function removeItem($index)
    {
        $this->checkIndex($index);
        $this->items = array_splice($this->items, $index, 1);
    }

    public function removeAll()
    {
        $this->items = array();
    }

    public function enableItem($index)
    {
        $this->checkIndex($index);
        $this->items[$index]['enabled'] = true;
    }

    public function disableItem($index)
    {
        $this->checkIndex($index);
        $this->item[$index]['enabled'] = false;
    }

    public function hideItem($index)
    {
        $this->checkIndex($index);
        $this->item[$index]['hidden'] = true;
    }

    public function showItem($index)
    {
        $this->checkIndex($index);
        $this->item[$index]['hidden'] = false;
    }

    public function indexOfUrl($url)
    {
        foreach ($this->items as $index => $item) {
            if ($item['url'] != $url) {
                continue;
            }
            return $index;
        }
        return null;
    }

    private function normalizeUrl($url)
    {
        if (is_string($url)) {
            return $url;
        }

        if (!is_array($url)) {
            throw new \InvalidArgumentException('URLs have to be defined as strings or arrays');
        }

        if (!$this->container) {
            throw new \RuntimeException('You must provide Dependency Injection container to the Toolbar before passing it parametrized URLs');
        }

        if (!isset($url['route'])) {
            throw new \InvalidArgumentException('URL array must include a route identifier');
        }

        if (!isset($url['parameters'])) {
            $url['parameters'] = array();
        }

        if (!isset($url['absolute'])) {
            $url['absolute'] = false;
        }

        return $this->container->get('router')->generate($url['route'], $url['parameters'], $url['absolute']);
    }

    private function normalizeItem(array $item)
    {
        if (!isset($item['url'])) {
            throw new \InvalidArgumentException("Toolbar items must have URLs");
        }
        $item['url'] = $this->normalizeUrl($item['url']);

        if (!isset($item['options'])) {
            $item['options'] = array();
        }

        if (!is_array($item['options'])) {
            throw new \InvalidArgumentException("Toolbar item options must be an array");
        }

        if (!isset($item['hidden'])) {
            $item['hidden'] = false;
        }

        if (!isset($item['helptext'])) {
            $item['helptext'] = '';
        }

        if (!isset($item['icon'])) {
            $item['icon'] = null;
        }

        if (!isset($item['label'])) {
            $item['label'] = '';
        }

        if (!isset($item['enabled'])) {
            $item['enabled'] = true;
        }

        if (!isset($item['post'])) {
            $item['post'] = false;
        }

        if (!isset($item['accesskey'])) {
            $item['accesskey'] = null;
        }

        if (!isset($item['hiddenargs'])) {
            $item['hiddenargs'] = array();
        }

        if (!is_array($item['hiddenargs'])) {
            throw new \InvalidArgumentException("Toolbar item hidden args must be an array");
        }

        if ($item['accesskey']) {
            if (in_array($item['accesskey'], Toolbar::$usedAccesskeys)) {
                $item['accesskey'] = null;
            } else {
                $item['helptext'] .= ' (Alt-' . strtoupper($item['accesskey']) . ')';
            }
        }

        return $item;
    }

    public function render()
    {
        $output = "<ul id=\"{$this->id}\" class=\"{$this->class}\">";

        foreach ($this->items as $index => $item) {
            if ($item['hidden']) {
                continue;
            }

            $class = 'disabled';
            if ($item['enabled']) {
                $class = 'enabled';
            }

            $output .= "<li class=\"{$class}\">" . $this->renderItem($index) . "</li>";
        }

        $output .= "</ul>\n";

        return $output;
    }

    public function renderItem($index)
    {
        $this->checkIndex($index);
        $item = $this->items[$index];

        if ($item['post'] && $item['enabled']) {
            return $this->renderPostItem($index);
        }

        if (!$item['enabled']) {
            $output = "<abbr title=\"{item['helptext']}\">";
        } else {
            $output = "<a href=\"{$item['url']}\" title=\"{$item['helptext']}\"";
            if ($item['accesskey']) {
                $output .= " accesskey=\"{$item['accesskey']}\"";
            }

            foreach ($item['options'] as $key => $value) {
                $outout .= " {$key}=\"{$value}\"";
            }

            $output .= '>';
        }

        if ($item['icon']) {
            $output .= "<img src=\"{$item['icon']}\" alt=\"\" />";
        }

        $output .= "<span class=\"toolbar_label\">{$item['label']}</span>";

        if (!$item['enabled']) {
            $output .= "</abbr>";
        } else {
            $output .= "</a>";
        }

        return $output;
    }

    private function renderPostItem($index)
    {
        $item = $this->items[$index];

        $output = "<form method=\"post\" action=\"{$item['url']}\">";

        foreach ($item['hiddenargs'] as $key => $value) {
            $output .= "<input type=\"hidden\" name=\"{$key}\" value=\"{$value}\" />";
        }

        $output .= "<button type=\"submit\" name=\"{$this->class}_submit\"";

        foreach ($item['options'] as $key => $value) {
            $output .= " {$key}=\"{$value}\"";
        }

        if ($item['accesskey']) {
            $output .= " accesskey=\"{$item['accesskey']}\"";
        }

        $output .= " title=\"{$item['helptext']}\">";

        if ($item['icon']) {
            $output .= "<img src=\"{$item['icon']}\" alt=\"\" />";
        }

        $output .= "<span class=\"toolbar_label\">{$item['label']}</span>";

        $output .= "</button></form>";

        return $output;
    }

    public function __sleep()
    {
        return array('class', 'id', 'items');
    }
}

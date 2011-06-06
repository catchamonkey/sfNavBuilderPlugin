<?php
/**
 * sfNavBuilderRenderer
 * The default renderer used when outputting navigation
 * @author Chris Sedlmayr catchamonkey <chris@sedlmayr.co.uk>
 */
class sfNavBuilderRenderer implements sfNavBuilderRendererInterface
{
    public function render(sfNavBuilder $menu)
    {
        $ret = '';
        foreach ($menu->getItems() as $item)
        {
            $ret .= '<li><a class="'.($item->isActive() ? 'selected' : '').'" href="'.$item->getUrl().'">'.$item.'</a></li>';

            if ($item->hasChildren() && $item->isActive())
            {
                $ret .= '<li><ul id="subnavlist">';
                foreach ($item->getChildren() as $child)
                {
                    $ret .= '<li><a class="'.($child->isActive() ? 'subSelected' : '').'" href="'.$child->getUrl().'">'.$child.'</a></li>';
                }
                $ret .= '</ul></li>';
            }
        }
        return $ret;
    }
}

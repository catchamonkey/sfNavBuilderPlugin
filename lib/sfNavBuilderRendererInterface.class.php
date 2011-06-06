<?php
/**
 * sfNavBuilderRendererInterface
 * Provides the interface for any navigation renderer classes
 * @author Chris Sedlmayr catchamonkey <chris@sedlmayr.co.uk>
 */
interface sfNavBuilderRendererInterface
{
    public function render(sfNavBuilder $menu);
}

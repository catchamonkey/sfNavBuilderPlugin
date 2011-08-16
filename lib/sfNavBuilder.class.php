<?php
/**
 * sfNavBuilder
 * Provides an OO interface to define a hierarchical navigation menu
 * @author Chris Sedlmayr catchamonkey <chris@sedlmayr.co.uk>
 */
class sfNavBuilder
{
    private $_menu;
    private $_renderer;
    private $_request;
    private $_context;

    public function __construct()
    {
        $this->_menu        = array();
        $this->_renderer    = 'sfNavBuilderRenderer';
        $this->_request     = FALSE;
        $this->_context     = new StdClass();
    }

    /**
     * Defines the class to be used when rendering
     * @param String $r The name of the class that will handle menu rendering
     * @return sfNavBuilder $this The current sfNavBuilder instance
     */
    public function setRenderer($r)
    {
        if (!class_exists($r))
        {
            throw new sfPluginException("Renderer class does not exist", 1);
        }
        $this->_renderer = $r;
        return $this;
    }

    /**
     * Sets the current request object instance
     * @param sfWebRequest $r The live instance of sfWebRequest
     * @return sfNavBuilder $this The current sfNavBuilder instance
     */
    public function setRequest(sfWebRequest $r)
    {
        $this->_request = $r;
        return $this;
    }

    /**
     * Sets the currently active module
     * @param String $m The currently active module
     * @return sfNavBuilder $this The current sfNavBuilder instance
     */
    public function setModule($m)
    {
        $this->_context->module = $m;
        return $this;
    }

    /**
     * Sets the currently active action
     * @param String $a The currently active action
     * @return sfNavBuilder $this The current sfNavBuilder instance
     */
    public function setAction($a)
    {
        $this->_context->action = $a;
        return $this;
    }

    /**
     * Adds an item to the menu
     * @param sfNavBuilderItem $item A single menu item
     * @return sfNavBuilder $this The current sfNavBuilder instance
     */
    public function addItem(sfNavBuilderItem $item)
    {
        // pass in the request instance to the item
        $item->setRequest($this->_request);
        // add this item to the menu
        $this->_menu[$item->getDisplayName()] = $item;
        // mark the active state of the parent element
        $item->setActive($this->isItemActive($item));
        // mark the active state of any children elements
        foreach ($item->getChildren() as $child)
        {
            $child->setActive($this->isItemActive($child));
        }
        return $this;
    }

    /**
     * Retrieves a menu item by display name
     * @param String $name The display name of the item you want to retrieve
     * @return Mixed, sfNavBuilderItem on success, Boolean FALSE on failure
     */
    public function getItem($name)
    {
        return isset($this->_menu[$name]) ? $this->_menu[$name] : FALSE;
    }

    /**
     * Adds multiple items to the menu
     * @param Array $items An array of sfNavBuilderItem instances
     * @return sfNavBuilder $this The current sfNavBuilder instance
     */
    public function addItems(Array $items)
    {
        foreach ($items as $item)
        {
            if (!$item instanceof sfNavBuilderItem)
            {
                throw new sfPluginException("Each item must be an instance of sfNavBuilderItem", 1);
            }
            $this->addItem($item);
        }
        return $this;
    }

    /**
     * Returns all defined menu items
     * @return Array $this->_menu All currently defined menu items
     */
    public function getItems()
    {
        return $this->_menu;
    }

    /**
     * Outputs the menu items using the defined renderer
     * @return The result of the rendering classes render() function
     */
    public function render()
    {
        $renderer = new $this->_renderer();
        return $renderer->render($this);
    }

    /**
     * Works out if the menu item is active
     * @param sfNavBuilderItem $item The item to check
     * @return Boolean $ret
     */
    public function isItemActive(sfNavBuilderItem $item)
    {
        $ret = FALSE;
        // look at the activation criteria and see if they are met
        foreach ($item->getActivateWhen() as $activate)
        {
            // minimum for activation is an array of modules to check
            if (in_array($this->_context->module, $activate['module']))
            {
                // check actions if defined
                if (isset($activate['action']))
                {
                    if (in_array($this->_context->action, $activate['action']))
                    {
                        // check params if defined
                        if (isset($activate['paramName']) && isset($activate['paramVal']))
                        {
                            if (
                                in_array(
                                    $this->_request->getParameter($activate['paramName']), 
                                    $activate['paramVal']
                                )
                            )
                            {
                                $ret = TRUE;
                                break;
                            }
                        }
                        else
                        {
                            $ret = TRUE;
                            break;
                        }
                    }
                }
                else
                {
                    $ret = TRUE;
                    break;
                }
            }
        }
        return $ret;
    }
}
<?php
/**
 * sfNavBuilderItem
 * Defines a single menu item to pass into sfNavBuilder
 * @author Chris Sedlmayr catchamonkey <chris@sedlmayr.co.uk>
 */
class sfNavBuilderItem
{
    private $_displayName;
    private $_url;
    private $_activateWhen;
    private $_credentialRules;
    private $_isActive;
    private $_children;
    private $_attributes;
    private $_request;
    private $_context;
    private $_route;
    private $_routeParams;
    private $_persistableRouteParams;

    public function __construct()
    {
        $this->_displayName             = NULL;
        $this->_url                     = NULL;
        $this->_activateWhen            = array();
        $this->_credentialRules         = array();
        $this->_isActive                = FALSE;
        $this->_children                = array();
        $this->_attributes              = array();
        $this->_request                 = FALSE;
        $this->_context                 = FALSE;
        $this->_route                   = FALSE;
        $this->_routeParams             = array();
        $this->_persistableRouteParams  = array();
    }

    /**
     * Sets the Display Name of the item
     * @param String $n The display name to use for this item
     * @return sfNavBuilderItem $this The current sfNavBuilderItem instance
     */
    public function setDisplayName($n)
    {
        if (!is_string($n))
        {
            throw new sfPluginException("Display Name must be a string", 1);
        }
        $this->_displayName = $n;
        return $this;
    }

    /**
     * Sets the URL to use in the href of this item
     * @param String $u The URL to use for this item
     * @return sfNavBuilderItem $this The current sfNavBuilderItem instance
     */
    public function setUrl($u)
    {
        if (!is_string($u))
        {
            throw new sfPluginException("URL must be a string", 1);
        }
        $this->_url = $u;
        return $this;
    }

    /**
     * Adds a set of activation matches for this item
     * @param Array $a A set of rules on which to mark this item as active
     * @return sfNavBuilderItem $this The current sfNavBuilderItem instance
     */
    public function addActivateWhen(Array $a)
    {
        $this->_activateWhen[] = $a;
        return $this;
    }

    /**
     * Adds a number of credentials (1 or more), that the current user must
     * have one of for this item to exist in their menu
     * @param Array $credentials The credentials to base this items addition on
     * @return sfNavBuilderItem $this The current sfNavBuilderItem instance
     */
    public function addCredentialRules(Array $credentials)
    {
        $this->_credentialRules = array_merge($this->_credentialRules, $credentials);
        return $this;
    }

    /**
     * Sets the active state of this item
     * @param Boolean $b
     * @return sfNavBuilderItem $this The current sfNavBuilderItem instance
     */
    public function setActive($b)
    {
        if (!is_bool($b))
        {
            throw new sfPluginException("Active state must be Boolean", 1);

        }
        $this->_isActive = $b;
        return $this;
    }

    /**
     * Adds a child to this item allowing hierarchy to be created in the menu
     * @param sfNavBuilderItem $i An item instance to add
     * @return sfNavBuilderItem $this The current sfNavBuilderItem instance
     * @see setParent() for alternative hierarchy creation
     */
    public function addChild(sfNavBuilderItem $i)
    {
        $this->_children[$i->_displayName] = $i;
        return $this;
    }

    /**
     * Sets this item as the parent of the supplied item allowing hierarchy to
     * be created in the menu
     * @param sfNavBuilderItem $i An item to set as the parent of this item
     * @return sfNavBuilderItem $this The current sfNavBuilderItem instance
     * @see addChild() for alternative hierarchy creation
     */
    public function setParent(sfNavBuilderItem $i)
    {
        $i->addChild($this);
        return $this;
    }

    /**
     * Adds an attribute to the item
     * @param String $name The attribute name
     * @param String $value The attribute value
     * @return sfNavBuilderItem $this The current sfNavBuilderItem instance
     */
    public function addAttribute($name, $value)
    {
        $this->_attributes[$name] = $value;
        return $this;
    }

    /**
     * Returns the attribute by key if it is set for the current item
     * @param  String $name attribute key to look for
     * @param  mixed $default OPTIONAL Value to to return if key not found.
     *         Default NULL
     * @return String attribute by key if found, NULL (default) if key not found
     *         $default if default param provided.
     */
    public function getAttribute($name, $default = NULL)
    {
        if (isset($this->_attributes[$name]))
        {
            return $this->_attributes[$name];
        }

        return $default;
    }

    /**
     * Sets an sfWebRequest instance
     * @param sfWebRequest $r An instance of sfWebRequest
     * @return sfNavBuilderItem $this The current sfNavBuilderItem instance
     */
    public function setRequest(sfWebRequest $r)
    {
        $this->_request = $r;
        return $this;
    }

    /**
     * Sets the context of this menu
     * @param StdClass $c The Context items defined as a StdClass
     * @return sfNavBuilderItem $this The current sfNavBuilderItem instance
     */
    public function setContext(StdClass $c)
    {
        $this->_context = $c;
        return $this;
    }

    /**
     * Sets the route to use for generatiion of the URL for this item
     * @param String $r The name of the route
     * @return sfNavBuilderItem $this The current sfNavBuilderItem instance
     */
    public function setRoute($r)
    {
        if (!is_string($r))
        {
            throw new sfPluginException("Route must be a string", 1);
        }
        $this->_route = $r;
        return $this;
    }

    /**
     * Adds route parameters to use if generating the URL for this item
     * Merges with any existing route params on this item
     * @param Array $params Key value pairs of route parameters
     * @return sfNavBuilderItem $this The current sfNavBuilderItem instance
     */
    public function addRouteParams(Array $params)
    {
        $this->_routeParams = array_merge($this->_routeParams, $params);
        return $this;
    }

    /**
     * Adds persistable route parameters to any existing on this item
     * The value of these parameters is retrieved from the sfWebRequest instance
     * and he key=value pairs are added to the URL if being generated
     * @param Array $params An array of parameter names to persist
     * @return sfNavBuilderItem $this The current sfNavBuilderItem instance
     **/
    public function addPersistableRouteParams(Array $params)
    {
        $this->_persistableRouteParams = array_merge($this->_persistableRouteParams, $params);
        return $this;
    }

    /**
     * Returns the attributes of this item as a single string
     * e.g. name="value" name2="value2"
     * @return String $ret All defined attributes in name="value" format
     **/
    public function getAttributes()
    {
        $ret = '';
        foreach ($this->_attributes as $name => $value)
        {
            $ret .= $name . '="'.$value.'"';
        }
        return $ret;
    }

    /**
     * Does this item have any children defined?
     * @return Boolean
     */
    public function hasChildren()
    {
        return (empty($this->_children) ? FALSE : TRUE);
    }

    /**
     * Returns the display name of this item
     * @return String $_displayName
     */
    public function getDisplayName()
    {
        return $this->_displayName;
    }

    /**
     * Removes a child item from the current Item
     * @param String $name The Display name of the child to remove
     */
    public function removeChild($name)
    {
        unset($this->_children[$name]);
    }

    /**
     * Returns the URL of this item, will also run the generation of the URL
     * if _route has been defined
     * @return String _url
     */
    public function getUrl()
    {
        $this->_prepareUrl();
        return $this->_url;
    }

    /**
     * Returns the activation rules for this item
     * @return Array $_activateWhen
     */
    public function getActivateWhen()
    {
        return $this->_activateWhen;
    }

    /**
     * Returns the credential rules for this item
     * @return void
     **/
    public function getCredentialRules()
    {
        return $this->_credentialRules;
    }

    /**
     * Returns the active state of this item
     * @return Boolean
     */
    public function isActive()
    {
        return $this->_isActive;
    }

    public function canBeAdded()
    {
        // be defensive
        $hasPermission = FALSE;
        // if no credential based rules are defined
        if (count($this->_credentialRules) == 0)
        {
            // grant permission to this item
            $hasPermission = TRUE;
        }
        // the user must have one of the defined credentials (at least)
        foreach ($this->_credentialRules as $credential)
        {
            // if the permission is now found
            if ($this->_context->user->hasCredential($credential))
            {
                // break out as they only have to have one
                $hasPermission = TRUE;
                break;
            }
        }
        return $hasPermission;
    }

    /**
     * Returns any children defined for this item
     * @return Array $_children, Will be an empty array if no children are defined
     */
    public function getChildren()
    {
        return $this->_children;
    }

    /**
     * Prepares a URL based on parameters within this sfNavBuilderItem instance
     * It will overwrite any existing _url if there is a _route defined
     * It will also persist _persistableRouteParams and their values
     */
    private function _prepareUrl()
    {
        // if a route has been defined we should generate the url from it
        if ($this->_route)
        {
            // retrieve the values for persistable params ready for URL generation
            $persistedParams = array();
            foreach ($this->_persistableRouteParams as $paramName)
            {
                $persistedParams[$paramName] = $this->_request->getParameter($paramName);
            }
            $this->_url = url_for($this->_route, array_merge(
                $this->_routeParams, $persistedParams
            ));
        }
    }

    /**
     * Magic method called when you echo this object
     * @return String $this->getDisplayName() The display name of this item
     */
    public function __toString()
    {
        return $this->getDisplayName();
    }
}
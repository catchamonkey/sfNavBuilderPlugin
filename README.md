#sfNavBuilderPlugin aids the creation of hierarchical menus

The sfNavBuilderPlugin is a symfony 1.4 plugin that offers a simple to use interface, 
and an easily overriden rendering system.  
It supports active states by module, action and param key value matching.

##Installation

###Clone the plugin into your project

    git clone git://github.com/catchamonkey/sfNavBuilderPlugin.git plugins/sfNavBuilderPlugin

###Activate the plugin in your config/ProjectConfiguration.class.php

    public function setup()
    {
        $this->enablePlugins(
            'sfDoctrinePlugin',
            'sfNavBuilderPlugin'
        );
    }

##Simple Example

    // if you are using url_for (as we are here), don't forget to load the helper
    $this->getContext()->getConfiguration()->loadHelpers('Url');

    // create a simple menu item
    $home = new sfNavBuilderItem();
    $home->setDisplayName('Home')
        ->setUrl(url_for('dashboard'))
        ->addActivateWhen(array(
            'module' => array('home'),
            'action' => array('index')
        ));

    $this->menu = new sfNavBuilder();
    $this->menu
        ->setRequest($request)
        ->setModule($this->getContext()->getModuleName())
        ->setAction($this->getContext()->getActionName())
        ->addItem($home);
    
    // and in your template you would call
    
    <?php echo $menu->render(); ?>

##Parent item with a child

We want to activate the parent item when either that is selected, or the child
item is selected

    $parentItem = new sfNavBuilderItem();
    $parentItem->setDisplayName('Dashboard')
        ->setUrl(url_for('dashboard'))
        ->addActivateWhen(array(
            'module' => array('home'),
            'action' => array('index', 'about)
        ));

And we activate the level 2 item only when it is selected

    $childItem = new sfNavBuilderItem();
    $childItem->setDisplayName('About Us')
        ->setUrl(url_for('about'))
        ->addActivateWhen(array(
            'module'        => array('home'),
            'action'        => array('about')
        ))
        ->setParent($parentItem);

You can also define the hierarchy with addChild() on the parent item

    $parentItem->addChild($childItem);

Now build the menu

    $this->menu = new sfNavBuilder();
    $this->menu
        ->setRequest($request)
        ->setModule($this->getContext()->getModuleName())
        ->setAction($this->getContext()->getActionName())
        ->addItem($parentItem);

And finally, call render within your template

    <?php echo $menu->render(); ?>
    
##Custom Rendering class

You can override the class used to render the menu, each menu item has useful
functions so you can appropriately render your menu.

After creating the items, when you add to the menu instance you set the class you 
want to be used when rendering.

    $this->menu = new sfNavBuilder();
    $this->menu
        ->setRequest($request)
        ->setModule($this->getContext()->getModuleName())
        ->setAction($this->getContext()->getActionName())
        ->addItem($parentItem)
        ->setRenderer('myMenuRenderingClass');

Your rendering class must have a public function called render() and should implement the 
interface sfNavBuilderRendererInterface.  
Take a look at the default rendering class in the plugin (sfNavBuilderRenderer) to see what menu item 
functions are available to you
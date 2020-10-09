<?php
namespace App\Reserve;

use Psr\Container\ContainerInterface;
use App\Reserve\DefaultSetup;

class DefaultSetupController
{
    protected $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args)
    {
        $module = $this->container->config->get('module','reserve');  
        $setup = new DefaultSetup($this->container->mysql,$this->container,$module);

        $setup->setup();
        $html = $setup->processSetup();
        
        $template['html'] = $html;
        $template['title'] = MODULE_LOGO.'All Defaults';
        
        return $this->container->view->render($response,'admin.php',$template);
    }
}
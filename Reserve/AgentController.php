<?php
namespace App\Reserve;

use Psr\Container\ContainerInterface;
use App\Reserve\Agent;

class AgentController
{
    protected $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args)
    {
        $table_name = TABLE_PREFIX.'agent'; 
        $table = new Agent($this->container->mysql,$this->container,$table_name);

        $table->setup();
        $html = $table->processTable();
        
        $template['html'] = $html;
        $template['title'] = MODULE_LOGO.' Configure reservation agents';
        
        return $this->container->view->render($response,'admin.php',$template);
    }
}
<?php
namespace App\Reserve;

use Psr\Container\ContainerInterface;
use App\Reserve\ServiceOperator;

class ServiceOperatorController
{
    protected $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args)
    {
        $table_name = TABLE_PREFIX.'service_operator'; 
        $table = new ServiceOperator($this->container->mysql,$this->container,$table_name);

        $table->setup();
        $html = $table->processTable();
        
        $template['html'] = $html;
        $template['title'] = MODULE_LOGO.' Service Operators';
        
        return $this->container->view->render($response,'admin.php',$template);
    }
}
<?php
namespace App\Reserve;

use Psr\Container\ContainerInterface;
use App\Reserve\Package;

class PackageController
{
    protected $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args)
    {
        $param = [];

        $table_name = TABLE_PREFIX.'package'; 
        $table = new Package($this->container->mysql,$this->container,$table_name);

        $table->setup($param);
        $html = $table->processTable();
        
        $template['html'] = $html;
        $template['title'] = MODULE_LOGO.'Packages';
        
        return $this->container->view->render($response,'admin.php',$template);
    }
}
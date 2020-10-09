<?php
namespace App\Reserve;

use Psr\Container\ContainerInterface;
use App\Reserve\Source;

class SourceController
{
    protected $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args)
    {
        $table_name = TABLE_PREFIX.'source'; 
        $table = new Source($this->container->mysql,$this->container,$table_name);

        $table->setup();
        $html = $table->processTable();
        
        $template['html'] = $html;
        $template['title'] = MODULE_LOGO.' Configure reservation sources';
        
        return $this->container->view->render($response,'admin.php',$template);
    }
}
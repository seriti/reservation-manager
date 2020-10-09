<?php
namespace App\Reserve;

use Psr\Container\ContainerInterface;
use App\Reserve\TransferType;

class TransferTypeController
{
    protected $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args)
    {
        $table_name = TABLE_PREFIX.'transfer_type'; 
        $table = new TransferType($this->container->mysql,$this->container,$table_name);

        $table->setup();
        $html = $table->processTable();
        
        $template['html'] = $html;
        $template['title'] = MODULE_LOGO.' Transfer Types';
        return $this->container->view->render($response,'admin.php',$template);
    }
}
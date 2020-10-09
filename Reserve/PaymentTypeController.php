<?php
namespace App\Reserve;

use Psr\Container\ContainerInterface;
use App\Reserve\PaymentType;

class PaymentTypeController
{
    protected $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args)
    {
        $table_name = TABLE_PREFIX.'payment_type'; 
        $table = new PaymentType($this->container->mysql,$this->container,$table_name);

        $table->setup();
        $html = $table->processTable();
        
        $template['html'] = $html;
        $template['title'] = MODULE_LOGO.' Payment Types';
        return $this->container->view->render($response,'admin.php',$template);
    }
}
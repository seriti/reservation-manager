<?php
namespace App\Reserve;

use Psr\Container\ContainerInterface;
use App\Reserve\UserExtend;

class UserExtendController
{
    protected $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args)
    {
        $table_name = TABLE_PREFIX.'user_extend'; 
        $table = new UserExtend($this->container->mysql,$this->container,$table_name);

        $table->setup();
        $html = $table->processTable();
        
        $template['html'] = $html;
        $template['title'] = MODULE_LOGO.'System user settings for reservation module';
        
        return $this->container->view->render($response,'admin.php',$template);
    }
}
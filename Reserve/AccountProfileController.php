<?php
namespace App\Reserve;

use Psr\Container\ContainerInterface;
use App\Reserve\AccountProfile;

class AccountProfileController
{
    protected $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args)
    {
        $user = $this->container->user;
        
        //NB: TABLE_PREFIX constant not applicable as not called within admin module
        $table_prefix = MODULE_RESERVE['table_prefix'];
        
        $table_name = $table_prefix .'user_extend'; 
        $record = new AccountProfile($this->container->mysql,$this->container,$table_name);

        $param = [];
        $param['user_id'] = $user->getId();
        $param['table_prefix'] = $table_prefix;
        $record->setup($param);
        $html = $record->processRecord();
        
        $template['html'] = $html;
        $template['title'] = 'Your profile data';
        
        return $this->container->view->render($response,'public.php',$template);
    }
}
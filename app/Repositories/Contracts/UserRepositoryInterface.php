<?php
/**
 * Created by PhpStorm.
 * User: rohini
 * Date: 14/2/17
 * Time: 1:03 PM
 */
namespace App\Repositories\Contracts;

interface UserRepositoryInterface
{
    public function getUser($mobile_number);
    
    public function storeUser($appinput);

}
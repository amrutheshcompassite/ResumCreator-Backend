<?php
/**
 * Created by PhpStorm.
 * User: rohini
 * Date: 14/2/17
 * Time: 1:04 PM
 */
namespace App\Repositories\Eloquent;

use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use DB;
use Carbon\Carbon;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Transformers\UserTransformer;
/**
 * Class UserRepository
 * @package App\Repositories\Eloquent
 */
class UserRepository extends BaseRepository implements UserRepositoryInterface
{

    private $user;
    public function __construct(User $user)
    {
        $this->user = $user;
    }
    
    public function getUser($mobile_number)
    {

        $user = $this->user->where('phone_no', '=', $mobile_number)
            ->first();

        return $user;
    }
    
    public function storeUser($data)
    {
        try {
            $returnData = $this->user->firstOrCreate([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone_no' => $data['phone_no']
            ]);

            return $this->fetch($returnData, new UserTransformer,
                'user');

        } catch (\Exception $e) {

            throw $e;
        }
    }
}
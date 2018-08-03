<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Middleware\BaseMiddleware;
use App\Http\Controllers\API\ApiBaseController;
use Illuminate\Contracts\Events\Dispatcher;

class VerifyJWTToken extends BaseMiddleware
{
    /**
     * {@inheritdoc}
     *
     * @return  void
     */
    protected $auth;
    protected $apiBase;

    public function __construct(JWTAuth $auth, ApiBaseController $apiBase, Dispatcher $events)
    {
        $this->auth = $auth;
        $this->apiBase = $apiBase;
        $this->events = $events;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$token = $this->auth->setRequest($request)->getToken()) {
            return $this->respond('tymon.jwt.absent', 'token_not_provided', 400);
        }
        try {
            $user = $this->auth->authenticate($token);

            if (!$user) {
                return $this->respond('tymon.jwt.user_not_found', 'user_not_found', 404);
            }

        } catch (TokenExpiredException $e) {
            return $this->respond('tymon.jwt.expired', 'token_expired', $e->getStatusCode(), [$e]);
        } catch (JWTException $e) {
            return $this->respond('ty   mon.jwt.invalid', 'token_invalid', $e->getStatusCode(), [$e]);
        }
        $this->events->fire('tymon.jwt.valid', $user);
        $response = $next($request);

        return $response;
    }
}

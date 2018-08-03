<?php

namespace App\Http\Controllers\API;

use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Manager;
use App\Http\Controllers\Controller;
use League\Fractal\Serializer\ArraySerializer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Serializer\DataArraySerializer;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Traits\BaseAbstract;
use Illuminate\Support\Facades\Auth;

class ApiBaseController extends Controller
{
    use BaseAbstract;

    protected $includes = [];

    protected $dataBag = [];

    protected $authUser;

    /**
     * Response Status Code
     *
     * @var integer
     */
    protected $statusCode = 200;

    const CODE_WRONG_ARGS = 'GEN-FUBARGS';
    const CODE_NOT_FOUND = 'GEN-LIKETHEWIND';
    const CODE_INTERNAL_ERROR = 'GEN-AAAGGH';
    const CODE_UNAUTHORIZED = 'GEN-MAYBGTFO';
    const CODE_FORBIDDEN = 'GEN-GTFO';

    /**
     * Getter for statusCode
     *
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Setter for statusCode
     *
     * @param int $statusCode Value to set
     *
     * @return self
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * Generates an error response
     *
     * @param  array $array
     * @param  array $headers
     *
     * @return \Illuminate\Http\Response|FatalErrorException
     */
    protected function respondWithError($message, $errorCode)
    {
        if ($this->statusCode === 200) {
            trigger_error(
                "You better have a really good reason for erroring on a 200...",
                E_USER_WARNING
            );
        }

        if ($errorCode == self::CODE_UNAUTHORIZED) {
                return $this->respondWithArray([
                'success' => false,
                'error'   => [
                    'code'      => $errorCode,
                    'http_code' => $this->statusCode,
                    'message'   => $message,
                ],
            ], ['WWW-Authenticate' => 'OAuth realm="users"']);
        }

        return $this->respondWithArray([
            'success' => false,
            'error'   => [
                'code'      => $errorCode,
                'http_code' => $this->statusCode,
                'message'   => $message,
            ],
        ]);
    }

    /**
     * Generates a Response with a 403 HTTP header and a given message.
     *
     * @return  Response
     */
    public function errorForbidden($message = 'Forbidden')
    {
        return $this->setStatusCode(403)->respondWithError($message, self::CODE_FORBIDDEN);
    }

    /**
     * Generates a Response with a 500 HTTP header and a given message.
     *
     * @return  Response
     */
    public function errorInternalError($message = 'Internal Error')
    {
        return $this->setStatusCode(500)->respondWithError($message, self::CODE_INTERNAL_ERROR);
    }

    /**
     * Generates a Response with a 404 HTTP header and a given message.
     *
     * @return  Response
     */
    public function errorNotFound($message = 'Resource Not Found')
    {
        return $this->setStatusCode(404)->respondWithError($message, self::CODE_NOT_FOUND);
    }

    /**
     * Generates a Response with a 401 HTTP header and a given message.
     *
     * @return  Response
     */
    public function errorUnauthorized($message = 'Unauthorized')
    {
        return $this->setStatusCode(401)->respondWithError($message, self::CODE_UNAUTHORIZED);
    }

    /**
     * Generates a Response with a 400 HTTP header and a given message.
     *
     * @return  Response
     */
    public function errorWrongArgs($message = 'Wrong Arguments')
    {
        return $this->setStatusCode(400)->respondWithError($message, self::CODE_WRONG_ARGS);
    }

    /**
     * @param array  $successArr
     * @param string $message
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function respond($successArr = [], $message = null)
    {
        $arr = [];
        if (!is_array($successArr)) {
            $arr['success'] = $successArr;
            $arr['message'] = $message;
        } else {
            $arr = $successArr;
        }

        $response = $this->responseFormat($arr, $this->dataBag);

        return response()
            ->json($response, $this->getStatusCode());
    }

    /**
     * @param array $successArr
     * @param array $dataArr
     *
     * @return array
     */
    protected function responseFormat(array $successArr, array $dataArr = [])
    {
        $response = [
            'status'  => [
                'success'   => $successArr['success'],
                'http_code' => $this->getStatusCode(),
                'message'   => $successArr['message'],
            ]
        ];
        if(empty($dataArr)){
        $response['data'] = NULL;
        }else {
             $response['data'] = $dataArr;
        }
        return $response;

    }

    /**
     * Parses json request and converts to array
     *
     * @param  \Illuminate\Http\Request $requestObject
     *
     * @return array                \Illuminate\Http\Request
     */
    public function getContent($requestObject = null)
    {
        if (app()->environment('testing')) {
            return request()->all();
        }

        $request = $requestObject;

        if (!is_object($requestObject)) {
            $request = request();
        }

        if ($request->isJson()) {

          // return (array) json_decode($request->getContent());

            return (array)json_decode($request->getContent());
          //  return (array) json_decode($request->getContent());
        } else {
            return request()->all();
        }
    }

}

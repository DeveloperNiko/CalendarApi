<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;

class ResponseJsonMiddleware
{
    /**
     * The Response Factory our app uses
     *
     * @var ResponseFactory
     */
    protected $factory;

    /**
     * JsonMiddleware constructor.
     *
     * @param ResponseFactory $factory
     */
    public function __construct(ResponseFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json');

        // Get the response
        $response = $next($request);

        // If the response is not strictly a JsonResponse, we make it
        if (!$response instanceof JsonResponse) {
            $responseData = [];

            $data = json_decode($response->content(), true);
            if (is_array($data) === false) {
                $data = $response->content();
            } else {
                $data = array_key_exists('data', $data) ? $data['data'] : $data;
            }

            if (in_array($response->status(), [200, 201, 202])) {
                $responseData = [
                    'status' => $response->status(),
                    'data' => $data,
                ];
            } else {
                if (is_array($data)) {
                    $responseData = array_merge(
                        ['status' => $response->status()],
                        $data
                    );
                } else {
                    $responseData = [
                        'status' => $response->status(),
                        'data' => $data,
                    ];
                }
            }
            $response = $this->factory->json(
                $responseData,
                $response->status(),
                $response->headers->all(),
            );
        }

        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}

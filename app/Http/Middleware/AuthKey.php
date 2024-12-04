<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthKey {

    protected $_key = "EX3hAgMaIMjtRDhOoodZXSF8anBDUR";

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next) {
        $key = $request->header('key');
        if ($key != $this->_key) {
            return $this->sendError('You are not authorized');
        }

        return $next($request);
    }

    public function sendError($error, $errorMessages = [], $code = 404) {
        $response = [
            'responseCode'  => 101,
            'responseState' => 'Error',
            'responseText'  => $error,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }

}

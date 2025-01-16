<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JWTMiddleware extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try{
            $user = JWTAuth::parseToken()->authenticate();
        }catch(Exceptions $e){
            if ($e instanceof \Tymon\JWTAuth\Exceptions\tokenInvalidException) {
                return response()->json(['status' => 'Token is Invalid']);
            }else if($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return response()->json(['status' => 'Token is expired']);
            }else if($e instanceof \Tymon\JWTAuth\Exceptions\TokenBlacklistedException){
                return response()->json(['status' => 'Token is blacklisted']);
            }else{
                return response()->json(['status' => 'Authorization Token not found']);
            }
        }         
        return $next($request);
    }
}

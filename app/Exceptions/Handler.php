<?php

namespace Microffice\Exceptions;

use Auth;
use Exception;
use Log;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;


use Illuminate\Routing\UrlGenerator;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException )
        {
            // Retrieving the Session Id
            $laravel_session = Crypt::decrypt(request()->cookie('laravel_session'));
            // Caching requested URL with this Session Id as key
            // to throw a warning on redirected page
            Cache::put($laravel_session . '_NotFoundHttpException', $request->url(), 1);
            // Retrieving last visited page
            $last_visited_page = Cache::pull($laravel_session . '_PreviousURL');
            return redirect($last_visited_page);
        } elseif ($e instanceof AuthorizationException) {
            Log::error('Unautorized action from user_id: ' . Auth::user()->id . '. Route: /' . request()->path() . '. HTTP method: ' . request()->method() . '.');
            return back()->withErrors($e->getMessage());
        } elseif ($e instanceof ModelNotFoundException) {
            Log::info('ModelNotFoundException from user_id: ' . Auth::user()->id . '. Route: /' . request()->path() . '. HTTP method: ' . request()->method() . '.');
            $e = new HttpException(404, $e->getModel());
        }
        return parent::render($request, $e);
    }
}

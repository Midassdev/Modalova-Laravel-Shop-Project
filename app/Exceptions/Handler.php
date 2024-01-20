<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Exceptions\OldGenderDetectedException;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class,
        \Illuminate\Queue\MaxAttemptsExceededException::class,
        \LogicException::class,
        \RedisException::class,
        OldGenderDetectedException::class,
    ];

    public function report(Throwable $e)
    {
        if ($this->shouldReport($e)) {
            if (app()->bound('sentry')) {
                app('sentry')->captureException($e);
            }
        }

        return parent::report($e);
    }

    public function render($request, Throwable $e) {
        if($e instanceof BrandNotFoundException) {
            if($request->color || $request->category || $request->gender)
                return redirect(get_magic_route([
                    'color' => $request->color,
                    'gender' => $request->gender,
                    'category' => $request->category,
                ], [
                    'brand' => $e->slug,
                ]));

            return redirect()->route('get.products.search', ['q' => $e->slug]);
        } elseif($e instanceof CategoryNotFoundException) {
            return redirect()->route('get.products.search', ['q' => $e->slug, 'brand' => $e->brand]);
        } elseif($e instanceof OldGenderDetectedException) {
            $route = $request->route();
            $params = $route->parameters();
            $params = [...$params, ...\Request::all()];

            if($params['gender'] = $e->getNewGender())
                return redirect()->route($route->getName(), $params, 301);
        }

        return parent::render($request, $e);
    }
}

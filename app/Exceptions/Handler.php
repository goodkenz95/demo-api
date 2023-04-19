<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

     /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function render($request, Throwable $exception)
    {
        if($exception){
            $api_response = false;
            if(in_array(strtolower($request->segment(1)), ["api","wh"]) == "api" OR $request->header('host') == env("API_URL","") ){
                $api_response = true;
            }

            switch(get_class($exception)){
                case "Illuminate\Validation\ValidationException":
                    goto ignore_error;
                break;
                case "libphonenumber\NumberParseException":
                    

                    $response = array(
                        "msg" => "Invalid Phone number format.",
                        "status" => FALSE,
                        'status_code' => "INVALID_FORMAT"
                        );
                    $status_code = 419;

                break;
                case "Illuminate\Session\TokenMismatchException": 

                    $response = array(
                        "msg" => "Token expired. Please try again",
                        "status" => FALSE,
                        'status_code' => "INVALID_TOKEN"
                        );
                    $status_code = 401;

                break;
                case "ParseError":
                case "Error":
                case "UnexpectedValueException":
                case "BadMethodCallException":
                case "ErrorException":
                case "ReflectionException":
                case "Symfony\Component\Debug\Exception\FatalErrorException":
                case "Symfony\Component\Debug\Exception\FatalThrowableError":
                case "InvalidArgumentException":
                case "GuzzleHttp\Exception\ClientException":
                case "Illuminate\Contracts\Container\BindingResolutionException":
                case "Exception":
                case "TypeError":
                case "Facade\Ignition\Exceptions\ViewException":
                case "Symfony\Component\ErrorHandler\Error\FatalError":
                case "Symfony\Component\Routing\Exception\RouteNotFoundException":

                    $response = array(
                        "msg" => "Server error. Code : #{$exception->getMessage()}",
                        "status" => FALSE,
                        'status_code' => "APP_ERROR",
                        );
                    $status_code = 500;
                break;
                case "Propaganistas\LaravelPhone\Exceptions\NumberParseException":
                    $response = array(
                        "msg" => "Invalid Phone number.",
                        "status" => FALSE,
                        'status_code' => "APP_ERROR",
                        );
                    $status_code = 400;
                break;
                case "Illuminate\Database\QueryException":
                    $response = array(
                        "msg" => "Database error. Code : #{$exception->getLine()}",
                        "status" => FALSE,
                        'status_code' => "DB_ERROR"
                        );
                    $status_code = 500;
                break;
                case "Symfony\Component\HttpKernel\Exception\NotFoundHttpException":
                    $status_code = $exception->getStatusCode();
                    $response = array(
                        "msg" => "METHOD : {$request->server()["REQUEST_METHOD"]},API : {$request->getRequestUri()} not found.",
                        "status" => FALSE,
                        'status_code' => "NOT_FOUND"
                        );
                    
                break;
                case "Tymon\JWTAuth\Exceptions\TokenBlacklistedException":
                case "PHPOpenSourceSaver\JWTAuth\Exceptions\TokenBlacklistedException":
                    $response = array(
                        "msg" => "Invalid/Expired token.",
                        "status" => FALSE,
                        'status_code' => "INVALID_TOKEN"
                        );
                    $status_code = 401;
                break;
                case "Illuminate\Auth\AuthenticationException":
                    $response = array(
                        "msg" => "Session already closed.",
                        "status" => FALSE,
                        'status_code' => "ACCOUNT_LOGOUT"
                        );
                    $status_code = 423;
                break;
                case "Tymon\JWTAuth\Exceptions\TokenExpiredException":
                    $response = array(
                        "msg" => "Expired token.",
                        "status" => FALSE,
                        'status_code' => "EXPIRED_TOKEN"
                        );
                    $status_code = 423;
                break;
                case "Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException":
                    $status_code = $exception->getStatusCode();

                    $response = array(
                        "msg" => "{$request->server()["REQUEST_METHOD"]} METHOD API : {$request->getRequestUri()} not allowed.",
                        "status" => FALSE,
                        'status_code' => "METHOD_NOT_ALLOWED"
                        );
                break;

                case "Illuminate\Http\Exceptions\PostTooLargeException":
                    $response = array(
                        "msg" => "Unable to process attachment. File too large.",
                        "status" => FALSE,
                        'status_code' => "UPLOAD_SIZE_LIMIT_REACHED"
                        );
                    $status_code = 406;
                break;

                case "Illuminate\Foundation\Http\Exceptions\MaintenanceModeException":
                    $response = array(
                        "msg" => "Platform currently under maintenance.",
                        "status" => FALSE,
                        'status_code' => "MAINTENANCE_MODE"
                        );
                    $status_code = 503;
                break;

                default:
                dd(get_class($exception));exit;

            }

            if($api_response){
                $response['code'] = "#".$exception->getCode();
                return response()->json($response, $status_code);
            }

            session()->flash('notification-status', "warning");
            session()->flash('notification-msg', $response['msg']);
            if(!in_array($status_code,["404","500"])){
                return redirect()->back();
            }
        }

        ignore_error:
        return parent::render($request, $exception);
    }
}

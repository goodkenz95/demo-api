<?php

namespace App\Laravel\Middlewares\Api;

use Closure, Helper,Str;
use App\Laravel\Models\{User, Article};


class ExistRecord
{

    protected $format;
    protected $reference_id;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string $record
     * @return mixed
     */
    public function handle($request, Closure $next, $record)
    {
        $this->format = $request->format;
        $response = array();

        switch (strtolower($record)) {
            case 'user':
                $this->reference_id = $request->get('code');
                if(! $this->__exist_user($request)) {
                    $response = [
                        'msg' => "Account not found.",
                        'status' => FALSE,
                        'status_code' => "CODE_NOT_FOUND",
                        'hint' => "Make sure the 'code' from your request parameter exists and valid."
                    ];
                }
            break;

            case 'article':
                $this->reference_id = $request->get('article_id');
                if (!$this->__exist_article($request)) {
                    $response = [
                        'msg' => "Article not found.",
                        'status' => FALSE,
                        'status_code' => "ARTICLE_NOT_FOUND",
                        'hint' => "Make sure the 'article_id' from your request parameter exists and valid."
                    ];
                }
            break;

            case 'own_article':
                $this->reference_id = $request->get('article_id');
                if (!$this->__exist_own_article($request)) {
                    $response = [
                        'msg' => "Article not found.",
                        'status' => FALSE,
                        'status_code' => "ARTICLE_NOT_FOUND",
                        'hint' => "Make sure the 'article_id' from your request parameter exists and valid."
                    ];
                }
            break;
        }

        if(empty($response)) {
            return $next($request);
        }

        callback:
        switch(Str::lower($this->format)){
            case 'json' :
                return response()->json($response, 406);
            break;
            case 'xml' :
                return response()->xml($response, 406);
            break;
        }
    }

    private function __exist_user($request){
        $code = Str::upper($this->reference_id);
        $user = User::whereRaw("UPPER(code) = '{$code}'")->first();

        if($user){
            $request->merge(['user_data' => $user]);
            return TRUE;
        }

        return FALSE;
    }

    private function __exist_article($request)
    {
        $code = Str::upper($this->reference_id);
        $article = Article::find($code);

        if ($article) {
            $request->merge(['article_data' => $article]);
            return TRUE;
        }

        return FALSE;
    }

    private function __exist_own_article($request)
    {
        $code = Str::upper($this->reference_id);
        $auth = $request->user('api');

        $article = Article::where('user_id', $auth->id)->where('id', $code)->first();

        if ($article) {
            $request->merge(['article_data' => $article]);
            return TRUE;
        }

        return FALSE;
    }
}
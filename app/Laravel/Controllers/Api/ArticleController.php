<?php 

namespace App\Laravel\Controllers\Api;


/* Request validator
 */
use App\Laravel\Requests\PageRequest;
use App\Laravel\Requests\Api\{ArticleRequest};


/* Models
 */
use App\Laravel\Models\{Article};


/* Data Transformer
 */
use App\Laravel\Transformers\{TransformerManager,ArticleTransformer};

/* App classes
 */
use Illuminate\Support\Facades\Auth;
use Carbon,DB,Str,Helper,ImageUploader;

class ArticleController extends Controller{
	protected $response = [];
	protected $response_code;
	protected $guard = 'api';


	public function __construct(){
		$this->response = array(
			"msg" => "Bad Request.",
			"status" => FALSE,
			'status_code' => "BAD_REQUEST"
			);
		$this->response_code = 400;
		$this->transformer = new TransformerManager;
	}

	public function index(PageRequest  $request,$format = NULL){
		$per_page = $request->get('per_page',10);
		$auth = $request->user($this->guard);

		$articles = Article::where("user_id", $auth->id)
							->orderBy('updated_at',"DESC")
							->paginate($per_page);

		$this->response['status'] = TRUE;
		$this->response['status_code'] = "ARTICLE_LIST";
		$this->response['msg'] = "Article list.";
		$this->response['has_morepages'] = $articles->hasMorePages();
		$this->response['total'] = $articles->total();
		$this->response['data'] = $this->transformer->transform($articles,new ArticleTransformer,'collection');
		$this->response_code = 200;

		callback:
		switch(Str::lower($format)){
		    case 'json' :
		        return response()->json($this->response, $this->response_code);
		    break;
		    case 'xml' :
		        return response()->xml($this->response, $this->response_code);
		    break;
		}
	}

	public function show(PageRequest $request,$format = NULL){
		$article = $request->get('article_data');

		$this->response['status'] = TRUE;
		$this->response['status_code'] = "ARTICLE_DETAIL";
		$this->response['msg'] = "Article detail.";
		$this->response['data'] = $this->transformer->transform($article, new ArticleTransformer, 'item');
		$this->response_code = 200;

		callback:
		switch(Str::lower($format)){
		    case 'json' :
		        return response()->json($this->response, $this->response_code);
		    break;
		    case 'xml' :
		        return response()->xml($this->response, $this->response_code);
		    break;
		}
	}

	public function store(ArticleRequest $request,$format = NULL){
		$auth = $request->user($this->guard);

		DB::beginTransaction();
		try{

			$article = new Article;
			$article->setConnection(env('WRITER_DB_CONNECTION'));

			$article->user_id = $auth->id;
			$article->name = $request->input('name');
			$article->description = $request->input('description');

			if ($request->hasFile('image')) {
				$image = ImageUploader::upload($request->file('image'), "uploads/articles/images");

				$article->path = $image['path'];
				$article->directory = $image['directory'];
				$article->filename = $image['filename'];
				$article->source = $image['source'];
			}

			$article->save();

			DB::commit();

			$this->response['status'] = TRUE;
			$this->response['status_code'] = "ARTICLE_CREATED";
			$this->response['msg'] = "Article was successfully created.";
			$this->response_code = 201;
		}catch(\Exception $e){
			DB::rollback();

			Log::info("ERROR: ", array($e));

			$this->response['status'] = FALSE;
			$this->response['status_code'] = "SERVER_ERROR";
			$this->response['msg'] = "Server Error: Code #{$e->getMessage()}";
			$this->response_code = 500;
		}

		callback:
		switch(Str::lower($format)){
		    case 'json' :
		        return response()->json($this->response, $this->response_code);
		    break;
		    case 'xml' :
		        return response()->xml($this->response, $this->response_code);
		    break;
		}	
	}

	public function update(ArticleRequest $request,$format = NULL){
		$auth = $request->user($this->guard);
		$article = $request->get('article_data');

		DB::beginTransaction();
		try{

			$article->setConnection(env('WRITER_DB_CONNECTION'));

			$article->user_id = $auth->id;
			$article->name = $request->input('name');
			$article->description = $request->input('description');

			if ($request->hasFile('image')) {
				$image = ImageUploader::upload($request->file('image'), "uploads/articles/images");

				$article->path = $image['path'];
				$article->directory = $image['directory'];
				$article->filename = $image['filename'];
				$article->source = $image['source'];
			}

			$article->save();

			DB::commit();

			$this->response['status'] = TRUE;
			$this->response['status_code'] = "ARTICLE_UPDATED";
			$this->response['msg'] = "Article was successfully updated.";
			$this->response['data'] = $this->transformer->transform($article, new ArticleTransformer, 'item');
			$this->response_code = 200;
		}catch(\Exception $e){
			DB::rollback();

			Log::info("ERROR: ", array($e));

			$this->response['status'] = FALSE;
			$this->response['status_code'] = "SERVER_ERROR";
			$this->response['msg'] = "Server Error: Code #{$e->getMessage()}";
			$this->response_code = 500;
		}

		callback:
		switch(Str::lower($format)){
		    case 'json' :
		        return response()->json($this->response, $this->response_code);
		    break;
		    case 'xml' :
		        return response()->xml($this->response, $this->response_code);
		    break;
		}	
	}

	public function destroy(PageRequest $request,$format = NULL){
		$auth = $request->user($this->guard);
		$article = $request->get('article_data');

		DB::beginTransaction();
		try{

			$article->setConnection(env('WRITER_DB_CONNECTION'));
			$article->delete();

			DB::commit();

			$this->response['status'] = TRUE;
			$this->response['status_code'] = "ARTICLE_REMOVED";
			$this->response['msg'] = "Article was successfully removed.";
			$this->response_code = 200;
		}catch(\Exception $e){
			DB::rollback();

			Log::info("ERROR: ", array($e));

			$this->response['status'] = FALSE;
			$this->response['status_code'] = "SERVER_ERROR";
			$this->response['msg'] = "Server Error: Code #{$e->getMessage()}";
			$this->response_code = 500;
		}

		callback:
		switch(Str::lower($format)){
		    case 'json' :
		        return response()->json($this->response, $this->response_code);
		    break;
		    case 'xml' :
		        return response()->xml($this->response, $this->response_code);
		    break;
		}	
	}
}
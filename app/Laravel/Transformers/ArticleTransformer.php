<?php 

namespace App\Laravel\Transformers;

use Str,Carbon,Helper;

use App\Laravel\Models\Article;

use League\Fractal\TransformerAbstract;

class ArticleTransformer extends TransformerAbstract{

	protected $availableIncludes = [];


	public function transform(Article $article) {
	    return [
	     	'article_id' => $article->id ?: 0,

	     	'name' => $article->name ?: "",
	     	'description' => $article->description ?: "",

	     	'date_created' => [
	     		'date_db' => $article->created_at ? $article->created_at->format("Y-m-d") : '',
	     		'month_year' => $article->created_at ? $article->created_at->format("F Y") : '',
	     		'time_passed' => $article->created_at ? Helper::time_passed($article->created_at) : '',
	     		'timestamp' => $article->created_at ? $article->created_at : '',
	     	],

 			'image' => [
				'path' => $article->directory ?: "",
				'filename' => $article->filename ?: "",
				'path' => $article->path ?: "",
				'directory' => $article->directory ?: "",
				'full_path' => strlen($article->filename) > 0 ? "{$article->directory}/resized/{$article->filename}" : "",
				'thumb_path' => strlen($article->filename) > 0 ? "{$article->directory}/thumbnails/{$article->filename}" : "",
			],
	    ];
	}
}
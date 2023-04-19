<?php 

namespace App\Laravel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Str;

class Article extends Model{
	
	use SoftDeletes;
	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = "article";

	/**
	 * The database connection used by the model.
	 *
	 * @var string
	 */
	protected $connection = "reader_db";

	/**
	 * Enable soft delete in table
	 * @var boolean
	 */
	protected $softDelete = true;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [];


	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [];

	/**
	 * The attributes that created within the model.
	 *
	 * @var array
	 */
	protected $appends = [];

	protected $dates = [];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
	];

	public function getDirectoryAttribute($value){
		$updated_directory = str_replace(env("AWS_S3_URL"), env("AWS_S3_CDN"), $value);
		return $updated_directory;
	}

	public function getCdnDirectoryAttribute($value){
		$updated_directory = str_replace(env("AWS_S3_URL"), env("AWS_S3_CDN"), $value);
		return $updated_directory;
	}
}
<?php
namespace Model;
use Illuminate\Database\Eloquent\Model as Eloquent;
class temp_import extends Eloquent {
  protected $table = "temp_import";
  protected $fillable = ["product_id", "storage_id", "amount"];
  public $timestamps = false;
}
?>
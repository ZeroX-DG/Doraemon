<?php
namespace Model;
use Illuminate\Database\Eloquent\Model as Eloquent;
class Products extends Eloquent {
  protected $table = "products";
  protected $fillable = ["Name"];
  public $timestamps = false;
}
?>
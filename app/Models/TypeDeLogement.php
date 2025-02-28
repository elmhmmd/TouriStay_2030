<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeDeLogement extends Model
{
    protected $table = 'type_de_logements'; 

    protected $fillable = ['name'];

    public function annonces()
    {
        return $this->hasMany(Annonce::class);
    }
}
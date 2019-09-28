<?php

namespace App\Http\Controllers;

use App\Models\Planta;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Request;

class PlantasController extends Controller
{
    public function postulador()
    {
        $query = QueryBuilder::for(Planta::class)
            ->with('campo')
            ->allowedFilters([
                Filter::scope('search'),
                Filter::scope('campo')
            ]);
        return new Resource($query->get());
    }
}

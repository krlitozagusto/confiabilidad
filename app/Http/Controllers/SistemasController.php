<?php

namespace App\Http\Controllers;

use App\Models\Sistema;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Request;

class SistemasController extends Controller
{
    public function postulador()
    {
        $query = QueryBuilder::for(Sistema::class)
            ->with('planta')
            ->allowedFilters([
                Filter::scope('search'),
                Filter::scope('campo')
            ]);
        return new Resource($query->get());
    }
}

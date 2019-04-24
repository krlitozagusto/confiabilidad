<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equipo;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\Input;

class EquiposController extends Controller
{
    public function postulador()
    {
        $query = QueryBuilder::for(Equipo::class)
            ->with('ubicacion_tecnica')
            ->allowedFilters([
                Filter::scope('search')
            ]);
        return new Resource($query->get());
    }
}

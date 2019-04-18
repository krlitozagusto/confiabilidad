<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\Input;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class EventosController extends Controller
{
    public function panel()
    {
        $perPage = Input::get('per_page');

        $query = QueryBuilder::for(Evento::class)
            ->with('tipo_evento', 'equipo')
            ->allowedFilters([
                Filter::scope('search')
            ])
            ->allowedSorts('id', 'evento_padre_id', 'estado');

        if($perPage){
            return new Resource($query->paginate($perPage));
        }
        return new Resource($query->get());
    }
}

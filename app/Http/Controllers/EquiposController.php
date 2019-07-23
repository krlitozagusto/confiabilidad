<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use Illuminate\Http\Request;
use App\Models\Equipo;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\Input;


class EquiposController extends Controller
{

    public function panel()
    {
        $perPage = Input::get('per_page');

        $query = QueryBuilder::for(Equipo::class)
            ->with('eventos', 'valoracion_ram')
            ->allowedFilters([
                Filter::scope('search')
            ])
            ->allowedSorts('id', 'nombre', 'tag', 'numero_equipo', 'estado');

        if($perPage){
            return new Resource($query->paginate($perPage));
        }
        return new Resource($query->get());
    }

    public function postulador()
    {
        $query = QueryBuilder::for(Equipo::class)
            ->with('sistema.planta.campo')
            ->allowedFilters([
                Filter::scope('search')
            ]);
        return new Resource($query->get());
    }

    public function getMachine(Request $request)
    {
        return response()->json([
            'equipo' => Equipo::where('id','=',$request->id)->with([
                'sistema',
                'eventos.tipo_evento',
                'valoracion_ram',
                'planes_mantenimiento',
                'centro_costo'
            ])->first()
        ]);
    }

    public function getContratos()
    {
        return response()->json([
            'contratos' => Contrato::with([
                'campos.plantas.sistemas.equipos'
            ])->get()
        ]);
    }
}

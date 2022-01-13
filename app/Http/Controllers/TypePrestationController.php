<?php

namespace App\Http\Controllers;

use App\Models\TypePrestation;
use Exception;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class TypePrestationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.type_prestation');
    }

    public function gettypeprestationList(Request $request)
    {
        $data = TypePrestation::latest()->get();
        return \Yajra\DataTables\DataTables::of($data)
            ->addIndexColumn()
            ->addColumn("id", function ($data) {
                return $data->id;
            })
            ->addColumn("updated_at", function ($data) {
                return $data->created_at;
            })
            ->editColumn("libelle", function ($data) {
                return
                    "<div class='user-card'>
                <div class='user-avatar bg-dim-primary d-none d-sm-flex'>
                    <span>TYP</span>
                </div>
                <div class='user-info'>
                    <span class='tb-lead'>$data->libelle</span>
                </div>
            </div>";;
            })
            ->addColumn("montant", function ($data) {
                return $data->montant . ' FCFA';
            })
            ->addColumn('Actions', function ($data) {
                return '<ul class="nk-tb-actions gx-1">
               
                <li class="nk-tb-action-hidden">
                    <a href="' . route('typeprestation.edit', $data->id) . '" class="btn btn-trigger btn-icon" data-toggle="tooltip" data-placement="top" title="Modifier">
                        <em class="icon ni ni-edit"></em>
                    </a>
                </li>
                <li class="nk-tb-action-hidden">
                    <a href="' . route('typeprestation.delete', $data->id) . '" class="btn btn-trigger btn-icon" data-toggle="tooltip" data-placement="top" title="Supprimer">
                       <em class="icon ni ni-trash"></em>
                    </a>
                </li>
                <li>
                    <div class="drodown">
                        <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <ul class="link-list-opt no-bdr">
                                <li><a href="' . route('typeprestation.edit', $data->id) . '" > <em class="icon ni ni-edit"></em><span>Modifier</span></a></li>
                                 
                                <li><a href="' . route('typeprestation.delete', $data->id) . '" ><em class="icon ni ni-trash"></em><span>Supprimer</span></a></li>
                            </ul>
                        </div>
                    </div>
                </li>
            </ul>';
            })->setRowClass("nk-tb-item")
            ->rawColumns(['libelle', 'Actions', 'status'])
            ->make(true);
    }

    public function getTypePrestations(Request $request)
    {
        $data = TypePrestation::select("id", "libelle", "montant")->where(function ($query) use ($request) {
            $query->where('libelle', 'like', '%' . $request->search . '%');
        })->limit(5)->get();
        $resp = array();
        foreach ($data as $e) {
            $resp[] = array('id' => $e->id . '|' . $e->montant, 'text' => $e->libelle, 'value' => $e->id . '|' . $e->montant);
        }
        return response()->json($resp);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.add_type_prestation');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = FacadesValidator::make($request->all(), [
            'libelle' => ['required', 'string', 'max:255', 'unique:type_prestations,libelle'],
            'montant' => ['required', 'numeric'],
        ]);
        if ($validator->fails()) {
            return response()
                ->json(['errors' => $validator->errors()->all()]);
        }
        try {
            DB::beginTransaction();
            $typeprestation  = new TypePrestation();
            $typeprestation['libelle'] = $request['libelle'];
            $typeprestation['montant'] = $request['montant'];
            $typeprestation->save();
            DB::commit();

            return response()->json(["success" => "Enregistrement éffectuer !"]);
        } catch (Exception $e) {
            return response()->json(["error" => "Une erreur s'est produite."]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $typeprestation = TypePrestation::findOrFail($id);
        return view('pages.add_type_prestation', ['typeprestation' => $typeprestation]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = FacadesValidator::make($request->all(), [
            'libelle' => [
                'required', 'string', 'max:255', Rule::unique('type_prestations', 'libelle')->where(function ($query) use ($request) {
                    $query->where('id', '!=', $request['id']);
                })
            ],
            'montant' => ['required', 'numeric'],
        ]);
        if ($validator->fails()) {
            return response()
                ->json(['errors' => $validator->errors()->all()]);
        }
        try {
            DB::beginTransaction();
            $typeprestation = TypePrestation::findOrFail($id);
            $typeprestation['libelle'] = $request['libelle'];
            $typeprestation['montant'] = $request['montant'];
            $typeprestation->save();
            DB::commit();

            return response()->json(["success" => "Enregistrement éffectuer !"]);
        } catch (Exception $e) {
            return response()->json(["error" => "Une erreur s'est produite."]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            TypePrestation::findOrFail($id)->delete();
            return back()->with("status", "Suppression éffectuer avec succès");
        } catch (Exception $e) {
            return response()->json(["error" => "Une erreur s'est produite."]);
        }
    }
}
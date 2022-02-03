<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Cotisation;
use App\Models\Prestation;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.users');
    }

    public function getUser(Request $request)
    {
        try {
            $data = DB::select(DB::raw('select us.id,us.sexe, us.nom, us.prenom, us.created_at, us.profile_photo_path, us.matricule, us.agence, us.tel, us.nationalité, us.status, ca.montant, ca.libelle from users us, categories ca where ca.id=us.categories_id'));
            return \Yajra\DataTables\DataTables::of($data)
                ->addIndexColumn()
                ->addColumn("id", function ($data) {
                    return $data->id;
                })
                ->addColumn("updated_at", function ($data) {
                    return $data->created_at;
                })
                ->editColumn("matricule", function ($data) {
                    if (isset($data->profile_photo_path)) {
                        return '<div class="user-card">
                <div class="user-avatar bg-dim-primary d-none d-sm-flex">
                     <img class="object-cover w-8 h-8 rounded-full" src="' . asset('picture_profile/' . $data->profile_photo_path)  . '" alt="" />
                </div>
                <div class="user-info">
                    <span class="tb-lead">' . $data->matricule . '</span>
                </div>
            </div>';
                    } else {
                        return '<div class="user-card">
                <div class="user-avatar bg-dim-primary d-none d-sm-flex">
                     <img class="object-cover w-8 h-8 rounded-full" src="https://ui-avatars.com/api/?name=' . $data->nom . '&background=1ee0ac&color=fff" alt="" />
                </div>
                <div class="user-info">
                    <span class="tb-lead">' . $data->matricule . '</span>
                </div>
            </div>';
                    }
                })
                ->addColumn("nom", function ($data) {
                    return $data->nom;
                })
                ->addColumn("prenom", function ($data) {
                    return $data->prenom;
                })
                ->addColumn("sexe", function ($data) {
                    return $data->sexe;
                })
                ->addColumn("nationalité", function ($data) {
                    return $data->nationalité;
                })
                ->addColumn("agence", function ($data) {
                    return $data->agence;
                })
                // ->addColumn("email", function ($data) {
                //     return $data->email;
                // })
                ->addColumn("tel", function ($data) {
                    return $data->tel;
                })
                ->addColumn("category", function ($data) {
                    return $data->libelle;
                })
                // ->addColumn("dateNais", function ($data) {
                //     return $data->role;
                // })
                // ->addColumn("dateRecru", function ($data) {
                //     return $data->role;
                // })
                // ->addColumn("dateHade", function ($data) {
                //     return $data->role;
                // }) 
                ->editColumn("status", function ($data) {
                    if ($data->status) {
                        return ' <td class="nk-tb-col tb-col-md">
                    <span class="badge badge-outline-success">Actif</span>
                </td>';
                    } else {
                        return ' <td class="nk-tb-col tb-col-md">
                <span class="badge badge-outline-danger">Inactif</span>
            </td>';
                    }
                })
                ->addColumn('Actions', function ($data) {
                    return '<ul class="nk-tb-actions gx-1">
               
                <li class="nk-tb-action-hidden">
                    <a href="' . route('membre.edit', $data->id) . '" class="btn btn-trigger btn-icon" data-toggle="tooltip" data-placement="top" title="Modifier">
                        <em class="icon ni ni-edit"></em>
                    </a>
                </li>
                <li class="nk-tb-action-hidden">
                    <a href=""  data_id="' . $data->id . '" class="btn btn-trigger btn-icon delete-data-user" data-toggle="tooltip" data-placement="top" title="Supprimer">
                       <em class="icon ni ni-trash"></em>
                    </a>
                </li>
                 <li class="nk-tb-action-hidden">
                    <a href="" data_id="' . $data->id . '" class="btn btn-trigger btn-icon dbauth-delete" data-toggle="tooltip" data-placement="top" title="Supprimer la double authentification">
                        <em class="icon ni ni-reload-alt"></em>
                    </a>
                </li>
                <li class="nk-tb-action-hidden">
                    <a href="' . route('ayantsdroits.create', $data->id) . '" class="btn btn-trigger btn-icon" data-toggle="tooltip" data-placement="top" title="Ajouter un ayant droit">
                      <em class="icon ni ni-user-add-fill"></em>
                    </a>
                </li>
                 <li class="nk-tb-action-hidden">
                    <a href="' . route('prestation.create', $data->id) . '" class="btn btn-trigger btn-icon" data-toggle="tooltip" data-placement="top" title="Ajouter une prestation">
                      <em class="icon ni ni-property-add"></em>
                    </a>
                </li>
                  <li class="nk-tb-action-hidden">
                    <a href="' . route('membre.info', $data->id) . '" class="btn btn-trigger btn-icon" data-toggle="tooltip" data-placement="top" title="Detail du membre">
                      <em class="icon ni ni-expand"></em>
                    </a>
                </li>
                <li>
                    <div class="drodown">
                        <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <ul class="link-list-opt no-bdr">
                                <li><a href="' . route('membre.edit', $data->id) . '" > <em class="icon ni ni-edit"></em><span>Modifier</span></a></li>
                                <li><a href="" class="delete-data-user" data_id="' . $data->id . '" ><em class="icon ni ni-trash"></em><span>Supprimer</span></a></li>
                                <li><a href="" class="delete-data-user" data_id="' . $data->id . '" ><em class="icon ni ni-reload-alt"></em><span>Supprimer la double authentification</span></a></li>
                                <li><a href="' . route('ayantsdroits.create', $data->id) . '" > <em class="icon ni ni-user-add-fill"></em><span>Ajouter un ayant droit</span></a></li>
                                <li><a href="' . route('prestation.create', $data->id) . '" > <em class="icon ni ni-property-add"></em><span>Ajouter une prestation</span></a></li>
                                <li><a href="' . route('membre.info', $data->id) . '" > <em class="icon ni ni-expand"></em><span>Detail du membre</span></a></li>
                            </ul>
                        </div>
                    </div>
                </li>
            </ul>';
                })->setRowClass("nk-tb-item")
                ->rawColumns(['matricule', 'Actions', 'status'])
                ->make(true);
        } catch (Exception $e) {
            return response()->json(["error" => "Une erreur s'est produite."]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function infomembre(Request $request)
    {
        try {
            if ($request->id != null) {
                $membre = User::select('id', 'nom', 'prenom', 'matricule', 'tel', 'email', 'date_hadésion', 'nationalité', 'agence', 'sexe', 'categories_id', 'date_naissance', 'date_recrutement', 'profile_photo_path')->where('id', $request->id)->first();
                $totalcotisation = Cotisation::where('users_id', '=', $request->id)->sum('montant');
                $totalprestation = Prestation::where('users_id', '=', $request->id)->sum('montant');
                $cat = Category::find($membre->categories_id);
                return view('pages.informationmembre', ['membre' => $membre, 'category' => $cat, 'totalCotisation' => number_format($totalcotisation, 0, ',', ' '), 'totalPrestation' => number_format($totalprestation, 0, ',', ' ')]);
            } else {
                abort(404);
            }
        } catch (Exception $e) {
            return response()->json(["error" => "Une erreur s'est produite."]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.addusers');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $attributeNames = array(
                'email' => 'email',
                'role' => 'role',
                'tel' => 'numéro de téléphone',
                'dateNaissance' => 'date de naissance',
                'dateRecrutement' => 'date de recrutement',
                'dateHadhésion' => 'date d\'hadésion',
                'tel' => 'numéro de téléphone',
                'listCategorie' => 'Categorie',
            );
            $validator = FacadesValidator::make($request->all(), [
                'matricule' => ['required', 'max:255', 'unique:users,matricule'],
                'nom' => ['required', 'string', 'max:255'],
                'prenom' => ['required', 'string', 'max:255'],
                'nationalité' => ['required', 'string', 'max:255'],
                'sexe' => ['required', 'string', 'max:10'],
                'agence' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'tel' => ['required', 'string', 'max:25', 'unique:users,tel'],
                'dateNaissance' => ['required', 'date', 'date_format:Y-m-d'],
                'dateRecrutement' => ['required', 'date', 'date_format:Y-m-d'],
                'dateHadhésion' => ['required', 'date', 'date_format:Y-m-d'],
                'listCategorie' => ['required', 'integer'],
                'role' => ['required', 'string'],
            ]);
            // dd(date('Y-m-d', strtotime(now())));
            $validator->setAttributeNames($attributeNames);
            if ($validator->fails()) {
                return response()
                    ->json(['errors' => $validator->errors()->all()]);
            }
            DB::beginTransaction();
            $user  = new User();
            $user['matricule'] = $request['matricule'];
            $user['nom'] = $request['nom'];
            $user['prenom'] = $request['prenom'];
            $user['nationalité'] = $request['nationalité'];
            $user['agence'] = $request['agence'];
            $user['email'] = $request['email'];
            $user['tel'] = $request['tel'];
            $user['sexe'] = $request['sexe'];
            $user['date_naissance'] = date("Y-m-d", strtotime($request['dateNaissance']));
            $user['date_hadésion'] = date("Y-m-d", strtotime($request['dateRecrutement']));
            $user['date_recrutement'] = date("Y-m-d", strtotime($request['dateHadhésion']));
            $user['role'] = $request['role'];
            $user['categories_id'] = $request['listCategorie'];
            $user['theme'] = 0;
            $user['status'] = 1;
            $user['email_verified_at'] = now();
            $user['password'] = Hash::make('1234');
            if ($request->hasFile('picture') && $request->file('picture')->isValid()) {
                $path3 = $request->file('picture')->store('public/images/picture_profile');
                $user['profile_photo_path'] = basename($path3);
            }

            $user->save();
            DB::commit();

            return response()->json(["success" => "Enregistrement éffectuer !"]);
        } catch (Exception $e) {
            return response()->json(["error" => "Une erreur s'est produite."]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $membre)
    {
        try {
            $category = Category::select('code', 'libelle')->where('id', '=', $membre->categories_id)->first();
            return view('pages.addusers', ['user' => $membre, 'category' => $category]);
        } catch (Exception $e) {
            return response()->json(["error" => "Une erreur s'est produite."]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        try {
            $attributeNames = array(
                'email' => 'email',
                'role' => 'role',
                'tel' => 'numéro de téléphone',
                'dateNaissance' => 'date de naissance',
                'dateRecrutement' => 'date de recrutement',
                'dateHadhésion' => 'date d\'hadésion',
                'tel' => 'numéro de téléphone',
                'listCategorie' => 'Categorie',
            );
            $validator = FacadesValidator::make($request->all(), [
                'matricule' => ['required', 'max:255', Rule::unique('users', 'matricule')->where(function ($query) use ($request) {
                    $query->where('id', '!=', $request['id']);
                })],
                'nom' => ['required', 'string', 'max:255'],
                'prenom' => ['required', 'string', 'max:255'],
                'nationalité' => ['required', 'string', 'max:255'],
                'agence' => ['required', 'string', 'max:255'],
                'sexe' => ['required', 'string', 'max:10'],
                'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->where(function ($query) use ($request) {
                    $query->where('id', '!=', $request['id']);
                })],
                'tel' => ['required', 'string', 'max:25', Rule::unique('users', 'tel')->where(function ($query) use ($request) {
                    $query->where('id', '!=', $request['id']);
                })],
                'dateNaissance' => ['required', 'date', 'date_format:Y-m-d'],
                'dateRecrutement' => ['required', 'date', 'date_format:Y-m-d'],
                'dateHadhésion' => ['required', 'date', 'date_format:Y-m-d'],
                'listCategorie' => ['required', 'integer'],
                'role' => ['required', 'string'],
                // 'picture' => ['max:2048', 'file', 'mimes:jpeg,png,doc,docs,pdf']
            ]);
            $validator->setAttributeNames($attributeNames);
            if ($validator->fails()) {
                return response()
                    ->json(['errors' => $validator->errors()->all()]);
            }
            DB::beginTransaction();
            $user = User::find($request['id']);
            $user['matricule'] = $request['matricule'];
            $user['nom'] = $request['nom'];
            $user['prenom'] = $request['prenom'];
            $user['nationalité'] = $request['nationalité'];
            $user['agence'] = $request['agence'];
            $user['email'] = $request['email'];
            $user['sexe'] = $request['sexe'];
            $user['tel'] = $request['tel'];
            $user['date_naissance'] = date("Y-m-d", strtotime($request['dateNaissance']));
            $user['date_hadésion'] = date("Y-m-d", strtotime($request['dateRecrutement']));
            $user['date_recrutement'] = date("Y-m-d", strtotime($request['dateHadhésion']));
            $user['role'] = $request['role'];
            $user['categories_id'] = $request['listCategorie'];
            $expath = $user['profile_photo_path'];
            if ($request->hasFile('picture') && $request->file('picture')->isValid()) {
                $path3 = $request->file('picture')->store('public/images/picture_profile');
                $user['profile_photo_path'] = basename($path3);
            }

            $user->save();
            DB::commit();
            if ($request->hasFile('picture') && $request->file('picture')->isValid()) {
                if (Storage::exists('public/images/picture_profile/' . $expath)) {
                    Storage::delete('public/images/picture_profile/' . $expath);
                }
            }
            return response()->json(["success" => "Modification éffectuer !"]);
        } catch (Exception $e) {
            return response()->json(["error" => "Une erreur s'est produite."]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    // public function destroy(User $user)
    // {
    //     User::find($user->id)->delete();
    //     return back()->with("status", "Suppression éffectuer avec succès");
    // }

    public function deletemembre(Request $request)
    {
        try {
            User::find($request->id)->delete();
            return response()->json(["success" => "Suppression éffectuer avec succès"]);
        } catch (Exception $e) {
            return response()->json(["error" => "Une erreur s'est produite."]);
        }
    }

    public function doubleauthdelete(Request $request)
    {
        try {
            $user = User::find($request->id)->first();
            $user->two_factor_secret = null;
            $user->two_factor_recovery_codes = null;
            $user->save();
            return response()->json(["success" => "Suppression de la double authentification éffectuer avec succès"]);
        } catch (Exception $e) {
            return response()->json(["error" => "Une erreur s'est produite."]);
        }
    }
}
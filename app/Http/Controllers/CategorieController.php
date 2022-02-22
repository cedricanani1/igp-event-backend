<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Http\Requests\StoreCategorieRequest;
use App\Http\Requests\UpdateCategorieRequest;

class CategorieController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $producttypes =  Categorie::with('child','parent','products')->get();
        foreach ($producttypes as $key => $type) {
            foreach ($type->products as $key => $value) {
                $value->type;
            }
        }
        return response()->json($producttypes);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCategorieRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCategorieRequest $request)
    {
        $types =  new Categorie;
        $types->libelle =  $request->libelle;
        $types->slug = str_replace(" ","-",$request->libelle);
        $types->description =  $request->description;
        $types->parent_id =  $request->parent_id;
        $types->save();
        if ($types) {
            return response()->json([
                'state' =>true
            ]);
        }else{
            return response()->json([
                'state' =>false
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Categorie  $categorie
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cat = Categorie::find($id);
        $cat->products;
        $cat->child;
        return response()->json($cat);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Categorie  $categorie
     * @return \Illuminate\Http\Response
     */
    public function edit(Categorie $categorie)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCategorieRequest  $request
     * @param  \App\Models\Categorie  $categorie
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCategorieRequest $request,  $id)
    {
        $categorie = Categorie::find($id);
        $categorie->libelle =  $request->libelle;
        $categorie->slug =  str_replace(" ","-",$request->libelle);
        $categorie->description =  $request->description;
        $categorie->parent_id =  $request->parent_id;
        $categorie->save();
        if ($categorie) {
            return response()->json([
                'state' =>true
            ]);
        }else{
            return response()->json([
                'state' =>false
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Categorie  $categorie
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $categorie = Categorie::find($id);
        if (count($categorie->products) > 0) {
            return response()->json([
                'state' =>false,
                'message' =>'Cette contient des élements en son sein'
            ]);
        }else{
            $categorie->delete();
            return response()->json([
                'state' =>true,
            ]);
        }
        
        return response()->json($categorie);
    }
}

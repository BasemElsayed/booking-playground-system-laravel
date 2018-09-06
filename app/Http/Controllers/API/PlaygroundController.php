<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\Playground;
use Validator;

class PlaygroundController extends Controller
{
    public $successStatus = 200;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function viewAll()
    {
        //
        $playgrounds = Playground::all();
        return response()->json($playgrounds, $this-> successStatus);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    
     public function addPlayground(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'name' => 'required', 
            'price' => 'required', 
            'address' => 'required',
            'area' => 'required',
            'avaiableFrom' => 'required',
            'avaiableTo' => 'required',
        ]);
        
        if ($validator->fails()) 
        { 
            return response()->json($validator->errors(), 401);            
        }

        $playground = new Playground();
        if($request->hasFile('imageURL'))
        {
            $image = $request->file('image');
            $name = str_slug($request->name) . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/images');
            $imagePath = $destinationPath . '/' . $name;
            $image->move($destinationPath, $name);
            $playground->imageURL = $name;
        }

        $playground->name = $request->get('name');
        $playground->price = $request->get('price');
        $playground->address = $request->get('address');
        $playground->area = $request->get('area');
        $playground->avaiableFrom = $request->get('avaiableFrom');
        $playground->avaiableTo = $request->get('avaiableTo');

        $playground->save();
         
        $success['name'] =  $playground->name;
        return response()->json($success, $this-> successStatus); 
    }

    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Playground  $playground
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $playground = Playground::find($id);
        if(!$playground)
        {
            return response()->json('Empty Playground', 401); 
        }

        return response()->json($playground, $this-> successStatus);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Playground  $playground
     * @return \Illuminate\Http\Response
     */
    public function edit(Playground $playground)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Playground  $playground
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {


        $playground = Playground::findOrFail($id);
        $input = $request->all(); 
        $playground->update($input);
        
        return $playground;

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Playground  $playground
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        //
        $playground = Playground::find($id);
        if(!$playground)
        {
            return response()->json('Empty Playground', 401); 
        }
        $playground->delete();

        return response()->json('Deleted Successfully', $this-> successStatus);
    }
}

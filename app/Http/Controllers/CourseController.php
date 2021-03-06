<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Purchase;
use App\Models\Module;
use App\Models\CoursePurchase;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Course::with('modules')->where('featured', 1)->get();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        $free = Course::with('modules')->where('free', 1)->limit(5)->get();
        $online = Course::where('modality','online')->limit(5)->get();
        $onsite = Course::where('modality','presencial')->limit(5)->get();

        $data = new \stdClass;
        $data->free = $free;
        $data->online = $online;
        $data->onsite = $onsite;
        return json_encode($data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        if ($request->modality) {
            return Course::with('modules')->where('modality', $request->modality)->get();
        } if ($request->price) {
            return Course::with('modules')->where('free', 1)->get();
        } else {
            return Course::with('modules')->get();
        }
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
        //
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
        //
    }

    /**
     * Retrieve my courses.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function myModules(Request $request)
    {
        $user = $request->user();
        return CoursePurchase::with('module')->where('user_id', $user->id)->get();
    }


    /**
     * Get module.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function module(Request $request)
    {
        $user = $request->user();
        //  return Module::find($request->module_id);
        $purchased = CoursePurchase::where('user_id', $user->id)->where('product_id', $request->module_id)->count();
        if ($purchased) {
            return Module::with('course')->with('course.modules')->find($request->module_id);
        } else {
            return response()->json([
            'message' => 'No haz comprado este modulo'
        ], 404);
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
        //
    }
}

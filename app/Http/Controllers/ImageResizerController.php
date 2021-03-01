<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class ImageResizerController extends Controller
{
    /**
     * Image Resizer passing a image url, width & height values
     *
     * @param Request $request
     * @return void
     */
    public function index(Request $request) {
        
        $validator = Validator::make($request->all(), [
            'img' => 'required|string',
            'height' => 'nullable|integer',
            'width' => 'nullable|integer',
            'isLocalResource' => 'nullable|boolean'
        ]);
        if ($validator->fails())
            return response($validator->errors()->getMessages(), 400);
        if (!$request->width && !$request->height) {
            return response(['message' => 'Must especify width or hieght of image'], 400);
        }
        if ( $request->isLocalResource && !Storage::disk('local')->exists($request->img) ) {
            return response(['message' => 'image_to_resize_not_found'], 404);
        }
        if ( !$request->isLocalResource ){
            $validator = Validator::make($request->all(), [
                'img' => 'required|string|url'
            ]);
            if ($validator->fails())
                return response($validator->errors()->getMessages(), 400);
        }
        logger($request);
        $img = $request->isLocalResource ? 
            Image::make(Storage::disk('local')->get( $request->img )) : Image::make($request->img);
        if (!$request->width){
            // resize the image to a height of X and constrain aspect ratio (auto width)
            $img->fit(null, $request->height, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize(); // prevent possible upsizing
            });
        } else if (!$request->height){ 
            // resize the image to a width of X and constrain aspect ratio (auto height)
            $img->fit($request->width, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize(); // prevent possible upsizing
            });
        } else { 
            // simple image resize 
            $img->fit($request->width, $request->height);
        }
        return $img->response('jpg');
    }

}

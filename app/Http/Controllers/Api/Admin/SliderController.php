<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Slider;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\SliderResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SliderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get sliders
        $sliders = Slider::latest()->paginate(5);
       
        //return with Api Resource
        return new SliderResource(true, 'List Data Sliders', $sliders);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,jpg,png|max:2000',
            'link'  => 'nullable|url'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            //upload image
            $image = $request->file('image');
            $image->storeAs('public/sliders', $image->hashName());

            //create slider
            $slider = Slider::create([
                'image' => $image->hashName(),
                'link'  => $request->link,
            ]);

            //return success with Api Resource
            return new SliderResource(true, 'Data Slider Berhasil Disimpan!', $slider);
        } catch (\Exception $e) {
            //return failed with Api Resource
            return new SliderResource(false, 'Data Slider Gagal Disimpan: ' . $e->getMessage(), null);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Slider  $slider
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Slider $slider)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2000',
            'link'  => 'nullable|url'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            //check if image is uploaded
            if ($request->hasFile('image')) {
                //delete old image
                Storage::disk('local')->delete('public/sliders/'.basename($slider->image));
                
                //upload new image
                $image = $request->file('image');
                $image->storeAs('public/sliders', $image->hashName());
                
                //update slider with new image
                $slider->update([
                    'image' => $image->hashName(),
                    'link'  => $request->link ?? $slider->link,
                ]);
            } else {
                //update slider without image
                $slider->update([
                    'link' => $request->link ?? $slider->link,
                ]);
            }
            
            //return success with Api Resource
            return new SliderResource(true, 'Data Slider Berhasil Diupdate!', $slider);
        } catch (\Exception $e) {
            //return failed with Api Resource
            return new SliderResource(false, 'Data Slider Gagal Diupdate: ' . $e->getMessage(), null);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Slider  $slider
     * @return \Illuminate\Http\Response
     */
    public function destroy(Slider $slider)
    {
        try {
            //remove image
            Storage::disk('local')->delete('public/sliders/'.basename($slider->image));
            
            //delete slider
            $slider->delete();
            
            //return success with Api Resource
            return new SliderResource(true, 'Data Slider Berhasil Dihapus!', null);
        } catch (\Exception $e) {
            //return failed with Api Resource
            return new SliderResource(false, 'Data Slider Gagal Dihapus: ' . $e->getMessage(), null);
        }
    }
}
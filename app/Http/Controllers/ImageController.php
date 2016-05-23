<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Helpers\ImageRepository;

class ImageController extends Controller
{
    protected $image;

    public function __construct(ImageRepository $imageRepository)
    {
        $this->image = $imageRepository;
    }

    public function getUpload()
    {
        return view('upload');
    }

    public function postUpload(Request $request)
    {
        $photo = $request->all();
        $response = $this->image->upload($photo);
        return $response;

    }

    public function deleteUpload(Request $request)
    {

        $filename = $request->input('id');
        
        if(!$filename)
        {
            return 0;
        }

        $response = $this->image->delete( $filename );

        return $response;
    }
}

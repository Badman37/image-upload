<?php

namespace App\Helpers;

use App\Image;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;

class ImageRepository
{
     public function upload ($form_data)
    {
        $validator = Validator::make($form_data, Image::$rules, Image::$messages);
        if ($validator->fails()) {

            return response()->json([
                'error' => true,
                'message' => $validator->messages()->first(),
                'code' => 400
            ], 400);
        }


        $path = 'uploads/images/' . date('Y') ."/" . date('m') . "/";

        if (!file_exists($path) && !is_dir($path)) {
            File::makeDirectory($path, $mode = 0777, true, true);
        }
        

        $photo = $form_data['file'];

        $originalName = $photo->getClientOriginalName();
        $originalNameWithoutExt = substr($originalName, 0, strlen($originalName) - 4);
        $filename = $this->sanitize($originalNameWithoutExt);
        $allowed_filename = $this->createUniqueFilename( $filename, $path );

        $filenameExt = $allowed_filename .".jpg";

        $uploadSuccess1 = $this->original( $photo, $filenameExt, $path );

        $uploadSuccess2 = $this->icon( $photo, $filenameExt, $path );

        if( !$uploadSuccess1 || !$uploadSuccess2 ) {

            return response()->json([
                'error' => true,
                'message' => 'Server error while uploading',
                'code' => 500
            ], 500);

        }
        
        $sessionImage = new Image;
        $sessionImage->original_name = $originalName;
        $sessionImage->filename      = $allowed_filename;
        $sessionImage->slug_icon = $path . Config::get('images.icon_size') .'-'. $filenameExt;
        $sessionImage->slug_original = $path . Config::get('images.full_size') .'-'. $filenameExt;

        $sessionImage->save();

        return response()->json([
            'error' => false,
            'code'  => 200
        ], 200);

    }

    public function createUniqueFilename( $filename , $path )
    {
        $full_image_path =  $path . Config::get('images.full_size') . '-' . $filename . '.jpg';

        if ( File::exists( $full_image_path ) )
        {
            // Generate token for image
            $imageToken = substr(sha1(mt_rand()), 0, 6);
            return $filename . '-' . $imageToken;
        }
        return $filename;
    }

    /**
     * Optimize Original Image
     */
    public function original( $photo, $filename, $path )
    {
        $manager = new ImageManager();
        $image = $manager->make( $photo )->encode('jpg')->save( $path . Config::get('images.full_size'). '-' . $filename );
        return $image;
    }

    /**
     * Create Icon From Original
     */
    public function icon( $photo, $filename, $path )
    {
        $manager = new ImageManager();
        $image = $manager->make( $photo )->encode('jpg')->resize(200, null, function($constraint){$constraint->aspectRatio();})->save( $path . Config::get('images.icon_size') . '-'  . $filename );
        return $image;
    }

    /**
     * Delete Image From Session folder, based on original filename
     */
    public function delete( $originalFilename)
    {
        $sessionImage = Image::where('original_name', 'like', $originalFilename)->first();
        if(empty($sessionImage))
        {
            return response()->json([
                'error' => true,
                'code'  => 400
            ], 400);

        }

        $full_path1 = $sessionImage->slug_icon;
        $full_path2 = $sessionImage->slug_original;

        if ( File::exists( $full_path1 ) )
        {
            File::delete( $full_path1 );
        }

        if ( File::exists( $full_path2 ) )
        {
            File::delete( $full_path2 );
        }

        if( !empty($sessionImage))
        {
            $sessionImage->delete();
        }

        return response()->json([
            'error' => false,
            'code'  => 200
        ], 200);
    }

    function sanitize($string, $force_lowercase = true, $anal = false)
    {
        $strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]",
            "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
            "â€”", "â€“", ",", "<", ".", ">", "/", "?");
        $clean = trim(str_replace($strip, "", strip_tags($string)));
        $clean = preg_replace('/\s+/', "-", $clean);
        $clean = ($anal) ? preg_replace("/[^a-zA-Z0-9]/", "", $clean) : $clean ;

        return ($force_lowercase) ?
            (function_exists('mb_strtolower')) ?
                mb_strtolower($clean, 'UTF-8') :
                strtolower($clean) :
            $clean;
    }
}
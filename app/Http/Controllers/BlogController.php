<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = Blog::get();
            foreach ($data as $blog) {
                $blog->products;
                $blog->image = Storage::disk('s3')->temporaryUrl("images/blog/$blog->id/$blog->image_uuid.png", now()->addMinutes(20));
            }
            return response()->json($data);
        } catch (QueryException $e) {
            return response()->json(["Something Went Wrong!", $e->getMessage(), 500]);
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
        $request->validate([
            'title' => ['string'],
            'url' => ['string'],
            'image_uuid' => ['string'],
            'description' => ['string'],
            'products' => ['array'],
        ]);
        $products = [];
        try {
            $blog = new Blog();
            $blog->title = $request->title;
            $blog->description = $request->description;
            $blog->url = $request->url;
            $image = Image::make($request->image)->resize(1024, 1024)->encode('png');
            $uuid = Str::uuid();
            $blog->image_uuid = $uuid;
            foreach ($request->products as  $value) {
                array_push($products, $value['id']);
            }
            $blog->save();
            Storage::disk('s3')->put("images/blog/$blog->id/$uuid.png", $image->stream());
            $blog->products()->sync($products);
            return response()->json(["Create Successfully!", 200]);
        } catch (QueryException $e) {
            return response()->json(["Something Went Wrong!", $e->getMessage(), 500]);
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $request->validate([
            'title' => ['string'],
            'url' => ['string'],
            'image_uuid' => ['string'],
            'description' => ['string'],
            'products' => ['array'],
        ]);
        $products = [];
        try {
            $blog = Blog::find($request->id);
            $blog->title = $request->title;
            $blog->description = $request->description;
            $blog->url = $request->url;
            $image = Image::make($request->image)->resize(1024, 1024)->encode('png');
            $uuidImage = Str::uuid();
            $oldUuidLogo = $blog->image_uuid;
            Storage::disk('s3')->put("images/blog/$blog->id/$uuidImage.png", $image->stream());
            $blog->image_uuid = $uuidImage;
            if ($oldUuidLogo) {
                Storage::disk('s3')->delete("images/blog/$oldUuidLogo.png");
            }
            foreach ($request->products as  $value) {
                array_push($products, $value['id']);
            }
            $blog->products()->sync($products);
            $blog->save();
        } catch (QueryException $e) {
            return response()->json(["Something Went Wrong!", $e->getMessage(), 500]);
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
            $blog =  Blog::find($id);
            if ($blog) {
                $oldUuidImage = $blog->image_uuid;
                if ($oldUuidImage) {
                    Storage::disk('s3')->delete("images/blog/$blog->id/$oldUuidImage.png");
                }
                DB::table('map_blog_product')->where('blog_id', $id)->delete();
                $blog->delete();
                return response()->json(['Data Deleted Successfully!', 200]);
            }
        } catch (QueryException $e) {
            return response()->json(["Something Went Wrong!", $e->getMessage(), 500]);
        }
    }
}

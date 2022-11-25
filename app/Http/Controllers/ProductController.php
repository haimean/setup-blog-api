<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = Product::get();
            foreach ($data as $product) {
                $product->category = Category::find($product->category_id);
                $product->image = Storage::disk('s3')->temporaryUrl("images/product/$product->id/$product->image_uuid.png", now()->addMinutes(20));
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

        ]);
        try {
            $product = new Product();
            $product->title = $request->title;
            $product->category_id = $request->category_id;
            $product->url = $request->url;
            $image = Image::make($request->image)->resize(1024, 1024)->encode('png');
            $uuid = Str::uuid();
            $product->image_uuid = $uuid;
            $product->save();
            Storage::disk('s3')->put("images/product/$product->id/$uuid.png", $image->stream());
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
        try {
            $data = Product::find($id);
            if ($data) {
                $data->blogs;
                foreach ($data->blogs as $blog) {
                    $blog->image = Storage::disk('s3')->temporaryUrl("images/blog/$blog->id/$blog->image_uuid.png", now()->addMinutes(20));
                }
                $data->category;
                $data->image = Storage::disk('s3')->temporaryUrl("images/product/$data->id/$data->image_uuid.png", now()->addMinutes(20));
                return response()->json($data);
            } else {
                return response()->json(['Data Not found!', 404]);
            }
        } catch (QueryException $e) {
            return response()->json(["Something Went Wrong!", $e->getMessage(), 500]);
        }
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

        ]);
        try {
            $product = Product::find($request->id);
            $product->title = $request->title;
            $product->category_id = $request->category_id;
            $product->url = $request->url;
            $image = Image::make($request->image)->resize(1024, 1024)->encode('png');
            $uuidImage = Str::uuid();
            $oldUuidLogo = $product->image_uuid;
            Storage::disk('s3')->put("images/product/$product->id/$uuidImage.png", $image->stream());
            $product->image_uuid = $uuidImage;
            if ($oldUuidLogo) {
                Storage::disk('s3')->delete("images/product/$product->id/$oldUuidLogo.png");
            }
            $product->save();
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
            $product =  Product::find($id);
            if ($product) {
                $oldUuidImage = $product->image_uuid;
                if ($oldUuidImage) {
                    Storage::disk('s3')->delete("images/product/$product->id/$oldUuidImage.png");
                }
                $product->delete();
                return response()->json(['Data Deleted Successfully!', 200]);
            }
        } catch (QueryException $e) {
            return response()->json(["Something Went Wrong!", $e->getMessage(), 500]);
        }
    }

    public function getProduct($id)
    {
        // return response()->json($id);
        try {
            $data = Product::where('category_id', $id)->get();
            foreach ($data as $product) {
                $product->category = Category::find($product->category_id);
                $product->image = Storage::disk('s3')->temporaryUrl("images/product/$product->id/$product->image_uuid.png", now()->addMinutes(20));
            }
            return response()->json($data);
        } catch (QueryException $e) {
            return response()->json(["Something Went Wrong!", $e->getMessage(), 500]);
        }
    }
}

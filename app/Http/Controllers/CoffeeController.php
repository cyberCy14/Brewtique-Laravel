<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
// use App\Models\CoffeeModel;
use App\Models\Coffee;

class CoffeeController extends Controller
{
    public function coffeeCategory($category): JsonResponse
    {

        try {
            $coffeeCategory = DB::table('coffees')
                ->where('category', $category)  
                ->get();

            $coffees = $coffeeCategory->map(function ($coffee) {
                    $coffee->img = asset('images/coffeeOptionsImages/' . $coffee->img);
                    return $coffee;
                });
    
            if ($coffees->isEmpty()) {
                return response()->json([]);
            }
    


            return response()->json($coffees);

        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching coffee items'], 500);
        }
    }


    public function getCoffee($id): JsonResponse
    {

        try {
            $coffee = Coffee::find($id);
            $coffee->img = asset('images/coffeeOptionsImages/' . $coffee->img);

            if (!$coffee) {
                return response()->json(['error' => 'Coffee item not found', 'id' => $id], 404);
            }
        
            return response()->json($coffee, 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching coffee items'], 500);
        }

    }


    

    public function getAllCoffees()
    {
        $coffees = Coffee::all()->map(function ($coffee) {
            $coffee->img = asset('images/coffeeOptionsImages/' . $coffee->img);
            return $coffee;
        });

        return response()->json($coffees);
    }





    // public function createCoffee(Request $request)
    // {
    //     $validatedData = $request->validate([
    //         'title' => 'required|string|max:255',
    //         'imgFile' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
    //         'img' => "required|string|max:255",
    //         'category' => "required|string|max:255",
    //         'description' => 'required|string',
    //         'description2' => 'nullable|string',
    //         'price' => 'required|integer|min:0',
    //         'link' => 'required|string|max:255',
    //     ]);
    
    //     // Handle image upload
    //     if ($request->hasFile('imgFile')) {
    //         $file = $request->file('imgFile');
    //         $fileName = time() . '_' . $file->getClientOriginalName();

    //         $path = public_path('images/coffeeOptionsImages');
            
    //         if (!file_exists($path)) {
    //             mkdir($path, 0777, true);  
    //         }
    //         $file->move($path, $fileName);
    //         $validatedData['imgFile'] = 'images/coffeeOptionsImages/' . $fileName;
    //     }
    
    //     try {
    //         $coffee = Coffee::create($validatedData);
    
    //         return response()->json([
    //             'message' => 'Coffee created successfully!',
    //             'coffee' => $coffee,
    //             'image_url' => asset($validatedData['imgFile']),
    //         ], 201);
    
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => 'Failed to create coffee'], 500);
    //     }
    // }
    public function createCoffee(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'img' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'category' => "required|string|max:255",
            'description' => 'required|string',
            'description2' => 'nullable|string',
            'price' => 'required|integer|min:0',
            'link' => 'required|string|max:255',
        ]);
    
        // Handle image upload
        if ($request->hasFile('img')) {
            $file = $request->file('img');
            $fileName = time() . '_' . $file->getClientOriginalName();

            $path = public_path('images/coffeeOptionsImages');
            
            if (!file_exists($path)) {
                mkdir($path, 0777, true);  
            }
            $file->move($path, $fileName);
            $validatedData['img'] = $fileName;
        }
    
        try {
            $coffee = Coffee::create($validatedData);
    
            return response()->json([
                'message' => 'Coffee created successfully!',
                'coffee' => $coffee,
                'image_url' => asset($validatedData['img']),
            ], 201);
    
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create coffee'], 500);
        }
    }

    public function destroy(Coffee $coffee)
    {
        // Authorization check
        if ($coffee->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $coffee->delete();

        return response()->json(['message' => 'Order deleted successfully'], 200);
    }
    
    
}    



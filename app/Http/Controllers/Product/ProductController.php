<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Companies\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Companies\Company;

class ProductController extends Controller
{

    public function create(): \Inertia\Response
    {
        return Inertia::render('Products/Create', [
            'categories' => [
                'Electronics',
                'Clothing',
                'Food',
                'Furniture',
                'Books',
                'Other'
            ],
            'units' => [
                'Pieces',
                'Kilograms',
                'Liters',
                'Meters',
                'Boxes',
                'Other'
            ]
        ]);
    }

    public function index(): \Inertia\Response
    {
        $products = Product::latest()->get();

        return Inertia::render('Products/Index', [
            'products' => $products
        ]);
    }

    public function getProductsBySupplier(Request $request)
    {
        $company = Company::where('uuid', $request->uuid)->firstOrFail();

        $products = Product::where('company_id', $company->id)
            ->with(['creator'])
            ->latest()
            ->get();

        return response()->json($products);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku_number' => 'required|string|unique:products',
            'category' => 'required|string',
            'unit_of_measure' => 'required|string',
            'description' => 'nullable|string',
            'manufucturer' => 'required|string',
            'primary_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'secondary_images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'created_by' => 'required|integer|exists:users,id',
            'company_id' => 'required|integer|exists:companies,id'
        ]);

        $images = [];

        // Handle primary image
        if ($request->hasFile('primary_image')) {
            $primaryImage = $request->file('primary_image');
            $uniqueRef = hexdec(uniqid());
            $extension = $primaryImage->getClientOriginalExtension();
            $primaryPath = $primaryImage->storeAs('products', $uniqueRef . '.' . $extension, 'public');
            $images[] = [
                'path' => $primaryPath,
                'type' => 'primary'
            ];
        }

        // Handle secondary images
        if ($request->hasFile('secondary_images')) {
            foreach ($request->file('secondary_images') as $image) {
                $uniqueRef = hexdec(uniqid());
                $extension = $image->getClientOriginalExtension();
                $path = $image->storeAs('products', $uniqueRef . '.' . $extension, 'public');
                $images[] = [
                    'path' => $path,
                    'type' => 'secondary'
                ];
            }
        }

        $product = Product::create([
            ...$validated,
            'images' => json_encode($images), // Encode the images array to JSON
            'status' => 'active'
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully');
    }
}

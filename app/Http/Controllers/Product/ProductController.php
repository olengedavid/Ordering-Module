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
        // $products = Product::latest()->get();

        return Inertia::render('Products/Index');
    }

    public function getProductsBySupplier(Request $request)
    {
        $company = Company::where('uuid', $request->uuid)->firstOrFail();
        $search = $request->input('search');

        \Illuminate\Support\Facades\Log::info('Product search parameter:', [
            'search_term' => $search,
            'company_uuid' => $request->uuid
        ]);

        $products = Product::where('company_id', $company->id)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%'])
                        ->orWhereRaw('LOWER(sku_number) LIKE ?', ['%' . strtolower($search) . '%']);
                });
            })
            ->with(['creator'])
            ->latest()
            ->get();

        return response()->json($products);
    }

    public function getPaginatedProductsBySupplier(Request $request)
    {
        $company = Company::where('uuid', $request->uuid)->firstOrFail();
        $search = $request->input('search');

        $products = Product::where('company_id', $company->id)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%'])
                        ->orWhereRaw('LOWER(sku_number) LIKE ?', ['%' . strtolower($search) . '%']);
                });
            })
            ->with(['creator'])
            ->latest()
            ->paginate($request->pageSize);

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
                array_push($images, [
                    'path' => $path,
                    'type' => 'secondary'
                ]);
            }
        }

        $product = Product::create([
            ...$validated,
            'images' => json_encode(array_values($images)), // Encode the images array to JSON
            'status' => 'active'
        ]);

        return redirect()->back()
            ->with('success', 'Product created successfully');
    }

    public function update(Request $request, $uuid)
    {
        $product = Product::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku_number' => 'required|string|unique:products,sku_number,' . $product->id,
            'category' => 'required|string',
            'unit_of_measure' => 'required|string',
            'description' => 'nullable|string',
            'manufucturer' => 'required|string',
            'primary_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'secondary_images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'created_by' => 'required|integer|exists:users,id',
            'company_id' => 'required|integer|exists:companies,id'
        ]);

        $images = $product->images ? json_decode($product->images, true) : [];

        // Handle primary image if new one is uploaded
        if ($request->hasFile('primary_image')) {
            $primaryImage = $request->file('primary_image');
            $uniqueRef = hexdec(uniqid());
            $extension = $primaryImage->getClientOriginalExtension();
            $primaryPath = $primaryImage->storeAs('products', $uniqueRef . '.' . $extension, 'public');

            // Remove old primary image
            $images = array_values(array_filter($images, fn($img) => $img['type'] !== 'primary'));

            // Add new primary image
            array_push($images, [
                'path' => $primaryPath,
                'type' => 'primary'
            ]);
        }

        // Handle secondary images
        if ($request->hasFile('secondary_images')) {
            // Remove old secondary images
            $images = array_filter($images, fn($img) => $img['type'] !== 'secondary');

            foreach ($request->file('secondary_images') as $image) {
                $uniqueRef = hexdec(uniqid());
                $extension = $image->getClientOriginalExtension();
                $path = $image->storeAs('products', $uniqueRef . '.' . $extension, 'public');
                array_push($images, [
                    'path' => $path,
                    'type' => 'secondary'
                ]);
            }
        }

        $product->update([
            ...$validated,
            'images' => json_encode(array_values($images)),
        ]);

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product
        ]);
    }

    public function destroy($uuid)
    {
        try {
            $product = Product::where('uuid', $uuid)->firstOrFail();
            $product->delete();

            return response()->json([
                'message' => 'Inventory deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting inventory',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProductController extends Controller
{
    /**
     * Display the product list page
     */
    public function index()
    {
        $products = Product::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product
     */
    public function create()
    {
        $stages = Product::getStages();
        
        return view('products.create', compact('stages'));
    }

    /**
     * Store a new product
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'batch_no' => 'required|string|max:255',
            'stage' => 'required|string|max:255',
        ]);

        Product::create([
            'name' => $request->name,
            'batch_no' => $request->batch_no,
            'stage' => $request->stage,
            'status' => 'pending'
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Product added successfully!');
    }

    /**
     * Display product details page
     */
    public function show(Product $product)
    {   
        return view('products.show', compact('product'));
    }

    /**
     * Update product details
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'pre_line_clearance' => 'nullable|in:0,1',
            'in_process' => 'nullable|in:0,1',
            'post_line_clearance' => 'nullable|in:0,1',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $product->update([
            'pre_line_clearance' => (bool) $request->input('pre_line_clearance', false),
            'in_process' => (bool) $request->input('in_process', false),
            'post_line_clearance' => (bool) $request->input('post_line_clearance', false),
            'remarks' => $request->remarks,
        ]);

        return redirect()->route('products.show', $product)
            ->with('success', 'Product details updated successfully!');
    }

    /**
     * Submit a product
     */
    public function submit(Product $product)
    {
        // Check if all checkboxes are marked
        if (!$product->isReadyForSubmission()) {
            return redirect()->route('products.show', $product)
                ->with('error', 'Please complete all clearances before submitting.');
        }

        $now = Carbon::now();
        
        $product->update([
            'stage' => 'Completed',
            'status' => 'submitted',
            'submission_date' => $now->toDateString(),
            'submission_time' => $now->toTimeString(),
        ]);        return redirect()->route('products.submitted')
            ->with('success', 'Product submitted successfully!');
    }

    /**
     * Display submitted products page
     */
    public function submitted(Request $request)
    {
        $query = Product::where('status', 'submitted');

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('batch_no', 'like', "%{$search}%")
                  ->orWhere('stage', 'like', "%{$search}%");
            });
        }

        $submittedProducts = $query->orderBy('submission_date', 'desc')
            ->orderBy('submission_time', 'desc')
            ->get();

        return view('products.submitted', compact('submittedProducts'));
    }

    /**
     * Export submitted products to CSV
     */
    public function exportCsv()
    {
        $products = Product::where('status', 'submitted')
            ->orderBy('submission_date', 'desc')
            ->get();

        $filename = 'submitted_products_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');
            
            // CSV Header
            fputcsv($file, [
                'ID',
                'Product Name',
                'Batch No',
                'Stage',
                'Submission Date',
                'Submission Time',
                'Remarks'
            ]);

            // CSV Data
            foreach ($products as $product) {
                fputcsv($file, [
                    $product->id,
                    $product->name,
                    $product->batch_no,
                    $product->stage,
                    $product->submission_date ? $product->submission_date->format('Y-m-d') : '',
                    $product->submission_time ? $product->submission_time->format('H:i:s') : '',
                    $product->remarks ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show the form for editing a product
     */
    public function edit(Product $product)
    {
        $stages = Product::getStages();
        return view('products.edit', compact('product', 'stages'));
    }

    /**
     * Update product basic information
     */
    public function updateBasic(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'batch_no' => 'required|string|max:255',
            'stage' => 'required|string|max:255',
        ]);

        $product->update([
            'name' => $request->name,
            'batch_no' => $request->batch_no,
            'stage' => $request->stage,
        ]);

        return redirect()->route('products.show', $product)
            ->with('success', 'Product information updated successfully!');
    }

    /**
     * Delete a product
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully!');
    }
}

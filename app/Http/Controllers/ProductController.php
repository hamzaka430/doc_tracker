<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ProductController extends Controller
{
    /**
     * Display the product list page - ALL documents (both pending and submitted)
     */
    public function index(Request $request)
    {
        $query = Product::where('user_id', auth()->id());

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('batch_no', 'like', "%{$search}%")
                  ->orWhere('stage', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $products = $query->orderByRaw("FIELD(type, 'Suspension', 'Injection', 'Capsule', 'Tablet')")
            ->orderBy('batch_no', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(25);
            
        // Analytics Data for Chart (Last 7 days submissions)
        $chartData = Product::where('user_id', auth()->id())
            ->where('status', 'submitted')
            ->where('submission_date', '>=', now()->subDays(6)->toDateString())
            ->selectRaw('submission_date as date, count(*) as count')
            ->groupBy('submission_date')
            ->orderBy('submission_date')
            ->get()
            ->keyBy('date');

        $labels = [];
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $labels[] = now()->subDays($i)->format('M d');
            $data[] = isset($chartData[$date]) ? $chartData[$date]->count : 0;
        }
        
        $chartDataJson = json_encode(['labels' => $labels, 'data' => $data]);
        
        return view('products.index', compact('products', 'chartDataJson'));
    }

    /**
     * Show the form for creating a new product
     */
    public function create()
    {
        $defaultNames = [
            'Caricef 100mg Suspension',
            'Caricef DS Suspension',
            'Caricef 400mg Capsule',
            'Caricef 200mg Tablet',
            '2Sum 2g Injection',
            '2Sum 1g Injection',
            'Oxidil 500mg IV Injection',
            'Oxidil 500mg IM Injection',
            'Oxidil 250mg IV Injection',
            'Oxidil 250mg IM Injection',
            'Oxidil 1g IV Injection',
            'Slate 250mg Capsule',
            'Slate 500mg Capsule'
        ];

        $dbNames = Product::distinct()->pluck('name')->toArray();
        $names = collect(array_merge($defaultNames, $dbNames))->unique()->sort()->values();

        $batchNos = Product::distinct()->pluck('batch_no')->filter()->unique()->sort()->values();
        
        $defaultStages = Product::getStages();
        $dbStages = Product::distinct()->pluck('stage')->toArray();
        $stages = collect(array_merge($defaultStages, $dbStages))->unique()->sort()->values();

        $types = Product::getTypes();
        
        $userPrefs = \App\Models\UserPreference::where('user_id', auth()->id())
            ->get()
            ->keyBy('key')
            ->map(function($item) {
                return $item->value;
            });
        
        return view('products.create', compact('names', 'batchNos', 'stages', 'types', 'userPrefs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|array|min:1',
            'name.*' => 'required|string|max:255',
            'batch_no' => 'required|array|min:1',
            'batch_no.*' => 'required|string|max:255',
            'stage' => 'required|array|min:1',
            'stage.*' => 'required|string|max:255',
            'type' => 'required|array|min:1',
            'type.*' => 'required|in:Injection,Suspension,Tablet,Capsule',
        ]);

        $userId = auth()->id();
        $names = $request->name;
        $batchNos = $request->batch_no;
        $stages = $request->stage;
        $types = $request->type;

        for ($i = 0; $i < count($names); $i++) {
            Product::create([
                'user_id' => $userId,
                'name' => $names[$i],
                'batch_no' => $batchNos[$i],
                'stage' => $stages[$i],
                'type' => $types[$i],
                'status' => 'pending'
            ]);
        }

        return redirect()->route('products.index')
            ->with('success', count($names) . ' Document(s) added successfully!');
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
        if (!$product->isEditable()) {
            return redirect()->route('products.show', $product)->with('error', 'This document is no longer editable.');
        }

        $request->validate([
            'line_clearance' => 'nullable|in:0,1',
            'review' => 'nullable|in:0,1',
            'confirmation' => 'nullable|in:0,1',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $product->update([
            'line_clearance' => (bool) $request->input('line_clearance', false),
            'review' => (bool) $request->input('review', false),
            'confirmation' => (bool) $request->input('confirmation', false),
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
            'submission_time' => $now->format('H:i:s'),
        ]);
        
        return redirect()->route('products.pending')
            ->with('success', 'Product submitted successfully!');
    }

    /**
     * Submit multiple products in bulk
     */
    public function bulkSubmit(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id'
        ]);

        $products = Product::where('user_id', auth()->id())
            ->whereIn('id', $request->product_ids)
            ->get();

        $submittedCount = 0;
        $skippedCount = 0;
        $now = \Carbon\Carbon::now();

        foreach ($products as $product) {
            if ($product->isSubmitted()) {
                $skippedCount++;
                continue;
            }

            $product->update([
                'stage' => 'Completed',
                'line_clearance' => true,
                'review' => true,
                'confirmation' => true,
                'status' => 'submitted',
                'submission_date' => $now->toDateString(),
                'submission_time' => $now->format('H:i:s'),
            ]);

            $submittedCount++;
        }

        if ($submittedCount > 0 && $skippedCount == 0) {
            $message = "Successfully submitted {$submittedCount} documents!";
            $type = 'success';
        } elseif ($submittedCount > 0 && $skippedCount > 0) {
            $message = "Successfully submitted {$submittedCount} documents. Skipped {$skippedCount} documents that were already submitted.";
            $type = 'warning';
        } elseif ($submittedCount == 0 && $skippedCount > 0) {
            $message = "No documents submitted. All {$skippedCount} selected documents were already submitted.";
            $type = 'error';
        } else {
            $message = "No documents selected for submission.";
            $type = 'error';
        }

        return redirect()->route('products.pending')
            ->with($type, $message);
    }

    /**
     * Display submitted products page
     */
    public function submitted(Request $request)
    {
        $query = Product::where('user_id', auth()->id())->where('status', 'submitted');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('batch_no', 'like', "%{$search}%")
                  ->orWhere('stage', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('submission_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('submission_date', '<=', $request->date_to);
        }

        $submittedProducts = $query->orderBy('submission_date', 'desc')
            ->orderBy('submission_time', 'desc')
            ->orderByRaw("FIELD(type, 'Suspension', 'Injection', 'Capsule', 'Tablet')")
            ->orderBy('batch_no', 'asc')
            ->paginate(25);

        return view('products.submitted', compact('submittedProducts'));
    }

    /**
     * Display today's documents (daily list) - All remaining/pending documents
     */
    public function daily(Request $request)
    {
        $query = Product::where('user_id', auth()->id())->where('status', 'pending');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('batch_no', 'like', "%{$search}%")
                  ->orWhere('stage', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $products = $query->orderByRaw("FIELD(type, 'Suspension', 'Injection', 'Capsule', 'Tablet')")
            ->orderBy('batch_no', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return view('products.daily', compact('products'));
    }

    /**
     * Display pending documents page
     */
    public function pending(Request $request)
    {
        $query = Product::where('user_id', auth()->id())->where('status', 'pending');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('batch_no', 'like', "%{$search}%")
                  ->orWhere('stage', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $products = $query->orderByRaw("FIELD(type, 'Suspension', 'Injection', 'Capsule', 'Tablet')")
            ->orderBy('batch_no', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return view('products.pending', compact('products'));
    }

    /**
     * Export remaining documents to PDF with grouped format
     */
    public function exportDailyPdf(Request $request)
    {
        $products = Product::where('user_id', auth()->id())->where('status', 'pending')
            ->orderByRaw("FIELD(type, 'Suspension', 'Injection', 'Capsule', 'Tablet')")
            ->orderBy('name', 'asc')
            ->orderBy('batch_no', 'asc')
            ->get();

        // Group products by name
        $groupedProducts = $products->groupBy('name')->map(function($items) {
            return [
                'name' => $items->first()->name,
                'batches' => $items->map(function($item) {
                    return [
                        'batch_no' => $item->batch_no,
                        'stage' => $item->stage
                    ];
                })->toArray()
            ];
        })->values();

        // Get layout preference from request (default: single)
        $layout = $request->get('layout', 'single');
        
        // Choose the appropriate view based on layout
        $viewName = $layout === 'double' ? 'products.daily-pdf-double' : 'products.daily-pdf';
        
        $pdf = Pdf::loadView($viewName, [
            'groupedProducts' => $groupedProducts,
            'totalDocuments' => $products->count(),
            'generatedDate' => Carbon::now()->format('d/m/Y h:i A')
        ]);

        $filename = 'remaining_documents_' . date('Y-m-d_H-i-s') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Export submitted products to CSV
     */
    public function exportCsv()
    {
        $products = Product::where('user_id', auth()->id())->where('status', 'submitted')
            ->orderByRaw("FIELD(type, 'Suspension', 'Injection', 'Capsule', 'Tablet')")
            ->orderBy('batch_no', 'asc')
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
                    $product->submission_date ? (\is_string($product->submission_date) ? date('Y-m-d', strtotime($product->submission_date)) : $product->submission_date->format('Y-m-d')) : '',
                    $product->submission_time ? (\is_string($product->submission_time) ? $product->submission_time : $product->submission_time->format('H:i:s')) : '',
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
        if (!$product->isEditable()) {
            return redirect()->route('products.show', $product)->with('error', 'This document is no longer editable.');
        }

        $stages = Product::getStages();
        $types = Product::getTypes();
        return view('products.edit', compact('product', 'stages', 'types'));
    }

    /**
     * Update product basic information
     */
    public function updateBasic(Request $request, Product $product)
    {
        if (!$product->isEditable()) {
            return redirect()->route('products.show', $product)->with('error', 'This document is no longer editable.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'batch_no' => 'required|string|max:255',
            'stage' => 'required|string|max:255',
            'type' => 'required|in:Injection,Suspension,Tablet,Capsule',
        ]);

        $product->update([
            'name' => $request->name,
            'batch_no' => $request->batch_no,
            'stage' => $request->stage,
            'type' => $request->type,
        ]);

        return redirect()->route('products.show', $product)
            ->with('success', 'Product information updated successfully!');
    }

    /**
     * Delete a product
     */
    public function destroy(Product $product)
    {
        if (!$product->isEditable()) {
            return redirect()->route('products.show', $product)->with('error', 'This document cannot be deleted because it is no longer editable.');
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully!');
    }

    /**
     * Update submission date of a product
     */
    public function updateSubmissionDate(Request $request, Product $product)
    {
        $request->validate([
            'submission_date' => 'required|date',
            'submission_time' => 'nullable|date_format:H:i',
        ]);

        // If time is provided, combine date and time; otherwise use only date
        $submissionDate = $request->submission_date;
        $submissionTime = $request->submission_time ?? $product->submission_time;

        $product->update([
            'submission_date' => $submissionDate,
            'submission_time' => $submissionTime,
        ]);

        return redirect()->route('products.submitted')
            ->with('success', 'Submission date updated successfully!');
    }

    public function trash(Request $request)
    {
        $query = Product::onlyTrashed()->where('user_id', auth()->id());

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('batch_no', 'like', "%{$search}%")
                  ->orWhere('stage', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('deleted_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('deleted_at', '<=', $request->date_to);
        }

        $products = $query->orderBy('deleted_at', 'desc')->paginate(25);
        
        return view('products.trash', compact('products'));
    }

    public function restore($id)
    {
        $product = Product::onlyTrashed()->where('user_id', auth()->id())->findOrFail($id);
        $product->restore();

        return back()->with('success', 'Document restored successfully from Recycle Bin.');
    }
}

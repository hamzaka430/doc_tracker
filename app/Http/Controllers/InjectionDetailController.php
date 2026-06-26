<?php

namespace App\Http\Controllers;

use App\Models\InjectionDetail;
use Illuminate\Http\Request;

class InjectionDetailController extends Controller
{
    /**
     * Display a listing of the Vials Data.
     */
    public function index(Request $request)
    {
        $query = InjectionDetail::whereIn('user_id', auth()->user()->getVisibleUserIds())
                    ->whereNotNull('batch_size')
                    ->where(function ($q) {
                        $q->whereDoesntHave('product', function ($q2) {
                            $q2->where('status', 'pending');
                        });
                    });

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                  ->orWhere('batch_no', 'like', "%{$search}%");
            });
        }

        $vialsData = $query->orderBy('created_at', 'desc')->paginate(25);

        return view('injection_details.index', compact('vialsData'));
    }

    /**
     * Display a listing of daily (pending) Vials Data.
     */
    public function daily(Request $request)
    {
        $query = InjectionDetail::whereIn('user_id', auth()->user()->getVisibleUserIds())
                    ->where(function ($q) {
                        $q->whereNull('batch_size')
                          ->orWhereHas('product', function ($q2) {
                              $q2->where('status', 'pending');
                          });
                    });

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                  ->orWhere('batch_no', 'like', "%{$search}%");
            });
        }

        $vialsData = $query->orderBy('created_at', 'desc')->paginate(25);

        return view('injection_details.daily', compact('vialsData'));
    }

    /**
     * Show the form for creating a new custom Vials Data entry.
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

        $dbNames = \App\Models\Product::distinct()->pluck('name')->toArray();
        $names = collect(array_merge($defaultNames, $dbNames))->unique()->sort()->values();

        $batchNos = \App\Models\Product::distinct()->pluck('batch_no')->filter()->unique()->sort()->values();

        $userPrefs = auth()->user()->preferences ?? [];

        return view('injection_details.create', compact('names', 'batchNos', 'userPrefs'));
    }

    /**
     * Store newly created Vials Data entries in storage (supports bulk).
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_name'   => 'required|array|min:1',
            'product_name.*' => 'required|string|max:255',
            'batch_no'       => 'required|array|min:1',
            'batch_no.*'     => 'required|string|max:255',
            'batch_size.*'   => 'nullable|string|max:255',
            'optical_qty.*'  => 'nullable|string|max:255',
            'pkg_qty.*'      => 'nullable|string|max:255',
            'rejection.*'    => 'nullable|string|max:255',
            'error.*'        => 'nullable|string',
        ]);

        $userId       = auth()->id();
        $names        = $request->product_name;
        $batchNos     = $request->batch_no;
        $batchSizes   = $request->batch_size ?? [];
        $opticalQtys  = $request->optical_qty ?? [];
        $pkgQtys      = $request->pkg_qty ?? [];
        $rejections   = $request->rejection ?? [];
        $errors       = $request->error ?? [];

        for ($i = 0; $i < count($names); $i++) {
            InjectionDetail::create([
                'user_id'      => $userId,
                'product_name' => $names[$i],
                'batch_no'     => $batchNos[$i],
                'batch_size'   => $batchSizes[$i] ?? '0',
                'optical_qty'  => $opticalQtys[$i] ?? '0',
                'pkg_qty'      => $pkgQtys[$i] ?? '0',
                'rejection'    => $rejections[$i] ?? '0',
                'error'        => $errors[$i] ?? '0',
            ]);
        }

        return redirect()->route('injection_details.daily')->with('success', count($names) . ' Vials Data entry(ies) saved successfully.');
    }

    /**
     * Show the form for editing the specified Vials Data.
     */
    public function edit($id)
    {
        $injectionDetail = InjectionDetail::whereIn('user_id', auth()->user()->getVisibleUserIds())->findOrFail($id);

        if (!auth()->user()->canModifyData($injectionDetail->user_id)) {
            abort(403, 'Unauthorized action.');
        }
        return view('injection_details.edit', compact('injectionDetail'));
    }

    /**
     * Update the specified Vials Data in storage.
     */
    public function update(Request $request, $id)
    {
        $injectionDetail = InjectionDetail::whereIn('user_id', auth()->user()->getVisibleUserIds())->findOrFail($id);

        if (!auth()->user()->canModifyData($injectionDetail->user_id)) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'product_name' => 'required|string|max:255',
            'batch_no' => 'required|string|max:255',
            'batch_size' => 'nullable|string|max:255',
            'optical_qty' => 'nullable|string|max:255',
            'pkg_qty' => 'nullable|string|max:255',
            'rejection' => 'nullable|string|max:255',
            'error' => 'nullable|string',
        ]);

        $data = $request->only(['product_name', 'batch_no']);
        $data['batch_size'] = $request->batch_size ?? '0';
        $data['optical_qty'] = $request->optical_qty ?? '0';
        $data['pkg_qty'] = $request->pkg_qty ?? '0';
        $data['rejection'] = $request->rejection ?? '0';
        $data['error'] = $request->error ?? '0';

        $injectionDetail->update($data);

        return redirect()->route('injection_details.daily')->with('success', 'Vials Data updated successfully.');
    }

    /**
     * Remove the specified Vials Data from storage.
     */
    public function destroy($id)
    {
        $injectionDetail = InjectionDetail::whereIn('user_id', auth()->user()->getVisibleUserIds())->findOrFail($id);

        if (!auth()->user()->canModifyData($injectionDetail->user_id)) {
            abort(403, 'Unauthorized action.');
        }
        $injectionDetail->delete();

        return redirect()->route('injection_details.daily')->with('success', 'Vials Data deleted successfully.');
    }

    /**
     * Export all Vials Data to CSV.
     */
    public function exportCsv()
    {
        $vialsData = InjectionDetail::whereIn('user_id', auth()->user()->getVisibleUserIds())
            ->orderBy('product_name', 'asc')
            ->orderBy('batch_no', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'vials_data_export_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($vialsData) {
            $file = fopen('php://output', 'w');
            
            // CSV Header
            fputcsv($file, [
                'S.No',
                'Product Name',
                'Batch No',
                'Batch Size',
                'Optical Qty',
                'Pkg Qty',
                'Rejection',
                'Error',
                'Created At'
            ]);

            // CSV Data
            foreach ($vialsData as $data) {
                fputcsv($file, [
                    $data->id,
                    $data->product_name,
                    $data->batch_no,
                    $data->batch_size ?? '',
                    $data->optical_qty ?? '',
                    $data->pkg_qty ?? '',
                    $data->rejection ?? '',
                    $data->error ?? '',
                    $data->created_at ? $data->created_at->format('Y-m-d H:i:s') : ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

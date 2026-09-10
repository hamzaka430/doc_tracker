<?php

namespace App\Http\Controllers;

use App\Models\SapError;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SapErrorController extends Controller
{
    public function __construct()
    {
        // Feature permission checks removed, data is controlled by visibility rules
    }

    /**
     * Display all SAP errors for the authenticated user.
     */
    public function index(): View
    {
        $sapErrors = SapError::whereIn('user_id', auth()->user()->getVisibleUserIds())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('sap_errors.index', compact('sapErrors'));
    }

    /**
     * Show the form for creating a new SAP error.
     */
    public function create(): View
    {
        return view('sap_errors.create');
    }

    /**
     * Store a newly created SAP error in S3 and DB.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'sap_tcode'   => 'nullable|string|max:100',
            'description' => 'nullable|string|max:5000',
            'image'       => 'nullable|image|mimes:png,jpg,jpeg,webp|max:5120',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            try {
                $disk = config('filesystems.default', 'public');
                $imagePath = $request->file('image')->store('errors', $disk);
            } catch (\Exception $e) {
                return back()->withInput()->withErrors(['image' => 'Image upload failed: ' . $e->getMessage()]);
            }
        }

        SapError::create([
            'user_id'     => auth()->id(),
            'title'       => $request->title,
            'sap_tcode'   => $request->sap_tcode,
            'description' => $request->description,
            'image_path'  => $imagePath,
        ]);

        return redirect()->route('sap-errors.index')
            ->with('success', 'Error saved successfully!');
    }

    /**
     * Display a specific SAP error.
     */
    public function show(SapError $sapError): View
    {
        $this->authorizeView($sapError);
        return view('sap_errors.show', compact('sapError'));
    }

    /**
     * Show the form for editing a SAP error.
     */
    public function edit(SapError $sapError): View
    {
        $this->authorizeModify($sapError);
        return view('sap_errors.edit', compact('sapError'));
    }

    /**
     * Update a SAP error record.
     */
    public function update(Request $request, SapError $sapError): RedirectResponse
    {
        $this->authorizeModify($sapError);

        $request->validate([
            'title'       => 'required|string|max:255',
            'sap_tcode'   => 'nullable|string|max:100',
            'description' => 'nullable|string|max:5000',
            'image'       => 'nullable|image|mimes:png,jpg,jpeg,webp|max:5120',
        ]);

        $imagePath = $sapError->image_path;
        $disk = config('filesystems.default', 'public');

        if ($request->hasFile('image')) {
            try {
                // Delete old image
                if ($sapError->image_path) {
                    Storage::disk($disk)->delete($sapError->image_path);
                }
                $imagePath = $request->file('image')->store('errors', $disk);
            } catch (\Exception $e) {
                return back()->withInput()->withErrors(['image' => 'Image upload failed: ' . $e->getMessage()]);
            }
        }

        // Handle image removal
        if ($request->has('remove_image') && $request->remove_image) {
            if ($sapError->image_path) {
                Storage::disk($disk)->delete($sapError->image_path);
            }
            $imagePath = null;
        }

        $sapError->update([
            'title'       => $request->title,
            'sap_tcode'   => $request->sap_tcode,
            'description' => $request->description,
            'image_path'  => $imagePath,
        ]);

        return redirect()->route('sap-errors.show', $sapError)
            ->with('success', 'Error updated successfully!');
    }

    /**
     * Delete a SAP error and its image.
     */
    public function destroy(SapError $sapError): RedirectResponse
    {
        $this->authorizeModify($sapError);

        if ($sapError->image_path) {
            $disk = config('filesystems.default', 'public');
            Storage::disk($disk)->delete($sapError->image_path);
        }

        $sapError->delete();

        return redirect()->route('sap-errors.index')
            ->with('success', 'Error deleted successfully!');
    }

    /**
     * Ensure the authenticated user can view this record.
     */
    private function authorizeView(SapError $sapError): void
    {
        if (!in_array($sapError->user_id, auth()->user()->getVisibleUserIds())) {
            abort(403);
        }
    }

    /**
     * Ensure the authenticated user can modify this record.
     */
    private function authorizeModify(SapError $sapError): void
    {
        if (!auth()->user()->canModifyData($sapError->user_id)) {
            abort(403, 'Unauthorized action.');
        }
    }
}

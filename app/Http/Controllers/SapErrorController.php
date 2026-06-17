<?php

namespace App\Http\Controllers;

use App\Models\SapError;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SapErrorController extends Controller
{
    /**
     * Display all SAP errors for the authenticated user.
     */
    public function index(): View
    {
        $sapErrors = SapError::where('user_id', auth()->id())
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
                $imagePath = $request->file('image')->store('errors', 's3');
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
        $this->authorizeError($sapError);
        return view('sap_errors.show', compact('sapError'));
    }

    /**
     * Show the form for editing a SAP error.
     */
    public function edit(SapError $sapError): View
    {
        $this->authorizeError($sapError);
        return view('sap_errors.edit', compact('sapError'));
    }

    /**
     * Update a SAP error record.
     */
    public function update(Request $request, SapError $sapError): RedirectResponse
    {
        $this->authorizeError($sapError);

        $request->validate([
            'title'       => 'required|string|max:255',
            'sap_tcode'   => 'nullable|string|max:100',
            'description' => 'nullable|string|max:5000',
            'image'       => 'nullable|image|mimes:png,jpg,jpeg,webp|max:5120',
        ]);

        $imagePath = $sapError->image_path;

        if ($request->hasFile('image')) {
            try {
                // Delete old image from S3
                if ($sapError->image_path) {
                    Storage::disk('s3')->delete($sapError->image_path);
                }
                $imagePath = $request->file('image')->store('errors', 's3');
            } catch (\Exception $e) {
                return back()->withInput()->withErrors(['image' => 'Image upload failed: ' . $e->getMessage()]);
            }
        }

        // Handle image removal
        if ($request->has('remove_image') && $request->remove_image) {
            if ($sapError->image_path) {
                Storage::disk('s3')->delete($sapError->image_path);
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
     * Delete a SAP error and its S3 image.
     */
    public function destroy(SapError $sapError): RedirectResponse
    {
        $this->authorizeError($sapError);

        if ($sapError->image_path) {
            Storage::disk('s3')->delete($sapError->image_path);
        }

        $sapError->delete();

        return redirect()->route('sap-errors.index')
            ->with('success', 'Error deleted successfully!');
    }

    /**
     * Ensure the authenticated user owns this record.
     */
    private function authorizeError(SapError $sapError): void
    {
        if ($sapError->user_id !== auth()->id()) {
            abort(403);
        }
    }
}

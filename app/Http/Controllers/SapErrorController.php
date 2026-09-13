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
     * Get the active upload storage disk.
     */
    protected function getStorageDisk(): string
    {
        $default = config('filesystems.default', 'public');
        return $default === 'local' ? 'public' : $default;
    }

    /**
     * Safely delete a stored image if present.
     */
    protected function deleteImage(?string $path): void
    {
        if ($path) {
            try {
                Storage::disk($this->getStorageDisk())->delete($path);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Failed to delete SAP error image {$path}: " . $e->getMessage());
            }
        }
    }

    /**
     * Store a newly created SAP error in S3/public storage and DB.
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
                $disk = $this->getStorageDisk();
                $imagePath = $request->file('image')->store('errors', $disk);
                if (!$imagePath) {
                    throw new \Exception("The storage driver was unable to save the file.");
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('SAP Error screenshot upload failed: ' . $e->getMessage(), ['exception' => $e]);
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

        if ($request->hasFile('image')) {
            try {
                $disk = $this->getStorageDisk();
                $newPath = $request->file('image')->store('errors', $disk);
                if (!$newPath) {
                    throw new \Exception("The storage driver was unable to save the file.");
                }
                $this->deleteImage($sapError->image_path);
                $imagePath = $newPath;
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('SAP Error screenshot update upload failed: ' . $e->getMessage(), ['exception' => $e]);
                return back()->withInput()->withErrors(['image' => 'Image upload failed: ' . $e->getMessage()]);
            }
        }

        // Handle image removal
        if ($request->has('remove_image') && $request->remove_image) {
            $this->deleteImage($sapError->image_path);
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

        $this->deleteImage($sapError->image_path);

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

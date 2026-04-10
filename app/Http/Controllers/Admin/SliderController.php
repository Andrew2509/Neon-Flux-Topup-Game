<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SliderController extends Controller
{
    public function index()
    {
        $sliders = Slider::latest()->get();
        return view('admin.sliders', compact('sliders'));
    }

    public function create()
    {
        return view('admin.sliders.create');
    }

    private function processImage($file, $maxWidth = 1200, $maxHeight = 600)
    {
        $imageInfo = getimagesize($file->getRealPath());
        if (!$imageInfo) return null;

        $mime = $imageInfo['mime'];
        $width = $imageInfo[0];
        $height = $imageInfo[1];

        // Create image from file
        switch ($mime) {
            case 'image/jpeg': $src = imagecreatefromjpeg($file->getRealPath()); break;
            case 'image/png':  $src = imagecreatefrompng($file->getRealPath());  break;
            case 'image/webp': $src = imagecreatefromwebp($file->getRealPath()); break;
            default: return null;
        }

        if (!$src) return null;

        // Calculate new dimensions (aspect-aware)
        $ratio = min($maxWidth / $width, $maxHeight / $height);
        if ($ratio < 1) {
            $newWidth = round($width * $ratio);
            $newHeight = round($height * $ratio);
            
            $dst = imagecreatetruecolor($newWidth, $newHeight);
            
            if ($mime == 'image/png' || $mime == 'image/webp') {
                imagealphablending($dst, false);
                imagesavealpha($dst, true);
            }

            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            $src = $dst;
        }

        // Output to buffer
        ob_start();
        switch ($mime) {
            case 'image/jpeg': imagejpeg($src, null, 80); break;
            case 'image/png':  imagepng($src, null, 6); break;
            case 'image/webp': imagewebp($src, null, 80); break;
        }
        $data = ob_get_clean();
        imagedestroy($src);

        return 'data:' . $mime . ';base64,' . base64_encode($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'tags' => 'nullable|string|max:255',
            'wa_link' => 'nullable|string|max:255',
            'ig_link' => 'nullable|string|max:255',
            'fb_link' => 'nullable|string|max:255',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'image_path' => 'required_without:image_file|nullable|url',
            'link' => 'nullable|url',
            'status' => 'required|string|in:Aktif,Nonaktif',
        ]);

        $imagePath = $request->image_path;

        if ($request->hasFile('image_file')) {
            try {
                $base64 = $this->processImage($request->file('image_file'));
                if ($base64) {
                    $imagePath = $base64;
                }
            } catch (\Exception $e) {
                Log::error('Slider Upload Error: ' . $e->getMessage());
            }
        }

        Slider::create([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'description' => $request->description,
            'tags' => $request->tags,
            'wa_link' => $request->wa_link,
            'ig_link' => $request->ig_link,
            'fb_link' => $request->fb_link,
            'image_path' => $imagePath,
            'link' => $request->link,
            'status' => $request->status,
            'clicks' => 0,
        ]);

        return redirect()->route('admin.sliders')->with('success', 'Slider/Banner berhasil ditambahkan.');
    }

    public function edit(Slider $slider)
    {
        return view('admin.sliders.edit', compact('slider'));
    }

    public function update(Request $request, Slider $slider)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'tags' => 'nullable|string|max:255',
            'wa_link' => 'nullable|string|max:255',
            'ig_link' => 'nullable|string|max:255',
            'fb_link' => 'nullable|string|max:255',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'image_path' => 'required_without:image_file|nullable', // No URL rule here to allow existing base64
            'link' => 'nullable|url',
            'status' => 'required|string|in:Aktif,Nonaktif',
        ]);

        $imagePath = $request->image_path;

        if ($request->hasFile('image_file')) {
            try {
                $base64 = $this->processImage($request->file('image_file'));
                if ($base64) {
                    $imagePath = $base64;
                }
            } catch (\Exception $e) {
                Log::error('Slider Update Upload Error: ' . $e->getMessage());
            }
        }

        $slider->update([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'description' => $request->description,
            'tags' => $request->tags,
            'wa_link' => $request->wa_link,
            'ig_link' => $request->ig_link,
            'fb_link' => $request->fb_link,
            'image_path' => $imagePath,
            'link' => $request->link,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.sliders')->with('success', 'Slider/Banner berhasil diperbarui.');
    }

    public function destroy(Slider $slider)
    {
        $slider->delete();
        return redirect()->route('admin.sliders')->with('success', 'Slider/Banner berhasil dihapus.');
    }
}

<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;
use Exception;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    protected ?Cloudinary $cloudinary = null;

    public function __construct()
    {
        try {
            $cloudinaryUrl = config('cloudinary.cloud_url');
            if ($cloudinaryUrl && !str_contains($cloudinaryUrl, 'dummy_key') && !str_contains($cloudinaryUrl, 'your_api_key')) {
                $this->cloudinary = new Cloudinary($cloudinaryUrl);
            }
        } catch (Exception $e) {
            Log::error('Cloudinary initialization error: ' . $e->getMessage());
        }
    }

    /**
     * Upload an image file to Cloudinary.
     */
    public function upload(UploadedFile $file, string $folder = 'products'): array
    {
        if (!$this->cloudinary) {
            // Fallback for dummy mode
            return [
                'secure_url' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=400&fit=crop',
                'public_id' => 'dummy_cloudinary_id_' . uniqid(),
            ];
        }

        try {
            $result = $this->cloudinary->uploadApi()->upload(
                $file->getRealPath(),
                [
                    'folder' => $folder,
                ]
            );
            return [
                'secure_url' => $result['secure_url'],
                'public_id' => $result['public_id'],
            ];
        } catch (Exception $e) {
            Log::error('Cloudinary upload error: ' . $e->getMessage());
            // Return placeholder on failure
            return [
                'secure_url' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=400&fit=crop',
                'public_id' => 'dummy_cloudinary_id_' . uniqid(),
            ];
        }
    }

    /**
     * Delete an image from Cloudinary.
     */
    public function delete(string $publicId): bool
    {
        if (!$this->cloudinary || str_contains($publicId, 'dummy')) {
            return true;
        }

        try {
            $result = $this->cloudinary->uploadApi()->destroy($publicId);
            return isset($result['result']) && $result['result'] === 'ok';
        } catch (Exception $e) {
            Log::error('Cloudinary delete error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get image URL.
     */
    public function getUrl(string $publicId, array $options = []): string
    {
        if (!$this->cloudinary || str_contains($publicId, 'dummy')) {
            return 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=400&fit=crop';
        }

        try {
            return (string) $this->cloudinary->image($publicId)->toUrl();
        } catch (Exception $e) {
            return 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=400&fit=crop';
        }
    }
}

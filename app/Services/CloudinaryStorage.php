<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;

class CloudinaryStorage
{
    private ?Cloudinary $cloudinary = null;

    private function client(): Cloudinary
    {
        if ($this->cloudinary !== null) {
            return $this->cloudinary;
        }

        $url = (string) config('services.cloudinary.url');
        $cloudName = (string) config('services.cloudinary.cloud_name');
        $apiKey = (string) config('services.cloudinary.api_key');
        $apiSecret = (string) config('services.cloudinary.api_secret');

        if ($url === '' && ($cloudName === '' || $apiKey === '' || $apiSecret === '')) {
            throw new RuntimeException('Chưa cấu hình Cloudinary. Hãy điền CLOUDINARY_URL hoặc CLOUDINARY_CLOUD_NAME, CLOUDINARY_API_KEY và CLOUDINARY_API_SECRET trong file .env.');
        }

        $this->cloudinary = $url !== ''
            ? new Cloudinary($url)
            : new Cloudinary([
                'cloud' => [
                    'cloud_name' => $cloudName,
                    'api_key' => $apiKey,
                    'api_secret' => $apiSecret,
                ],
                'url' => ['secure' => true],
            ]);

        return $this->cloudinary;
    }

    public function upload(UploadedFile $file, string $folder, ?string $publicId = null): array
    {
        $configuredFolder = trim((string) config('services.cloudinary.folder'), '/');
        $folder = trim($configuredFolder.'/'.$folder, '/');

        $options = [
            'folder' => $folder,
            'resource_type' => 'image',
            'overwrite' => true,
        ];

        if ($publicId !== null && $publicId !== '') {
            $options['public_id'] = $publicId;
        }

        $result = $this->client()->uploadApi()->upload($file->getRealPath(), $options);

        return [
            'secure_url' => (string) $result['secure_url'],
            'public_id' => (string) $result['public_id'],
            'asset_id' => (string) ($result['asset_id'] ?? ''),
        ];
    }

    public function delete(?string $value): void
    {
        if (! $value) {
            return;
        }

        $publicId = null;

        if (str_starts_with($value, 'cloudinary://')) {
            $publicId = substr($value, strlen('cloudinary://'));
        } elseif (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            $path = (string) parse_url($value, PHP_URL_PATH);
            $marker = '/upload/';
            $markerPosition = strpos($path, $marker);

            if ($markerPosition !== false) {
                $publicId = substr($path, $markerPosition + strlen($marker));
                $publicId = preg_replace('#^v\d+/#', '', $publicId);
                $publicId = preg_replace('#\.[^.]+$#', '', $publicId ?? '');
            }
        }

        if (! $publicId) {
            return;
        }
        $this->client()->uploadApi()->destroy($publicId, [
            'invalidate' => true,
        ]);
    }

    public function publicIdToUrlValue(string $publicId): string
    {
        return 'cloudinary://'.$publicId;
    }
}

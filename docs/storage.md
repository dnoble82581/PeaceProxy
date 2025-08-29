# Storage & Uploads

## Strategy
- S3 **public** bucket: avatars, UI images
- S3 **private** bucket: documents, subject images

## Processing
- Use **Spatie Image** (repository pattern) for resizing and optimization

## Uploads
- Livewire `WithFileUploads`
- Virus scanning (optional)
- Signed URLs for private downloads

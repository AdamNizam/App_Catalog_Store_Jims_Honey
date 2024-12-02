<x-filament::page>
    <h2 class="text-xl font-bold">Product Details</h2>
    <div class="mt-4">
        <p><strong>Name:</strong> {{ $record->name }}</p>
        <p><strong>Price:</strong> Rp {{ number_format($record->price, 0, ',', '.') }}</p>
        <p><strong>Stock:</strong> {{ $record->stock }}</p>
        <p><strong>Category:</strong> {{ $record->category->name ?? 'N/A' }}</p>
        <p><strong>Brand:</strong> {{ $record->brand->name ?? 'N/A' }}</p>
        <p><strong>Description:</strong> {{ $record->about }}</p>
    </div>
    @if($record->thumbnail)
        <div class="mt-4">
            <h3>Thumbnail:</h3>
            <img src="{{ Storage::url($record->thumbnail) }}" alt="Thumbnail" class="w-32 h-32 object-cover">
        </div>
    @endif
</x-filament::page>

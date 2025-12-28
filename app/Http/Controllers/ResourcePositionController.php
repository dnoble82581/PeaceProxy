<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use Illuminate\Http\Request;

class ResourcePositionController
{
    public function update(Request $request, Resource $resource)
    {
        $validated = $request->validate([
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
        ]);

        // TODO: authorize that this resource belongs to the same tenant, etc.

        $resource->update([
            'latitude' => $validated['lat'],
            'longitude' => $validated['lng'],
        ]);

        return response()->json(['status' => 'ok']);
    }
}

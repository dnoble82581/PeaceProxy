<?php

namespace App\Http\Controllers\Twilio;

use App\Services\Call\CallPlacementService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class OutboundCallController extends Controller
{
    protected CallPlacementService $placer;

    public function __construct(CallPlacementService $placer)
    {
        $this->placer = $placer; // ✅ no property promotion
    }

    public function store(Request $request)
    {
        // $this->placer->dialSubject(...);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FormResponse;
use App\Models\FormVersion;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $responses = FormResponse::with(['user', 'formVersion'])->orderBy('created_at', 'desc')->get();
        return view('admin.reports.index', compact('responses'));
    }

    public function show(FormResponse $response)
    {
        $response->load(['user', 'formVersion.phases.fields', 'fieldResponses.field']);
        return view('admin.reports.show', compact('response'));
    }
}

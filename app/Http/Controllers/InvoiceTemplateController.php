<?php

namespace App\Http\Controllers;

use App\Models\InvoiceTemplate;
use Illuminate\Http\Request;


class InvoiceTemplateController extends Controller
{
     public function index()
    {
        $invoiceTemplates = InvoiceTemplate::all();
        return view('invoice_templetes.index', compact('invoiceTemplates'));
    }

    public function toggleDefault(InvoiceTemplate $template)
    {
        // If trying to set as default, remove default from all other templates
        if (!$template->is_default) {
            InvoiceTemplate::where('is_default', true)->update(['is_default' => false]);
            $template->is_default = true;
            $message = 'Template set as default successfully';
        } else {
            // If already default, you might want to prevent unsetting default
            // Option 1: Prevent unsetting (recommended)
            return response()->json([
                'success' => false,
                'message' => 'Cannot unset default template. Another template must be set as default first.'
            ], 422);

            // Option 2: Allow unsetting (if you have fallback logic)
            // $template->is_default = false;
            // $message = 'Default template unset successfully';
        }

        $template->save();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'is_default' => $template->is_default
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    public function toggleStatus(InvoiceTemplate $template)
    {
        // Prevent disabling the default template
        if ($template->is_default && $template->status) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot disable the default template'
                ], 422);
            }
            return redirect()->back()->with('error', 'Cannot disable the default template');
        }

        $template->status = !$template->status;
        $template->save();

        $message = $template->status ? 'Template activated successfully' : 'Template deactivated successfully';

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'status' => $template->status
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    public function view($id)
    {
        $template = InvoiceTemplate::findOrFail($id);
        return view('invoices.templates.classic', compact('template'));
    }
}

<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SupportTicketAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->isSuperAdmin()) {
                abort(403);
            }
            return $next($request);
        });
    }

    // Super admin - tüm destek talepleri
    public function index(Request $request)
    {
        $query = SupportTicketAdminController::with(['user', 'tenant']);

        // Filtreler
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('tenant_id')) {
            $query->where('tenant_id', $request->tenant_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(15);
        
        $tenants = Tenant::select('id', 'firma_adi')->orderBy('firma_adi')->get();
        
        $categories = [
            'teknik_sorun' => 'Teknik Sorun',
            'faturalandirma' => 'Faturalandırma',
            'ozellik_talebi' => 'Özellik Talebi',
            'genel_destek' => 'Genel Destek',
            'hesap_sorunu' => 'Hesap Sorunu'
        ];

        return view('frontend.secure.super_admin.support.index', compact('tickets', 'tenants', 'categories'));
    }

    // Super admin - destek talebi detay
    public function show(SupportTicketAdminController $ticket)
    {
        $ticket->load('replies.user', 'user', 'tenant');
        return view('frontend.secure.super_admin.support.show', compact('ticket'));
    }

    // Super admin - yanıt ver
    public function reply(Request $request, SupportTicketAdminController $ticket)
    {
        $request->validate([
            'message' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx'
        ]);

        // Kapalı taleplere yanıt verilemez
        if (!$ticket->canBeReplied()) {
            return back()->with('error', 'Kapalı taleplere yanıt verilemez.');
        }

        $attachments = [];

        // Dosya yükleme
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('support-attachments', $filename, 'public');
                $attachments[] = [
                    'original_name' => $file->getClientOriginalName(),
                    'stored_name' => $filename,
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType()
                ];
            }
        }

        SupportTicketReply::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => Auth::user()->user_id,
            'message' => $request->message,
            'attachments' => !empty($attachments) ? $attachments : null,
            'is_admin_reply' => true
        ]);

        return back()->with('success', 'Yanıtınız başarıyla gönderildi.');
    }

    // Super admin - talebi kapat
    public function close(SupportTicketAdminController $ticket)
    {
        $ticket->close();
        return back()->with('success', 'Destek talebi kapatıldı.');
    }

    // Super admin - talebi yeniden aç
    public function reopen(SupportTicketAdminController $ticket)
    {
        $ticket->update(['status' => 'acik']);
        return back()->with('success', 'Destek talebi yeniden açıldı.');
    }

    // Dashboard istatistikleri
    public function dashboard()
    {
        $stats = [
            'total' => SupportTicketAdminController::count(),
            'open' => SupportTicketAdminController::where('status', 'acik')->count(),
            'answered' => SupportTicketAdminController::where('status', 'cevaplandi')->count(),
            'closed' => SupportSupportTicketAdminControllerTicket::where('status', 'kapali')->count(),
            'high_priority' => SupportTicketAdminController::where('priority', 'yuksek')->where('status', '!=', 'kapali')->count()
        ];

        $recentTickets = SupportTicketAdminController::with(['user', 'tenant'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('super-admin.support.dashboard', compact('stats', 'recentTickets'));
    }
}
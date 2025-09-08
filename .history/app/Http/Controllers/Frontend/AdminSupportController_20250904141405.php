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

class AdminSupportController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->isSuperAdmin()) {
                abort(403, 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
            }
            return $next($request);
        });
    }

    // Super admin - tüm destek talepleri
    public function index(Request $request)
    {
        // Debug: Hangi controller çalışıyor kontrol et
        logger('AdminSupportController index() çalışıyor', [
            'user' => Auth::user()->name,
            'is_super_admin' => Auth::user()->isSuperAdmin()
        ]);

        $query = SupportTicket::with(['user', 'tenant']);

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

        $tickets = $query->orderBy('last_reply_at', 'desc')->paginate(10);
        
        $tenants = Tenant::select('id', 'firma_adi')->orderBy('firma_adi')->get();
        
        $categories = [
            'teknik_sorun' => 'Teknik Sorun',
            'faturalandirma' => 'Faturalandırma',
            'ozellik_talebi' => 'Özellik Talebi',
            'genel_destek' => 'Genel Destek',
            'hesap_sorunu' => 'Hesap Sorunu'
        ];
         $priorities = [
        'acil' => 'Acil',
        'kritik' => 'Kritik',
        'yuksek' => 'Yüksek',
        'orta' => 'Orta',
        'dusuk' => 'Düşük'
    ];


        return view('frontend.secure.super_admin.support.index', compact('tickets', 'tenants', 'categories','priorities'));
    }

    // Super admin - destek talebi detay
    public function show(SupportTicket $ticket)
    {
        $ticket->load('replies.user', 'user', 'tenant');
        return view('frontend.secure.super_admin.support.show', compact('ticket'));
    }

    // Super admin - yanıt ver
    public function reply(Request $request, SupportTicket $ticket)
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
    public function close(SupportTicket $ticket)
    {
        $ticket->close();
        return back()->with('success', 'Destek talebi kapatıldı.');
    }

    // Super admin - talebi yeniden aç
    public function reopen(SupportTicket $ticket)
    {
        $ticket->update(['status' => 'acik']);
        return back()->with('success', 'Destek talebi yeniden açıldı.');
    }

    // Dosya indirme
    public function downloadAttachment($ticketId, $fileName)
    {
        $ticket = SupportTicket::findOrFail($ticketId);
        
        $filePath = 'support-attachments/' . $fileName;
        
        if (!Storage::disk('public')->exists($filePath)) {
            abort(404);
        }

        return Storage::disk('public')->download($filePath);
    }

    // Dashboard istatistikleri
    public function dashboard()
    {
        $stats = [
            'total' => SupportTicket::count(),
            'open' => SupportTicket::where('status', 'acik')->count(),
            'answered' => SupportTicket::where('status', 'cevaplandi')->count(),
            'closed' => SupportTicket::where('status', 'kapali')->count(),
            'high_priority' => SupportTicket::where('priority', 'acil')
            ->where('status', '!=', 'kapali')
            ->count()

        ];

        $recentTickets = SupportTicket::with(['user', 'tenant'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('frontend.secure.super_admin.support.dashboard', compact('stats', 'recentTickets'));
    }
}
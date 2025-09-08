<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SupportTicketController extends Controller
{
    // Kullanıcı tarafı - destek talepleri listesi
    public function index($tenant_id)
    {
        $user = Auth::user();
        
        // Super Admin değilse tenant kontrolü yap
        if (!$user->isSuperAdmin() && $user->tenant_id != $tenant_id) {
            abort(403, 'Bu tenant\'a erişim yetkiniz yok.');
        }
        
        $query = SupportTicket::where('user_id', $user->user_id);
        
        // Super Admin değilse sadece kendi tenant'ını görsün
        if (!$user->isSuperAdmin()) {
            $query->where('tenant_id', $user->tenant_id);
        }

        $tickets = $query->orderBy('last_reply_at', 'desc')->paginate(10);

        return view('frontend.secure.support.index', compact('tickets'));
    }

    // Kullanıcı tarafı - yeni destek talebi formu
    public function create($tenant_id)
    {
        $user = Auth::user();
        
        // Super Admin değilse tenant kontrolü yap
        if (!$user->isSuperAdmin() && $user->tenant_id != $tenant_id) {
            abort(403, 'Bu tenant\'a erişim yetkiniz yok.');
        }

        $categories = [
            'teknik_sorun' => 'Teknik Sorun',
            'faturalandirma' => 'Faturalandırma',
            'ozellik_talebi' => 'Özellik Talebi',
            'genel_destek' => 'Genel Destek',
            'hesap_sorunu' => 'Hesap Sorunu'
        ];

        return view('frontend.secure.support.create', compact('categories'));
    }

    // Kullanıcı tarafı - destek talebi oluştur
    public function store(Request $request, $tenant_id)
    {
        $request->validate([
            'category' => 'required|string',
            'subject' => 'required|string|max:255',
            'priority' => 'required|in:acil,kritik,yuksek,orta,dusuk',
            'description' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx'
        ]);

        $user = Auth::user();
        
        // Super Admin değilse tenant kontrolü yap
        if (!$user->isSuperAdmin() && $user->tenant_id != $tenant_id) {
            abort(403, 'Bu tenant\'a erişim yetkiniz yok.');
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

        $ticket = SupportTicket::create([
            'ticket_number' => SupportTicket::generateTicketNumber(),
            'tenant_id' => $tenant_id,
            'user_id' => $user->user_id,
            'category' => $request->category,
            'subject' => $request->subject,
            'priority' => $request->priority,
            'description' => $request->description,
            'attachments' => !empty($attachments) ? $attachments : null,
            'status' => 'acik',
            'last_reply_at' => now()
        ]);

        return redirect()->route('support.index', $tenant_id)
                        ->with('success', 'Destek talebiniz başarıyla oluşturuldu. Talep numaranız: ' . $ticket->ticket_number);
    }

    // Kullanıcı tarafı - destek talebi detay
    public function show($tenant_id, SupportTicket $ticket)
    {
        $user = Auth::user();
        
        // Super Admin tümünü görebilir, diğerleri sadece kendilerininkini
        if (!$user->isSuperAdmin() && $ticket->user_id !== $user->user_id) {
            abort(403, 'Bu destek talebini görüntüleme yetkiniz bulunmamaktadır.');
        }

        // Tenant kontrolü (super admin değilse)
        if (!$user->isSuperAdmin() && $ticket->tenant_id != $tenant_id) {
            abort(403, 'Bu destek talebi size ait değil.');
        }

        $ticket->load('replies.user', 'user');

        return view('frontend.secure.support.show', compact('ticket'));
    }

    // Kullanıcı tarafı - yanıt ekle
    public function reply(Request $request, $tenant_id, SupportTicket $ticket)
    {
        $request->validate([
            'message' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx'
        ]);

        $user = Auth::user();
        
        // Super Admin tümünü yanıtlayabilir, diğerleri sadece kendilerininkini
        if (!$user->isSuperAdmin() && $ticket->user_id !== $user->user_id) {
            abort(403, 'Bu destek talebini yanıtlama yetkiniz bulunmamaktadır.');
        }

        // Tenant kontrolü (super admin değilse)
        if (!$user->isSuperAdmin() && $ticket->tenant_id != $tenant_id) {
            abort(403, 'Bu destek talebi size ait değil.');
        }

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
            'user_id' => $user->user_id,
            'message' => $request->message,
            'attachments' => !empty($attachments) ? $attachments : null,
            'is_admin_reply' => $user->isSuperAdmin() // Super Admin ise admin yanıtı olarak işaretle
        ]);

        return back()->with('success', 'Yanıtınız başarıyla gönderildi.');
    }

    // Dosya indirme
    public function downloadAttachment($tenant_id, $ticketId, $fileName)
    {
        $user = Auth::user();
        $ticket = SupportTicket::findOrFail($ticketId);
        
        // Kullanıcı kontrolü - Super Admin veya ticket sahibi
        if (!$user->isSuperAdmin() && $ticket->user_id !== $user->user_id) {
            abort(403);
        }

        $filePath = 'support-attachments/' . $fileName;
        
        if (!Storage::disk('public')->exists($filePath)) {
            abort(404);
        }

        return Storage::disk('public')->download($filePath);
    }
}
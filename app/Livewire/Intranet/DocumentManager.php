<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Department;
use App\Models\DocumentVersion;
use App\Models\DocumentStatus;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentManager extends Component
{
    use WithFileUploads, WithPagination;

    // Filter Props (Livewire 3 URL persistence)
    #[Url]
    public $search = '';

    #[Url]
    public $filterType = '';

    #[Url]
    public $filterDepartment = '';

    #[Url]
    public $filterStatus = '';

    // We link the sidebar "filterRegulatory" to this property in the URL
    #[Url]
    public $filterRegulatory = false;

    // Create Props
    public $title, $code, $document_status_id, $department_id, $document_type_id, $is_regulatory = false, $file;
    public $visibility = 'public', $expires_at;
    public $showCreateModal = false;

    // History Props
    public $showHistoryModal = false;
    public $selectedDocument = null;
    public $newVersionNumber, $newFile;

    // Viewer Props
    public $showViewer = false;
    public $viewingFilePath = '';

    public function openViewer($filePath)
    {
        $this->viewingFilePath = Storage::disk('public')->url($filePath);
        $this->showViewer = true;
    }

    public function mount()
    {
        $s = DocumentStatus::where('slug', 'pendiente')->first();
        $this->document_status_id = $s->id ?? 1;
        $this->visibility = 'public';
    }

    public function openCreateModal()
    {
        $this->reset(['title', 'code', 'file', 'is_regulatory', 'visibility', 'expires_at']);
        $this->showCreateModal = true;
    }

    public function store()
    {
        $this->validate([
            'title' => 'required|min:5',
            'code' => 'required|unique:documents,code',
            'document_status_id' => 'required',
            'department_id' => 'required',
            'document_type_id' => 'required',
            'visibility' => 'required|in:public,department,private',
            'expires_at' => 'nullable|date',
            'file' => 'required|mimes:pdf|max:10240',
        ]);

        $isAdmin = Auth::user()->hasRole('Administrador');

        $document = Document::create([
            'title' => $this->title,
            'code' => $this->code,
            'document_status_id' => $this->document_status_id,
            'department_id' => $this->department_id,
            'document_type_id' => $this->document_type_id,
            'is_regulatory' => $this->is_regulatory,
            'visibility' => $this->visibility,
            'expires_at' => $this->expires_at,
            'created_by' => Auth::id(),
            'approval_status' => $isAdmin ? 'approved' : 'pending',
            'approved_at' => $isAdmin ? now() : null,
            'approved_by' => $isAdmin ? Auth::id() : null,
        ]);

        $path = $this->file->store('documents', 'public');

        DocumentVersion::create([
            'document_id' => $document->id,
            'version' => '1.0',
            'file_path' => $path,
            'created_by' => Auth::id() ?? 1,
            'approved_at' => $isAdmin ? now() : null,
            'approved_by' => $isAdmin ? Auth::id() : null,
        ]);

        // Trigger Notification if pending
        if (!$isAdmin) {
            $admins = User::role('Administrador')->get();
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\ApprovalRequested($document, Auth::user()));
            }
        }

        AuditLog::create([
            'user_id' => Auth::id() ?? 1,
            'action' => 'CREATE_DOCUMENT',
            'auditable_type' => Document::class,
            'auditable_id' => $document->id,
            'details' => [
                'title' => $document->title, 
                'code' => $document->code, 
                'approval_status' => $document->approval_status
            ],
            'ip_address' => request()->ip()
        ]);

        // Reset all filters to show the new document
        $this->reset(['search', 'filterType', 'filterDepartment', 'filterStatus', 'filterRegulatory']);
        $this->showCreateModal = false;
        
        $msg = $isAdmin ? 'Documento corporativo creado y aprobado con éxito.' : 'Documento enviado a revisión institucional.';
        $this->dispatch('notify', $msg);
        $this->resetPage();
    }

    public function openHistoryModal($id)
    {
        $this->selectedDocument = Document::with(['versions.creator', 'versions.approver', 'status'])->find($id);
        $lastVersion = $this->selectedDocument->versions->last();
        $this->newVersionNumber = (float)$lastVersion->version + 1.0;
        $this->showHistoryModal = true;
    }

    public function uploadNewVersion()
    {
        $this->validate([
            'newVersionNumber' => 'required',
            'newFile' => 'required|mimes:pdf|max:10240',
        ]);

        $path = $this->newFile->store('documents', 'public');

        DocumentVersion::create([
            'document_id' => $this->selectedDocument->id,
            'version' => $this->newVersionNumber,
            'file_path' => $path,
            'created_by' => Auth::id() ?? 1,
        ]);

        AuditLog::create([
            'user_id' => Auth::id() ?? 1,
            'action' => 'UPLOAD_VERSION',
            'auditable_type' => Document::class,
            'auditable_id' => $this->selectedDocument->id,
            'details' => ['version' => $this->newVersionNumber],
            'ip_address' => request()->ip()
        ]);

        $this->openHistoryModal($this->selectedDocument->id);
        $this->reset('newFile');
        $this->dispatch('notify', 'Nueva versión cargada y pendiente de validación.');
    }

    public function approveDocument($documentId)
    {
        $document = Document::find($documentId);
        $document->update([
            'approval_status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        // Also approve ALL versions of this document that were pending
        $document->versions()->whereNull('approved_at')->update([
            'approved_at' => now(),
            'approved_by' => Auth::id(),
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'APPROVE_DOCUMENT',
            'auditable_type' => Document::class,
            'auditable_id' => $document->id,
            'details' => ['status' => 'approved'],
            'ip_address' => request()->ip()
        ]);

        $this->dispatch('notify', 'Documento aprobado institucionalmente.');
    }

    public function rejectDocument($documentId, $reason)
    {
        $document = Document::find($documentId);
        $document->update([
            'approval_status' => 'rejected',
            'rejection_reason' => $reason,
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'REJECT_DOCUMENT',
            'auditable_type' => Document::class,
            'auditable_id' => $document->id,
            'details' => ['reason' => $reason],
            'ip_address' => request()->ip()
        ]);

        $this->dispatch('notify', 'Documento rechazado con observaciones.');
    }

    public function approveVersion($versionId)
    {
        $version = DocumentVersion::find($versionId);
        $version->update([
            'approved_at' => now(),
            'approved_by' => Auth::id() ?? 1,
        ]);

        $s = DocumentStatus::where('slug', 'vigente')->first();
        if ($s) {
            $version->document->update(['document_status_id' => $s->id, 'approval_status' => 'approved']);
        }

        AuditLog::create([
            'user_id' => Auth::id() ?? 1,
            'action' => 'APPROVE_VERSION',
            'auditable_type' => Document::class,
            'auditable_id' => $version->document_id,
            'details' => ['version' => $version->version, 'approved_at' => now()->toDateTimeString()],
            'ip_address' => request()->ip()
        ]);

        $this->openHistoryModal($version->document_id);
        $this->dispatch('notify', 'Versión aprobada y marcada como vigente.');
    }

    public function render()
    {
        // APPLY VISIBILITY SCOPE
        $query = Document::with(['documentType', 'department', 'status', 'versions'])
                 ->visibleTo(Auth::user());

        if ($this->search) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('code', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterType) {
            $query->where('document_type_id', $this->filterType);
        }

        if ($this->filterDepartment) {
            $query->where('department_id', $this->filterDepartment);
        }

        if ($this->filterStatus) {
            $query->where('document_status_id', $this->filterStatus);
        }

        // If filterRegulatory is enabled, ONLY show regulatory documents
        if ($this->filterRegulatory) {
            $query->where('is_regulatory', true);
        }

        return view('livewire.document-manager', [
            'documents' => $query->latest()->paginate(10),
            'departments' => Department::all(),
            'documentTypes' => DocumentType::all(),
        ])->layout('components.layouts.app');
    }
}

<?php

namespace App\Livewire\CallCenter;

use Livewire\Component;
use App\Models\CallCenterRegistro;

class FollowUpReminder extends Component
{
    public $pendingCount = 0;

    public function mount()
    {
        $this->refreshReminders();
    }

    public function refreshReminders()
    {
        if (!auth()->check()) return;

        $this->pendingCount = CallCenterRegistro::where('operador_id', auth()->id())
            ->whereNotNull('proximo_contacto_at')
            ->where('proximo_contacto_at', '<=', now()->addMinutes(30))
            ->whereHas('estado', function($q) {
                $q->where('nombre', 'NOT LIKE', '%Promovido%')
                  ->where('nombre', 'NOT LIKE', '%Completado%');
            })
            ->count();
    }

    public function render()
    {
        return view('livewire.call-center.follow-up-reminder');
    }
}

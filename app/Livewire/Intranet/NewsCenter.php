<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Announcement;

class NewsCenter extends Component
{
    use WithPagination;

    public $search = '';
    public $category = '';
    public $selectedNews = null;

    protected $listeners = ['closeModal' => 'closeNews'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function viewNews($id)
    {
        $this->selectedNews = Announcement::find($id);
    }

    public function closeNews()
    {
        $this->selectedNews = null;
    }

    public function render()
    {
        $query = Announcement::active();

        if ($this->search) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('content', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->category) {
            $query->where('category', $this->category);
        }

        $news = $query->latest('published_at')->paginate(9);

        return view('livewire.news-center', [
            'news' => $news,
            'categories' => Announcement::active()->distinct()->pluck('category')
        ])->layout('components.layouts.app');
    }
}

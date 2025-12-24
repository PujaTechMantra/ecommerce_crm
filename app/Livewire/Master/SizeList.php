<?php

namespace App\Livewire\Master;

use Livewire\Component;
use App\Models\Size;

class SizeList extends Component
{
    public $active_tab = 1;

    public $size_id;
    public $title;
    public $search = '';

    protected function rules()
    {
        return [
            'title' => 'required|string|max:255|unique:sizes,name,' . $this->size_id,
        ];
    }

    public function ActiveCreateTab($tab)
    {
        $this->resetForm();
        $this->active_tab = $tab;
    }

     public function toggleStatus($id)
    {
        $size = Size::findOrFail($id);
        $size->status = !$size->status;
        $size->save();

        session()->flash('message', 'Size status updated successfully!');
    }

    public function store()
    {
        $this->validate();

        Size::create([
            'name' => $this->title,
        ]);

        session()->flash('success', 'Size added successfully!');
        $this->ActiveCreateTab(1);
    }

    public function edit($id)
    {
        $size = Size::findOrFail($id);

        $this->size_id = $size->id;
        $this->title = $size->name;

        $this->active_tab = 3;
    }

    public function update()
    {
        $this->validate();

        Size::where('id', $this->size_id)->update([
            'name' => $this->title,
        ]);

        session()->flash('success', 'Size updated successfully!');
        $this->ActiveCreateTab(1);
    }

    public function delete($id)
    {
        $color = Size::findOrFail($id);
        $color->delete(); // Soft delete

        session()->flash('success', 'Size deleted successfully!');
    }

    public function resetForm()
    {
        $this->reset(['size_id', 'title']);
        $this->resetErrorBag();
    }

    public function render()
    {
        $sizes = Size::whereNull('deleted_at')
            ->where('name', 'like', "%{$this->search}%")
            ->latest()
            ->get();

        return view('livewire.master.size-list', compact('sizes'));
    }
}

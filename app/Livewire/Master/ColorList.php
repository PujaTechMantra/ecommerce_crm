<?php

namespace App\Livewire\Master;

use Livewire\Component;
use App\Models\Color;

class ColorList extends Component
{
    public $active_tab = 1;

    public $color_id;
    public $title, $code;
    public $search = '';

    protected function rules()
    {
        return [
            'title' => 'required|string|max:255|unique:colors,name,' . $this->color_id,
            'code'  => 'required|string|max:255',
        ];
    }

    public function ActiveCreateTab($tab)
    {
        $this->resetForm();
        $this->active_tab = $tab;
    }

    public function store()
    {
        $this->validate();

        Color::create([
            'name' => $this->title,
            'code' => $this->code,
        ]);

        session()->flash('success', 'Color added successfully!');
        $this->ActiveCreateTab(1);
    }

    public function edit($id)
    {
        $color = Color::findOrFail($id);

        $this->color_id = $color->id;
        $this->title    = $color->name;
        $this->code     = $color->code;

        $this->active_tab = 3;
    }

    public function update()
    {
        $this->validate();

        Color::where('id', $this->color_id)->update([
            'name' => $this->title,
            'code' => $this->code,
        ]);

        session()->flash('success', 'Color updated successfully!');
        $this->ActiveCreateTab(1);
    }
    
    public function toggleStatus($id)
    {
        $color = Color::findOrFail($id);
        $color->status = !$color->status;
        $color->save();

        session()->flash('message', 'Size status updated successfully!');
    }

    public function destroy($id)
    {
        $color = Color::findOrFail($id);
        $color->delete(); // Soft delete

        session()->flash('success', 'Color deleted successfully!');
    }

    public function resetForm()
    {
        $this->reset(['color_id', 'title', 'code']);
        $this->resetErrorBag();
    }

    public function render()
    {
        $colors = Color::where('deleted_at', null)
        ->where('name', 'like', "%{$this->search}%")
            ->latest()
            ->get(); 

        return view('livewire.master.color-list', compact('colors'));
    }
}

<?php

namespace App\Livewire\Product;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Collection;

class CollectionList extends Component
{
   use WithPagination, WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $collectionId;
    public $name;
    public $image;

    protected function rules()
    {
        return [
            'name'  => 'required|string|max:255|unique:collections,name,NULL,id,is_deleted,0',
            'image' => 'nullable|image|max:10240',
        ];
    }

    public function render()
    {
        $collections = Collection::where('is_deleted', 0)
            ->where('name', 'like', '%' . trim($this->search) . '%')
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('livewire.product.collection-list', compact('collections'));
    }


    public function save()
    {
        $this->validate();

        $data = [
            'name'  => $this->name,
            'slug'  => \Str::slug($this->name) . '-' . rand(1000, 9999),
            
        ];

        if ($this->image) {
            $file = $this->image->store('uploads/collection', 'public');
            $data['image'] = $file;
        }

        Collection::create($data);

        session()->flash('message', 'Collection created successfully.');
        $this->resetForm();
    }

    public function edit($id)
    {
        $c = Collection::findOrFail($id);

        $this->collectionId = $c->id;
        $this->name = $c->name;
    }

    public function update()
    {
        $this->validate([
            'name'  => 'required|string|max:255|unique:collections,name,' . $this->collectionId . ',id,is_deleted,0',
            'image' => 'nullable|image|max:10240',
        ]);

        $collection = Collection::findOrFail($this->collectionId);

        $data = ['name' => $this->name];

        if ($this->image) {
            $file = $this->image->store('uploads/collection', 'public');
            $data['image'] = $file;
        }

        $collection->update($data);

        session()->flash('message', 'Collection updated successfully.');
        $this->resetForm();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function toggleStatus($id)
    {
        $c = Collection::findOrFail($id);
        $c->status = !$c->status;
        $c->save();

        session()->flash('message', 'Status updated successfully.');
    }

    public function destroy($id)
    {
        $c = Collection::findOrFail($id);
        $c->is_deleted = 1;
        $c->save();

        session()->flash('message', 'Collection deleted successfully.');
    }

    public function resetForm()
    {
        $this->collectionId = null;
        $this->name = '';
        $this->image = null;
    }

    public function refresh()
    {
        $this->resetForm();
        $this->search = '';
    }
}

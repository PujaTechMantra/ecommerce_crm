<?php

namespace App\Livewire\Product;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Category;
use App\Models\Collection;
use Illuminate\Support\Str;

class MasterCategory extends Component
{
    use WithFileUploads;

    public $categoryId, $title, $image, $search = '';
    public $collection_id;
    public $categories = [];
    public $collections = [];
    public $existingImage;

    protected $messages = [
        'collection_id.required' => 'Please select a collection.',
    ];

    protected function rules()
    {
        return [
            'title' => 'required|max:255|unique:categories,title,' 
                        . $this->categoryId . ',id,deleted_at,NULL',

            'image' => 'nullable|image|max:10240',

            'collection_id' => 'required|exists:collections,id',
        ];
    }


    public function mount()
    {
        $this->collections = Collection::where('status', 1)->get();
    }

    public function render()
    {
        $this->categories = Category::with('collection')
            ->whereNull('deleted_at')
            ->where('title', 'like', '%' . trim($this->search) . '%')
            ->orderBy('id', 'DESC')
            ->get();

        return view('livewire.product.master-category');
    }

    public function save()
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'slug' => Str::slug($this->title) . '-' . rand(1000, 9999),
            'collection_id' => $this->collection_id,
        ];

        if ($this->image) {
            $data['image'] = $this->image->store('uploads/category', 'public');
        }

        Category::create($data);

        session()->flash('message', 'Category created successfully!');
        $this->resetForm();
    }

    public function edit($id)
    {
        $cat = Category::findOrFail($id);

        $this->categoryId = $cat->id;
        $this->title = $cat->title;
        $this->collection_id = $cat->collection_id;
        $this->existingImage = $cat->image;
    }

    public function update()
    {
        $this->validate();

        $cat = Category::findOrFail($this->categoryId);

        $data = [
            'title' => $this->title,
            'slug'  => Str::slug($this->title) . '-' . rand(1000, 9999),
            'collection_id' => $this->collection_id,
        ];

        if ($this->image) {
            if ($cat->image && \Storage::disk('public')->exists($cat->image)) {
                \Storage::disk('public')->delete($cat->image);
            }

            $data['image'] = $this->image->store('uploads/category', 'public');
        }

        $cat->update($data);

        session()->flash('message', 'Category updated successfully!');
        $this->resetForm();
    }

    public function toggleStatus($id)
    {
        $cat = Category::findOrFail($id);
        $cat->status = !$cat->status;
        $cat->save();
    }

    public function destroy($id)
    {
        $cat = Category::findOrFail($id);
        $cat->deleted_at = now();
        $cat->save();

        session()->flash('message', 'Category deleted successfully!');
    }

    public function resetForm()
    {
        $this->categoryId = null;
        $this->title = '';
        $this->image = null;
        $this->collection_id = null;
        $this->existingImage = null;
    }

    public function refresh()
    {
        $this->resetForm();
        $this->search = '';
    }
}

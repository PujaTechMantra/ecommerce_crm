<?php

namespace App\Livewire\Product;

use Livewire\Component;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Collection;
use Illuminate\Validation\Rule;
use Livewire\WithPagination;


class MasterSubCategory extends Component
{
    use WithPagination;

    public $subCategoryId;
    public $collection_id;
    public $category_id;
    public $title;
    public $search = '';

    public $collections = [];
    public $categories = [];
    
    public function mount()
    {
        $this->collections = Collection::where('status', 1)
            ->orderBy('name')
            ->get();

        $this->categories = collect(); // empty initially
    }

   public function updatedCollectionId($value)
    {
        $this->resetPage();
        $value = (int) $value;
        $this->category_id = null;
        $this->categories = Category::where('collection_id', $value)
            ->where('status', 1)
            ->orderBy('title')
            ->get();
    }



    public function store(){
        $this->validate([
            'category_id' => 'required|exists:categories,id',
                'title' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('sub_categories')->where(function ($query) {
                        return $query->where('category_id', $this->category_id);
                    }),
                ],
        ],[
            'category_id.required' => 'The category field is mandatory.',
            'category_id.exists' => 'The selected category is invalid.',
            'title.required' => 'The title field cannot be empty.',
            'title.max' => 'The title may not be greater than 255 characters.',
            'title.unique' => 'A sub-category with this title already exists under the selected category.',
        ]);

        SubCategory::create([
            'title'=>$this->title,
            'category_id'=>$this->category_id
        ]);

        session()->flash('message','SubCategory Created Successfully');
        $this->resetForm();
    }

    public function edit($id)
    {
        $subcategory = SubCategory::with('category')->findOrFail($id);

        $this->subCategoryId = $subcategory->id;
        $this->title = $subcategory->title;
        $this->collection_id = $subcategory->category->collection_id;

        // Load categories of that collection
        $this->categories = Category::where('collection_id', $this->collection_id)
            ->where('status', 1)
            ->orderBy('title')
            ->get();

        $this->category_id = $subcategory->category_id;
    }



    public function update()
    {
        $this->validate([
            'category_id' => 'required|exists:categories,id',
                'title' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('sub_categories')->where(function ($query) {
                        return $query->where('category_id', $this->category_id);
                    })->ignore($this->subCategoryId),
                ],
        ]);

        $subcategory = SubCategory::findOrFail($this->subCategoryId);
        $subcategory->update([
            'category_id' => $this->category_id,
            'title' => $this->title,    
        ]);

        session()->flash('message', 'Subcategory updated successfully!');
        $this->resetForm();
    }

    public function destroy($id)
    {
        $subcategory = SubCategory::findOrFail($id);

        $subcategory->delete(); // soft delete

        session()->flash('message', 'Subcategory deleted successfully!');
    }

    public function toggleStatus($id)
    {
        $category = SubCategory::findOrFail($id);
        $category->status = !$category->status;  // Toggle the status
        $category->save();  // Save the updated status

        session()->flash('message', 'SubCategory status updated successfully!');
    }

    public function resetForm()
    {
        $this->collection_id = '';
        $this->category_id = '';
        $this->title = '';
        $this->subCategoryId = null;
        $this->categories = collect();
    }


    public function refresh()
    {
        $this->resetForm();
        $this->search = ''; // Reset the search filter
        $subcategories = SubCategory::with('category')->paginate(5);
        // session()->flash('message', 'Data refreshed successfully!');
    }
    
    // public function render()
    // {
        
    //     $subcategories = SubCategory::with('category.collection')->paginate(5);
    //     return view('livewire.product.master-sub-category', [
    //         'subcategories' => $subcategories
    //     ]);
    // }
    public function render()
    {
        $subcategories = SubCategory::with('category.collection')
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%');
            })
            ->paginate(5);

        return view('livewire.product.master-sub-category', [
            'subcategories' => $subcategories
        ]);
    }
}

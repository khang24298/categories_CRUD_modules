<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\CategoryRepository;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\Category\StoreCategoryRequest; // Import the request
use App\Http\Requests\Category\UpdateCategoryRequest; // Import the request
use App\Http\Requests\Category\IndexCategoryRequest; // Import the request
use Illuminate\Http\Response;
class CategoryController extends Controller
{
    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function index(IndexCategoryRequest $request): Response
    {
      // ... logic for fetching categories with filtering
      try {
        //code...
        // dd($request->all());
        $filters = $request->all() ?? [];
        $data = $this->categoryRepository->filter($filters);
        $statusCode = 200;
        $code = 0;
      } catch (\Exception $e) {
        //throw $th;
        $code = $e->getCode();
        $message = $e->getMessage();
        $statusCode = 500;
        $data = null;
      }
      return response(['data' => $data, 'code' => $code, 'message' => $message ?? 'Success!'], $statusCode);
    }

    public function store(StoreCategoryRequest $request): Response
    {
        // ... validation and category creation logic
        $validatedData = $request->validated();
        try {
          $data = $this->categoryRepository->create($validatedData);
          $statusCode = 201; // Category created!
          $code = 0;
        } catch (\Exception $e) {
          $code = $e->getCode();
          $message = $e->getMessage();
          $statusCode = 500;
          $data = null;
        }
        return response(['data' => $data, 'code' => $code, 'message' => $message ?? 'Success!'], $statusCode);
    }

    public function update(UpdateCategoryRequest $request, Category $category): Response
    {
        // ... validation and category update logic
        $validatedData = $request->validated();
        try {
          $data = $this->categoryRepository->update($category->id, $validatedData);
          $statusCode = 204; // Category created!
          $code = 0;
        } catch (\Exception $e) {
          $code = $e->getCode();
          $message = $e->getMessage();
          $statusCode = 500;
          $data = null;
        }
        return response(['data' => $data, 'code' => $code, 'message' => $message ?? 'Success!'], $statusCode);
    }

    public function destroy(Category $category): Response
    {
        // ... delete category
        try { 
          $this->categoryRepository->delete($category->id);
          $code = 0;
          $statusCode = 200;
        } catch (\Exception $e) {
          $message = $e->getMessage();
          $code = $e->getCode();
          $statusCode = 500;
        }
        return response(['code' => $code, 'message' => $message ?? 'Success!'], $statusCode);
    }
}

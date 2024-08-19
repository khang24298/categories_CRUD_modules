<?php
namespace Tests\Feature;

use App\Http\Controllers\Admin\CategoryController;
use App\Models\Category;
use App\Repositories\CategoryRepository;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class CategoryTest extends TestCase
{
    use RefreshDatabase;

    // for index
    public function test_the_get_categories_endpoint_return_successful_response(): void
    {
        $this->seed();
        $response = $this->get('/admin/categories');
        $response->assertStatus(200);
        $response->assertSee('data');
        $response->assertJsonCount(10, "data");
    }

    public function test_the_get_categories_endpoint_successfully_with_level_filter(): void
    {
        $this->seed();
        $level = 2;
        $response = $this->get("/admin/categories?level={$level}");
        $response->assertStatus(200);
        $response->assertSee('data');
        $response->assertJsonFragment(['level' => 2]);
    }

    public function test_the_get_categories_endpoint_successfully_with_level_and_active_filter(): void
    {
        $this->seed();
        $level = 2;
        $active = true;
        $response = $this->get("/admin/categories?level={$level}&active={$active}");
        $response->assertStatus(200);
        $response->assertSee('data');
        $response->assertJsonFragment(['level' => 2, 'active' => 1]);
    }

    public function test_the_get_categories_endpoint_throw_exception(): void
    {
        $this->seed();
        // Mock a scenario where a custom exception is thrown
        $this->mock(CategoryRepository::class, function ($mock) {
            $mock->shouldReceive('filter')->andThrow(new Exception('Error while query DB', 1));
        });

        $response = $this->get('/admin/categories');

        $response->assertStatus(500); // Assuming a 404 Not Found response for category not found
        $response->assertJsonFragment([
            'data' => null,
            'message' => "Error while query DB",
            'code' => 1
        ]);
    }

    // for store
    public function test_the_post_categories_endpoint_return_successful_response(): void
    {
		$exampleCategory = [
            'level' => 2, 
            'type' => 'service', 
            'key' => 'test_key',
            'code' => 'TEST_CODE',
            'name' => json_encode(['en' => 'helloworld']),
            'active' => FALSE,
		];
        $response = $this->post(
          '/admin/categories',
          $exampleCategory
        );
        $response->assertStatus(201);
		$response->assertJsonFragment($exampleCategory);
    }

    public function test_the_post_categories_endpoint_failed_validation_response(): void
    {
		$exampleCategory = [
            'level' => 10, 
            'type' => 'service',
            'key' => 'test_key',
            'code' => 'TEST_CODE',
            'name' => json_encode(['en' => 'helloworld']),
            'active' => FALSE,
		];
        $response = $this->post(
          '/admin/categories',
          $exampleCategory
        );
        $response->assertStatus(422);
        $response->assertJsonFragment(
			['data' => ['level' => [0 => "The level field must not be greater than 3."]]]
		);
    }

	public function test_the_post_categories_endpoint_return_server_exception(): void
    {
		$exampleCategory = [
            'level' => 3, 
            'type' => 'service',
            'key' => 'test_key',
            'code' => 'TEST_CODE',
            'name' => json_encode(['en' => 'helloworld']),
            'active' => TRUE,
		];

		$this->mock(CategoryRepository::class, function ($mock) {
			$mock->shouldReceive('create')->andThrow(new Exception("Error Processing Request", 1));
		});
		
        $response = $this->post(
          '/admin/categories',
          $exampleCategory
        );
        $response->assertStatus(500);
        $response->assertJsonFragment(
			['data' => null, 'message' => 'Error Processing Request', 'code' => 1]
		);
    }

    // for update
    public function test_the_put_categories_endpoint_return_successful_response(): void
    {
		// Create example category
        $categories = Category::factory(1)->create();
		$this->assertDatabaseHas('categories', ['id' => $categories[0]->id, 'key'=> $categories[0]->key]);
		$this->assertNotEquals('updated_category', $categories[0]->key);
		$id = $categories[0]->id;
		$response = $this->put(
			"/admin/categories/{$id}",
			['key' => 'updated_category']
		);
        $response->assertStatus(204);
		$this->assertDatabaseHas('categories', ['id' => $id, 'key'=> 'updated_category']);
    }

	public function test_the_put_categories_endpoint_failed_validation_response(): void
    {
		// Create example category
        $categories = Category::factory(1)->create();
		
		// Update categories[0] to key: "updated_category" and "coce" does not exist
		$response = $this->put(
			"/admin/categories/{$categories[0]->id}",
			[
				'code' => $categories[0]->code,
				'key'  => 'updated_category',
			]
		);
        $response->assertStatus(422);
		$response->assertJsonFragment(
			['data' => ['code' => [0 => "The code has already been taken."]]]
		);
    }

	public function test_the_put_categories_endpoint_return_server_exception(): void
    {
		// Create example category
        $categories = Category::factory(1)->create();
	
		$this->mock(CategoryRepository::class, function ($mock) {
			$mock->shouldReceive('update')->andThrow(new Exception("Error Processing Request", 1));
		});
		
		$response = $this->put(
			"/admin/categories/{$categories[0]->id}",
			['key' => 'updated_category']
		);
        $response->assertStatus(500);
        $response->assertJsonFragment(
			['data' => null,  'code' => 1, 'message' => 'Error Processing Request']
		);
    }

    // for delete
    public function test_the_delete_categories_endpoint_return_successful_response(): void
    {
        $categories = Category::factory(2)->create();
		$this->assertCount(2, $categories);
        $deleteCategoryId = $categories[0]->id;
        $response = $this->delete("/admin/categories/{$deleteCategoryId}");
        $response->assertStatus(200);
		$this->assertCount(1, Category::all('id'));
    }

    public function test_the_delete_categories_endpoint_not_found_response(): void
    {
        // Create example category
        $categories = Category::factory(2)->create();
		$this->assertCount(2, $categories);
        $deleteCategoryId = 0;
        $response = $this->delete("/admin/categories/{$deleteCategoryId}");
        $response->assertStatus(404);
		$this->assertCount(2, Category::all('id'));
    }
    public function test_the_delete_categories_endpoint_return_server_exception(): void
    {
        // Create example category
        $categories = Category::factory(1)->create();
        
        $this->mock(CategoryRepository::class, function ($mock) {
            $mock->shouldReceive('delete')->andThrow(new Exception("Error Processing Request", 1));
        });
        
        $response = $this->delete("/admin/categories/{$categories[0]->id}");
        $response->assertStatus(500);
        $response->assertJsonFragment(
            ['code' => 1, 'message' => 'Error Processing Request']
        );
    }
}

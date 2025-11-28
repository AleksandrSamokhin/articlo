<?php

namespace Tests\Feature\Livewire;

use App\Livewire\PostCreateCategory;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PostCreateCategoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // If your modal requires authentication
        $this->actingAs(User::factory()->create());
    }

    #[Test]
    public function it_can_render_the_component()
    {
        $component = Livewire::test(PostCreateCategory::class);

        $component->assertStatus(200);
    }

    #[Test]
    public function it_validates_required_name_field()
    {
        Livewire::test(PostCreateCategory::class)
            ->set('name', '')
            ->call('createCategory')
            ->assertHasErrors(['name' => 'required']);
    }

    #[Test]
    public function it_validates_maximum_name_length()
    {
        // Assuming you have a max:255 validation rule
        Livewire::test(PostCreateCategory::class)
            ->set('name', str_repeat('a', 256))
            ->call('createCategory')
            ->assertHasErrors(['name' => 'max']);
    }

    #[Test]
    public function it_validates_unique_category_name()
    {
        // Create existing category
        Category::create([
            'name' => 'Existing Category',
            'slug' => 'existing-category',
        ]);

        Livewire::test(PostCreateCategory::class)
            ->set('name', 'Existing Category')
            ->call('createCategory')
            ->assertHasErrors(['name' => 'unique']);
    }

    #[Test]
    public function it_can_create_a_category_successfully()
    {
        $this->assertDatabaseCount('categories', 0);

        Livewire::test(PostCreateCategory::class)
            ->set('name', 'New Technology')
            ->call('createCategory')
            ->assertHasNoErrors();

        $this->assertDatabaseCount('categories', 1);
        $this->assertDatabaseHas('categories', [
            'name' => 'New Technology',
            'slug' => 'new-technology',
        ]);
    }

    #[Test]
    public function it_generates_correct_slug_from_name()
    {
        Livewire::test(PostCreateCategory::class)
            ->set('name', 'Web Development & Design')
            ->call('createCategory');

        $category = Category::first();

        $this->assertEquals('web-development-design', $category->slug);
    }

    #[Test]
    public function it_trims_whitespace_from_name()
    {
        Livewire::test(PostCreateCategory::class)
            ->set('name', '  Laravel Tips  ')
            ->call('createCategory');

        $category = Category::first();

        $this->assertEquals('Laravel Tips', $category->name);
        $this->assertEquals('laravel-tips', $category->slug);
    }

    #[Test]
    public function it_dispatches_category_created_event()
    {
        Livewire::test(PostCreateCategory::class)
            ->set('name', 'New Category')
            ->call('createCategory')
            ->assertDispatched('category-created');
    }

    #[Test]
    public function it_resets_form_after_successful_creation()
    {
        Livewire::test(PostCreateCategory::class)
            ->set('name', 'Test Category')
            ->call('createCategory')
            ->assertSet('name', '')
            ->assertSet('showModal', false);
    }

    #[Test]
    public function it_resets_validation_errors_after_creation()
    {
        $component = Livewire::test(PostCreateCategory::class)
            ->set('name', '')
            ->call('createCategory')
            ->assertHasErrors(['name']);

        $component
            ->set('name', 'Valid Category')
            ->call('createCategory')
            ->assertHasNoErrors();
    }

    #[Test]
    public function it_handles_special_characters_in_slug()
    {
        Livewire::test(PostCreateCategory::class)
            ->set('name', 'C++ Programming!')
            ->call('createCategory');

        $category = Category::first();

        $this->assertEquals('c-programming', $category->slug);
    }

    #[Test]
    public function multiple_categories_can_be_created()
    {
        $component = Livewire::test(PostCreateCategory::class);

        $component
            ->set('name', 'First Category')
            ->call('createCategory')
            ->assertHasNoErrors();

        $component
            ->set('name', 'Second Category')
            ->call('createCategory')
            ->assertHasNoErrors();

        $this->assertDatabaseCount('categories', 2);
    }
}

<?php

namespace Yakuzan\Boiler\Tests\Services;

use Laracasts\TestDummy\Factory;
use Yakuzan\Boiler\Services\AbstractService;
use Yakuzan\Boiler\Tests\Stubs\Entities\Lesson;
use Yakuzan\Boiler\Tests\Stubs\Services\LessonService;
use Yakuzan\Boiler\Tests\TestCase;

class AbstractServiceTest extends TestCase
{
    /** @var AbstractService $service */
    protected $service;

    protected function setUp()
    {
        parent::setUp();

        $this->service = new LessonService();
    }

    /** @test */
    public function it_return_instance_of_lesson_entity()
    {
        $this->assertInstanceOf(Lesson::class, $this->service->entity());
    }

    /** @test */
    public function it_return_all_lessons()
    {
        $lessons = Factory::times(10)->create(Lesson::class);
        $result = $this->service->all();

        $this->assertEquals(count($lessons), count($result));
    }

    /** @test */
    public function it_return_lesson_with_id()
    {
        $lessons = Factory::times(10)->create(Lesson::class)->all();

        /** @var Lesson $lesson */
        $lesson = next($lessons);

        $result = $this->service->find($lesson->id);
        $this->assertEquals($result->id, $lesson->id);
    }

    /** @test */
    public function it_return_null_on_not_existed_lesson()
    {
        Factory::create(Lesson::class);
        $result = $this->service->find(9);

        $this->assertNull($result);
    }

    /** @test */
    public function it_store_new_lesson()
    {
        $result = $this->service->create(['title' => 'fake title', 'subject' => 'fake subject']);
        $this->assertGreaterThanOrEqual(1, $result->id);

        $lesson = Lesson::where('title', 'fake title')->first();
        $this->assertEquals($lesson->id, $result->id);
    }
}
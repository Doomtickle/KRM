<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ReadNotesTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();

        $this->user   = create('App\User');
        $this->client = create('App\Client');
        $this->task   = create('App\Task', [
            'assigned_to' => $this->user->id,
            'created_by'  => $this->user->id
        ]);
    }
    /** @test */
    public function an_unauthenticated_user_cannot_view_a_note()
    {
        $this->withExceptionHandling()->get('/task/1/notes')
            ->assertRedirect('/login');
    }

    /** @test */
    public function an_authenticated_user_can_view_a_note()
    {
        $this->signIn();

        $note = $this->createNote();

        $this->get("/task/{$this->task->id}/notes")
                ->assertJson([
                    ['body' => $note->body]
                ]);

    }

    protected function makeNote()
    {
        $note = make('App\Note', [
            'user_id'   => $this->user->id,
            'client_id' => $this->client->id,
            'task_id'   => $this->task->id
        ]);

        return $note;
    }

    protected function createNote()
    {
        $note = create('App\Note', [
            'user_id'   => $this->user->id,
            'client_id' => $this->client->id,
            'task_id'   => $this->task->id
        ]);

        return $note;
    }
}
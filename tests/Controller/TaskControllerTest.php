<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Enum\Status;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelBrowser;

class TaskControllerTest extends WebTestCase
{
    private HttpKernelBrowser $client;
    private EntityManagerInterface $entityManager;
    private TaskRepository $taskRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();

        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->taskRepository = static::getContainer()->get(TaskRepository::class);

        $this->entityManager->getConnection()->executeQuery('DELETE FROM tasks');
    }

    public function testGetAllTasks(): void
    {
        $task = new Task();
        $task->setTitle('Test Task');
        $task->setDescription('Test Description');
        $task->setStatus(Status::NEW);
        $task->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($task);
        $this->entityManager->flush();

        $this->client->request('GET', '/api/tasks/');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseContent = $this->client->getResponse()->getContent();
        $this->assertJson($responseContent);

        $responseData = json_decode($responseContent, true);
        $this->assertCount(1, $responseData);
        $this->assertEquals('Test Task', $responseData[0]['title']);
    }

    public function testGetTaskById(): void
    {
        $task = new Task();
        $task->setTitle('Test Task');
        $task->setDescription('Test Description');
        $task->setStatus(Status::NEW);
        $task->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($task);
        $this->entityManager->flush();

        $this->client->request('GET', '/api/tasks/' . $task->getId());

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseContent = $this->client->getResponse()->getContent();
        $this->assertJson($responseContent);

        $responseData = json_decode($responseContent, true);
        $this->assertEquals('Test Task', $responseData['title']);
    }

    public function testCreateTask(): void
    {
        $this->client->request(
            'POST',
            '/api/tasks/',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'title' => 'New Task',
                'description' => 'New Description',
                'status' => Status::NEW,
            ])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $responseContent = $this->client->getResponse()->getContent();
        $this->assertJson($responseContent);

        $responseData = json_decode($responseContent, true);
        $this->assertEquals('New Task', $responseData['title']);
        $this->assertEquals('New Description', $responseData['description']);
    }

    public function testUpdateTask(): void
    {
        $task = new Task();
        $task->setTitle('Old Title');
        $task->setDescription('Old Description');
        $task->setStatus(Status::NEW);
        $task->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($task);
        $this->entityManager->flush();

        $this->client->request(
            'PUT',
            '/api/tasks/' . $task->getId(),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'title' => 'Updated Title',
                'description' => 'Updated Description',
                'status' => Status::IN_PROGRESS,
            ])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $responseContent = $this->client->getResponse()->getContent();
        $this->assertJson($responseContent);

        $responseData = json_decode($responseContent, true);
        $this->assertEquals('Updated Title', $responseData['title']);
        $this->assertEquals('Updated Description', $responseData['description']);
        $this->assertEquals('in_progress', $responseData['status']);
    }

    public function testDeleteTask(): void
    {
        $task = new Task();
        $task->setTitle('Task to Delete');
        $task->setDescription('Description');
        $task->setStatus(Status::NEW);
        $task->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($task);
        $this->entityManager->flush();

        $this->client->request('DELETE', '/api/tasks/' . $task->getId());

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        $deletedTask = $this->taskRepository->find($task->getId());
        $this->assertNull($deletedTask);
    }
}

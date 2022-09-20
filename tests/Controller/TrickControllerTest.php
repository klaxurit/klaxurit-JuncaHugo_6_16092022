<?php

namespace App\Test\Controller;

use App\Entity\Trick;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TrickControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private TrickRepository $repository;
    private string $path = '/trick/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = (static::getContainer()->get('doctrine'))->getRepository(Trick::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Trick index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'trick[name]' => 'Testing',
            'trick[description]' => 'Testing',
            'trick[picture]' => 'Testing',
            'trick[video]' => 'Testing',
            'trick[picture_alt]' => 'Testing',
            'trick[slug]' => 'Testing',
            'trick[createdAt]' => 'Testing',
            'trick[groups]' => 'Testing',
        ]);

        self::assertResponseRedirects('/trick/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Trick();
        $fixture->setName('My Title');
        $fixture->setDescription('My Title');
        $fixture->setPicture('My Title');
        $fixture->setVideo('My Title');
        $fixture->setPicture_alt('My Title');
        $fixture->setSlug('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setGroups('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Trick');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Trick();
        $fixture->setName('My Title');
        $fixture->setDescription('My Title');
        $fixture->setPicture('My Title');
        $fixture->setVideo('My Title');
        $fixture->setPicture_alt('My Title');
        $fixture->setSlug('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setGroups('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'trick[name]' => 'Something New',
            'trick[description]' => 'Something New',
            'trick[picture]' => 'Something New',
            'trick[video]' => 'Something New',
            'trick[picture_alt]' => 'Something New',
            'trick[slug]' => 'Something New',
            'trick[createdAt]' => 'Something New',
            'trick[groups]' => 'Something New',
        ]);

        self::assertResponseRedirects('/trick/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getName());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getPicture());
        self::assertSame('Something New', $fixture[0]->getVideo());
        self::assertSame('Something New', $fixture[0]->getPicture_alt());
        self::assertSame('Something New', $fixture[0]->getSlug());
        self::assertSame('Something New', $fixture[0]->getCreatedAt());
        self::assertSame('Something New', $fixture[0]->getGroups());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Trick();
        $fixture->setName('My Title');
        $fixture->setDescription('My Title');
        $fixture->setPicture('My Title');
        $fixture->setVideo('My Title');
        $fixture->setPicture_alt('My Title');
        $fixture->setSlug('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setGroups('My Title');

        $this->repository->add($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/trick/');
    }
}

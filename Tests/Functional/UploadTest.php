<?php

declare(strict_types=1);

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Max107\Bundle\UploadBundle\Tests\Functional;

use Max107\Bundle\UploadBundle\Form\Type\FileEvent;
use Symfony\Component\Filesystem\Filesystem;

class UploadTest extends WebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $fs = new Filesystem();
        $client = static::createClient();
        $fs->remove($this->getUploadsDir($client));
    }

    public function testFileIsUploadedWithFileType(): void
    {
        $client = static::createClient();
        $this->loadFixtures($client);

        $crawler = $client->request('GET', '/upload/vich_file');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $form = $crawler->selectButton('form_save')->form();
        $image = $this->getUploadedFile($client, 'symfony_black_03.png');

        $client->submit($form, ['form' => ['imageFile' => $image]]);

        // we should be redirected to the "view" page
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertFileExists($this->getUploadsDir($client) . '/sy/dd6526671de5b2e633f7b97a91a437bb.png', 'The file is uploaded');

        // test the delete feature
        $this->assertCount(1, $crawler->filter('input[type=checkbox]'), 'the delete checkbox is here');
        $form = $crawler->selectButton('form_save')->form();
        $client->submit($form, ['form' => ['imageFile' => FileEvent::CLEAR_VALUE]]);
        $this->assertTrue($client->getResponse()->isRedirect());
        $this->assertFileNotExists($this->getUploadsDir($client) . '/sy/dd6526671de5b2e633f7b97a91a437bb.png', 'The file is deleted');
    }

    public function testFileIsUploadedAndRemoved(): void
    {
        $client = static::createClient();
        $this->loadFixtures($client);

        $crawler = $client->request('GET', '/upload/vich_file');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $form = $crawler->selectButton('form_save')->form();
        $image = $this->getUploadedFile($client, 'symfony_black_03.png');

        $client->submit($form, ['form' => ['imageFile' => $image]]);

        // we should be redirected to the "view" page
        $this->assertTrue($client->getResponse()->isRedirect());
        $client->followRedirect();

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertFileExists($this->getUploadsDir($client) . '/sy/dd6526671de5b2e633f7b97a91a437bb.png', 'The file is uploaded');

        $client->request('POST', '/upload/remove/1');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertFileNotExists($this->getUploadsDir($client) . '/sy/dd6526671de5b2e633f7b97a91a437bb.png', 'The file is deleted');
    }

    public function testFileIsUploadedWithSameName(): void
    {
        $client = static::createClient();
        $this->loadFixtures($client);

        $fs = new Filesystem();
        $fs->mkdir($this->getUploadsDir($client) . '/sy');
        file_put_contents($this->getUploadsDir($client) . '/sy/dd6526671de5b2e633f7b97a91a437bb.png', '123');

        $crawler = $client->request('GET', '/upload/vich_file');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $form = $crawler->selectButton('form_save')->form();
        $image = $this->getUploadedFile($client, 'symfony_black_03.png');

        $client->submit($form, ['form' => ['imageFile' => $image]]);

        // we should be redirected to the "view" page
        $this->assertTrue($client->getResponse()->isRedirect());
        $client->followRedirect();

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertFileExists($this->getUploadsDir($client) . '/sy/dd6526671de5b2e633f7b97a91a437bb_1.png', 'The file is uploaded');
    }

    public function testFileIsUploadedWithImageType(): void
    {
        $client = static::createClient();
        $this->loadFixtures($client);

        $crawler = $client->request('GET', '/upload/vich_image');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $form = $crawler->selectButton('form_save')->form();
        $image = $this->getUploadedFile($client, 'symfony_black_03.png');

        $client->submit($form, ['form' => ['imageFile' => $image]]);

        // we should be redirected to the "view" page
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertFileExists($this->getUploadsDir($client) . '/sy/dd6526671de5b2e633f7b97a91a437bb.png', 'The file is uploaded');

        // test the delete feature
        $this->assertCount(1, $crawler->filter('input[type=checkbox]'), 'the delete checkbox is here');
        $form = $crawler->selectButton('form_save')->form();
        $crawler = $client->submit($form, [
            'form' => [
                'imageFile' => FileEvent::CLEAR_VALUE,
            ],
        ]);
        $this->assertTrue($client->getResponse()->isRedirect());
        $this->assertFileNotExists($this->getUploadsDir($client) . '/sy/dd6526671de5b2e633f7b97a91a437bb.png', 'The file is deleted');
    }
}

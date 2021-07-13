<?php

declare(strict_types=1);

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Max107\Bundle\UploadBundle\Tests\Functional;

use Max107\Bundle\UploadBundle\Form\Type\FileEvent;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\Filesystem\Filesystem;

class UploadTest extends WebTestCase
{
    protected KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        (new Filesystem())->remove($this->getUploadsDir());
        $this->loadFixtures();
    }

    public function testFileIsUploadedWithFileType(): void
    {
        $crawler = $this->client->request('GET', '/upload/vich_file');
        $this->assertTrue(
            $this->client->getResponse()->isSuccessful(),
            $this->client->getResponse()->getContent()
        );

        $form = $crawler->selectButton('form_save')->form();
        $image = $this->getUploadedFile($this->client, 'symfony_black_03.png');

        $this->client->submit($form, ['form' => ['imageFile' => $image]]);

        // we should be redirected to the "view" page
        $this->assertTrue(
            $this->client->getResponse()->isRedirect(),
            $this->client->getResponse()->getContent()
        );
        $crawler = $this->client->followRedirect();

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertFileExists(
            $this->getUploadsDir() . '/sy/dd6526671de5b2e633f7b97a91a437bb.png',
            'The file is uploaded'
        );

        // test the delete feature
        $this->assertCount(
            1,
            $crawler->filter('input[type=checkbox]'),
            'the delete checkbox is here'
        );
        $form = $crawler->selectButton('form_save')->form();
        $this->client->submit($form, ['form' => ['imageFile' => FileEvent::CLEAR_VALUE]]);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->assertFileNotExists(
            $this->getUploadsDir() . '/sy/dd6526671de5b2e633f7b97a91a437bb.png',
            'The file is deleted'
        );
    }

    public function testFileIsUploadedAndRemoved(): void
    {
        $crawler = $this->client->request('GET', '/upload/vich_file');
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $form = $crawler->selectButton('form_save')->form();
        $image = $this->getUploadedFile($this->client, 'symfony_black_03.png');

        $this->client->submit($form, ['form' => ['imageFile' => $image]]);

        // we should be redirected to the "view" page
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->client->followRedirect();

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertFileExists(
            $this->getUploadsDir() . '/sy/dd6526671de5b2e633f7b97a91a437bb.png',
            'The file is uploaded'
        );

        $this->client->request('POST', '/upload/remove/1');
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertFileNotExists(
            $this->getUploadsDir() . '/sy/dd6526671de5b2e633f7b97a91a437bb.png',
            'The file is deleted'
        );
    }

    public function testFileIsUploadedWithSameName(): void
    {
        $fs = new Filesystem();
        $fs->mkdir($this->getUploadsDir() . '/sy');
        file_put_contents(
            $this->getUploadsDir() . '/sy/dd6526671de5b2e633f7b97a91a437bb.png',
            '123'
        );

        $crawler = $this->client->request('GET', '/upload/vich_file');
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $form = $crawler->selectButton('form_save')->form();
        $image = $this->getUploadedFile($this->client, 'symfony_black_03.png');

        $this->client->submit($form, ['form' => ['imageFile' => $image]]);

        // we should be redirected to the "view" page
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->client->followRedirect();

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertFileExists(
            $this->getUploadsDir() . '/sy/dd6526671de5b2e633f7b97a91a437bb_1.png',
            'The file is uploaded'
        );
    }

    public function testFileIsUploadedWithImageType(): void
    {
        $crawler = $this->client->request('GET', '/upload/vich_image');
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $form = $crawler->selectButton('form_save')->form();
        $image = $this->getUploadedFile($this->client, 'symfony_black_03.png');

        $this->client->submit($form, ['form' => ['imageFile' => $image]]);

        // we should be redirected to the "view" page
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $crawler = $this->client->followRedirect();

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertFileExists(
            $this->getUploadsDir() . '/sy/dd6526671de5b2e633f7b97a91a437bb.png',
            'The file is uploaded'
        );

        // test the delete feature
        $this->assertCount(1, $crawler->filter('input[type=checkbox]'), 'the delete checkbox is here');
        $form = $crawler->selectButton('form_save')->form();
        $this->client->submit($form, ['form' => ['imageFile' => FileEvent::CLEAR_VALUE]]);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->assertFileNotExists(
            $this->getUploadsDir() . '/sy/dd6526671de5b2e633f7b97a91a437bb.png',
            'The file is deleted'
        );
    }
}

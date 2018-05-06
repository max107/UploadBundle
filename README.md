[![Build Status](https://travis-ci.org/max107/UploadBundle.svg?branch=master)](https://travis-ci.org/max107/UploadBundle)
[![codecov](https://codecov.io/gh/max107/UploadBundle/branch/master/graph/badge.svg)](https://codecov.io/gh/max107/UploadBundle)

# Описание

Бандл основан на [VichUploaderBundle](https://github.com/dustin10/VichUploaderBundle). Отличительной особенностью бандла 
является простота API за счет работы только с [flysystem](https://github.com/thephpleague/flysystem), а так же 
обязательное сохранение относительного пути до файла.

Бандл так же предоставляет 2 поля для форм с превью загруженного файла.

# Установка

```
composer require max107/upload-bundle
```

# Настройка

```
use Max107\Bundle\UploadBundle\Upload\Annotation as Upload;

// @Upload\Uploadable для сущности и 
// @Upload\UploadableField(filesystem="default", path="image") для поля
```

где `filesystem` это примонтированная файловая система в `flysystem` и `path` это маппинг переменной куда сохранять
относительный путь до сохраненного файла. Так же поддерживаются основные параметры из 
VichUploadBundle: `name`, `size`, `mimeType`, `originalName`, `dimensions`

# Пример

```php
<?php

use Doctrine\ORM\Mapping as ORM;
use Max107\Bundle\UploadBundle\Upload\Annotation as Upload;

/**
 * @ORM\Entity
 * @Upload\Uploadable
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated_at;

    /**
     * @Upload\UploadableField(filesystem="default", path="image")
     *
     * @var File
     */
    protected $image_file;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     */
    protected $image;

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param \SplFileInfo $image
     *
     * @throws \Exception
     */
    public function setImageFile(\SplFileInfo $image = null)
    {
        $this->image_file = $image;

        if (null !== $image) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updated_at = new \DateTime();
        }
    }

    public function getImageFile(): ?\SplFileInfo
    {
        return $this->image_file;
    }
}
```

# Установка файла вручную

```php
<?php

use Symfony\Component\HttpFoundation\File\File;

$product = new Product;
$product->setName('beer');
$product->setImageFile(new File(__DIR__.'/images/beer.png'));
```

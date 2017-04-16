# FSiAdminPositionableBundle

This bundle provides two main features

1. Simple way to change position of item on the list gradually. This mean that position will be changed by 1.
   This requires [Sortable behavior extension for Doctrine2](https://github.com/Atlantic18/DoctrineExtensions/blob/master/doc/sortable.md)
2. Simple way to change order of collection items in conjunction with some JS to change their order in the DOM 

## Installation

Add to `AppKernel.php`:

```php
new FSi\Bundle\AdminPositionableBundle\FSiAdminPositionableBundle(),
```

Add routes to `/app/config/routing.yml`:

```yml
_fsi_positionable:
    resource: "@FSiAdminPositionableBundle/Resources/config/routing/positionable.xml"
    prefix:   /admin
```

## Sortable entity:

**Note:** **@Gedmo\Mapping\Annotation\SortablePosition** points column to store **position** index

```php
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use FSi\Bundle\AdminPositionableBundle\Model\PositionableInterface;

class Promotion implements PositionableInterface
{
    /**
     * @Gedmo\SortablePosition
     * @ORM\Column(type="integer")
     */
    protected $position;

    /**
     * {@inheritdoc}
     */
    public function increasePosition()
    {
        $this->position++;
    }

    /**
     * {@inheritdoc}
     */
    public function decreasePosition()
    {
        $this->position--;
    }
}
```

**Note:** that `PositionableInterface` implementation is required

Sample datagrid definition:

```yml
columns:
  title:
    type: text
    options:
      display_order: 1
      label: "backend.promotions.datagrid.title.label"
  actions:
    type: action
    options:
      display_order: 2
      label: "backend.promotions.datagrid.actions.label"
      field_mapping: [ id ]
      actions:
        move_up:
          route_name: "fsi_admin_positionable_decrease_position"
          additional_parameters: { element: "promotions" }
          parameters_field_mapping: { id: id }
          content: <span class="glyphicon glyphicon-arrow-up icon-white"></span>
          url_attr: { class: "btn btn-warning btn-sm" }
        move_down:
          route_name: "fsi_admin_positionable_increase_position"
          additional_parameters: { element: "promotions" }
          parameters_field_mapping: { id: id }
          content: <span class="glyphicon glyphicon-arrow-down icon-white"></span>
          url_attr: { class: "btn btn-warning btn-sm" }
```

## Sortable collection:

Assume you have some entity with one-to-many association **ordered by position field**:

```php
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Gallery
{
    /**
     * @ORM\OneToMany(targetEntity="GalleryPhoto", mappedBy="gallery")
     * @ORM\OrderBy({"position" = "ASC"})
     * @var GalleryPhoto[]|ArrayCollection
     */
    private $photos;

    public function __construct()
    {
        $this->photos = new ArrayCollection();
    }

    /**
     * @param GalleryPhoto $photo
     */
    public function addPhoto(GalleryPhoto $photo)
    {
        if (!$this->photos->contains($photo)) {
            $photo->setGallery($this);
            $this->photos->add($photo);
        }
    }

    /**
     * @param GalleryPhoto $photo
     */
    public function removePhoto(GalleryPhoto $photo)
    {
        $this->photos->removeElement($photo);
    }

    /**
     * @return GalleryPhoto[]
     */
    public function getPhotos()
    {
        return $this->photos;
    }
}
```

And you have collection item class that **implements ``FSi\Bundle\AdminPositionableBundle\Model\SortableInterface``**

```php
use Doctrine\ORM\Mapping as ORM;
use FSi\Bundle\AdminPositionableBundle\Model\SortableInterface;

/**
 * @ORM\Entity
 */
class GalleryPhoto implements SortableInterface
{
    /**
     * @ORM\ManyToOne(targetEntity="Gallery", inversedBy="photos")
     * @var Gallery
     */
    private $gallery;

    /**
     * @ORM\Column(name="position", type="integer", nullable=false)
     * @var int
     */
    private $position;

    /**
     * @return Gallery
     */
    public function getGallery()
    {
        return $this->gallery;
    }

    /**
     * @param Gallery $gallery
     */
    public function setGallery(Gallery $gallery)
    {
        $this->gallery = $gallery;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }
}
```

And you define the form like this

```php
use AppBundle\Entity\Gallery;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class GalleryType extends AbstractType
{
    public function getName()
    {
        return 'gallery';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('photos', 'collection', array(
            'type' => new GalleryPhotoType(),
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
        ));
    }
}
```

When the order of photos is changed in the DOM (i.e. using some JS) then the ``$position`` in ``GalleryPhoto`` entities
will be updated accordingly.

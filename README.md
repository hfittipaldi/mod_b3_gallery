# B3 Gallery Module
**B3 Gallery Module is a module for Joomla 3.6+ which displays a gallery of images and a modal carousel.**

This is compatible with Bootstrap 3.x and 4.x

The gallery items width should be set on your template css file. Example (SASS):
```
.b3Gallery-item {
    flex-basis: calc(50% - 1rem);
    @media (min-width: 768px) {
        flex-basis: calc(100% / 3 - 1rem);
    }
    @media (min-width: 992px) {
        flex-basis: calc(25% - 1rem);
    }
}
```

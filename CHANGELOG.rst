CHANGELOG
=====

0.3.0
-----

* Bugfix `#24`_: getRawData() does not return raw EXIF data
* Bugfix `#18`_: Create CHANGELOG
* Separated ``Adapter`` & ``Reader`` classes
* Created ``ReaderInterface`` class
* BC-break `#15`_: Exiftool adapter: add -n switch to exiftool call 
* Composer.json: added semver version for phpmd; removed pdepend
* added ``Orientation``, ``MimeType``, ``FileSize`` and ``ColorSpace`` options to EXIF

.. _`#24`: https://github.com/Miljar/php-exif/issues/24
.. _`#18`: https://github.com/Miljar/php-exif/issues/18
.. _`#15`: https://github.com/Miljar/php-exif/issues/15

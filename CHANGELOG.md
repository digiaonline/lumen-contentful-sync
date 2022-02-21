# Change log

## 4.2.0

* use proper "DeletedEntry" type to handle unpublish webhook call

## 4.1.0

* relax version constraints enough to work on Lumen 6.x

## 4.0.0

* use v4.x of the Contentful SDK

## 2.0.0

* fix some code analysis issues
* use v2.x of the Contentful SDK

## 1.2.0

* add support for specifying the batch size in console commands (default is still 100)

## 1.1.0

* add support for synchronizing multiple specific content types at once
* run static analysis using phpstan

## 1.0.3

* provide a default getTotalQuery() implementation, removes one abstract method

## 1.0.2

* fix counters in console commands not being reset
* made some private methods protected so they can be overriden if necessary

## 1.0.1

* add some more documentation
* fix usage of "skip" and "numSynchronized"

## 1.0.0

* initial version

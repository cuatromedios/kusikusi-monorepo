# Kusikusi

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

Kusikusi is both a CMS and a collection of Laravel packages for the creation of applications based on hierarchical data, like the data found on most websites. We are not reinventing the wheel, Kusikusi is built on top of Laravel framework, so it uses all well proven packages like Eloquent ORM and can be deployed on almost any hosting provider using PHP and MySQL. KusiKusi has its own way to organize models and its relations, mainly tree based relations.

Kusikusi or its packages can be used as:
* A full CMS
* Headless CMS
* An API first application
* Backend for web applications
* Backend of mobile applications

## Installation

> Please note Kusikusi is based in [Laravel Framework](https://www.laravel.com/), so it is reccomended if you have previous experience working with it.

Kusikusi can be installed as CMS for brand new Laravel application, or you can use its packages in a existing one.

#### Kusikusi packages

The individual packages or components can also be used in existing Laravel applications:

* Models
* Website
* API Controller
* Admin Interfaces

##### Splitting the packages
As we are using a monorepo, the packages must be splitted to a separate repository, we use [splitsh/lite](https://github.com/splitsh/lite) to do so. There are some shell commands in the repo to automatize this, they make the split and commits to a specific branch, for example:

```
./splitmodels.sh
```

Will take the contnet of packages/models split it and send the changes to the branch `models`. The branch can be pushed to the specific remote in Github: `cuatromedios/kusikusi-models`

##License

Kusikusi is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

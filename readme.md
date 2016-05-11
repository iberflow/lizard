<p align="center">
<img src="https://iber.lt/assets/images/projects/lizard.png" width="400" alt="Laravel Lizard" />
</p>
## Laravel Lizard

Laravel project dependency manager and wizard.

**NOTE:** This is an alpha release. This project is still in testing phase therefore some things might not work as expected. Do not try it on existing projects.

> This package allows you to easily add Laravel composer packages along with service providers and facades into your Laravel projects.

Currently available composer packages:

- barryvdh/laravel-debugbar
- barryvdh/laravel-ide-helper
- laravelcollective/html
- themsaid/laravel-langman
- doctrine/dbal
- intervention/image
- patricktalmadge/bootstrapper
- cviebrock/eloquent-sluggable
- laracasts/flash

In addition it also allows you to setup bower and gulp files.

#### Installation
Install globally via composer:

`composer global require "ignasbernotas/lizard=*"`

#### Usage

To start lizard, run:
`lizard init <project directory>`

#### Roadmap
[Roadmap](roadmap.md)

#### Contributors

Artwork by [Justas Galaburda](https://dribbble.com/jucha)

#### License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

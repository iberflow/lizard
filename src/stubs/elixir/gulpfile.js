// disable annoying notifications for gulp tasks
process.env.DISABLE_NOTIFIER = true;

var assets = {
    app: {
        js: [],
        sass: 'app',
        file: 'app'
    },
    vendor: {
        js: [],
        css: [],
        copy: [],
        file: 'vendor'
    }
};

var directories = {
    resources: {
        base: 'resources/assets/',
        vendor: 'vendor/',
        appJs: 'js/'
    },
    public: {
        base: 'public/',
        css: 'css/',
        js: 'js/'
    }
};

assets.vendor.js = [
    // 'bootstrap/dist/js/bootstrap.js'
];
assets.vendor.css = [
    // 'bootstrap/dist/css/bootstrap.css'
];

var elixir = require('laravel-elixir');

elixir(function (mix) {
    // compiles main SCSS file
    mix.sass(assets.app.sass + '.scss');

    // compile app JS files
    mix.styles(
        assets.app.js,
        directories.public.base + directories.public.js + assets.app.file + '.js',
        directories.resources.base + directories.resources.vendor
    );

    // compile vendor CSS files
    mix.styles(
        assets.vendor.css,
        directories.public.base + directories.public.css + assets.vendor.file + '.css',
        directories.resources.base + directories.resources.vendor
    );

    // compile vendor JS files 
    mix.scripts(
        assets.vendor.js,
        directories.public.base + directories.public.js + assets.vendor.file + '.js',
        directories.resources.base + directories.resources.vendor
    );

    // version files
    mix.version([
        // app files
        directories.public.base + directories.public.js + assets.app.file + '.js',
        directories.public.base + directories.public.css + assets.app.sass + '.css',
        
        // vendor files
        directories.public.base + directories.public.js + assets.vendor.file + '.js',
        directories.public.base + directories.public.css + assets.vendor.file + '.css'
    ]);
});

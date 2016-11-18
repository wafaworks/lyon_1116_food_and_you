// Include Gulp and all required plugins

var gulp = require('gulp');
var autoprefixer = require('gulp-autoprefixer');
var concat = require('gulp-concat');
var del = require('del');
var exec = require('child_process').exec;
var less = require('gulp-less');
var minifyCss = require('gulp-minify-css');
var sass = require('gulp-sass');
var uglify = require('gulp-uglify');
var rewriteCSS = require('gulp-rewrite-css');

var paths = {
    watch_less: [
        './web/source/less/*.less',
        './web/source/less/**/*.less'
    ],
    less: [
        './web/source/less/app.less'
    ],
    js: [
        './web/source/js/*.js',
        './web/source/js/**/*.js',
        './web/vendor/app-routing.js'
    ],
    dist: {
        css: './web/css/',
        js: './web/js/'
    },
    vendor: {
        js: [
            './web/vendor/tether/dist/js/tether.min.js',
            './web/vendor/bootstrap/js/transition.js',
            './web/vendor/bootstrap/js/collapse.js',
            './web/vendor/bootstrap/dist/js/bootstrap.min.js',
            './web/vendor/bootstrap/dist/js/carousel.min.js',
            './web/vendor/jquery-validation/dist/jquery.validate.min.js',
            './web/vendor/jquery-validation/src/localization/messages_fr.js',
            './vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.js',
            './web/vendor/routes.js',
            './web/vendor/moment/moment.js',
            './web/vendor/moment/locale/fr.js',
            './web/vendor/eonasdan-bootstrap-datetimepicker/src/js/bootstrap-datetimepicker.js',
            './web/vendor/jquery.ui/ui/core.js',
            './web/vendor/jquery.ui/ui/widget.js',
            './web/vendor/jquery.ui/ui/spinner.js',
            './web/vendor/jquery.ui/ui/button.js',
            './web/vendor/smooth-scroll/smooth-scroll.min.js',
            './web/vendor/ion.rangeslider/js/ion.rangeSlider.min.js',
            './web/vendor/jquery-mask-plugin/dist/jquery.mask.min.js',
            './web/vendor/blueimp-load-image/js/load-image.all.min.js',
            './web/vendor/jquery.ui/ui/datepicker.js',
            './web/vendor/jquery.ui/ui/i18n/datepicker-fr.js',
        ],
        css: [
            './web/vendor/font-awesome/css/font-awesome.min.css',
            './web/vendor/select2/select2.css',
            './web/vendor/select2/select2-bootstrap.css',
            './web/vendor/ion.rangeslider/css/ion.rangeSlider.css',
            './web/vendor/ion.rangeslider/css/ion.rangeSlider.skinNice.css',
            './web/vendor/jquery.ui/themes/base/core.css',
            './web/vendor/jquery.ui/themes/base/datepicker.css'
        ]
    }
};

gulp.task('clean', function () {
    return del([
        paths.dist.css + '*',
        paths.dist.js + '*'
    ]);
});

gulp.task('dump-routes', function () {
    exec('php app/console fos:js-routing:dump  --target="web/vendor/routes.js"', function (err, stdout, stderr) {
        console.log(stderr);
    });
});

gulp.task('less', function() {
    gulp.src(paths.less)
        .pipe(concat('01-app.css'))
        .pipe(less())
        .pipe(autoprefixer('last 2 versions', 'ie 11'))
        .pipe(minifyCss())
        .pipe(gulp.dest(paths.dist.css));
});

gulp.task('js', function() {
    gulp.src(paths.js)
        .pipe(concat('01-app.js'))
        //.pipe(uglify({mangle: false}))
        .pipe(gulp.dest(paths.dist.js));
});

gulp.task('watch-assets', function() {
    gulp.watch(paths.watch_less, ['less']);
    console.log('Watching directory:' + paths.less.join(', '));

    gulp.watch(paths.js, ['js']);
    console.log('Watching directory:' + paths.js.join(', '));
});

gulp.task('vendor', function() {
    gulp.src(paths.vendor.css)
        .pipe(rewriteCSS({destination:paths.dist.css}))
        .pipe(concat('00-vendor.css'))
        .pipe(minifyCss())
        .pipe(gulp.dest(paths.dist.css));

    gulp.src(paths.vendor.js)
        .pipe(concat('00-vendor.js'))
        .pipe(uglify({mangle: false}))
        .pipe(gulp.dest(paths.dist.js));
});

gulp.task('build', ['clean', 'dump-routes', 'vendor', 'less', 'js']);
gulp.task('watch', ['clean', 'dump-routes', 'vendor', 'less', 'js', 'watch-assets']);

gulp.task('default', ['build']);

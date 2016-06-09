var gulp    = require('gulp');
var phpunit = require('gulp-phpunit');
var phplint = require('gulp-phplint');
var watch = require('gulp-watch');
var notifier = require('node-notifier');
var debug = require('gulp-debug');

var map = ['src/**/*.php', 'tests/**/*.php'];

gulp.task('dev', function(cb) {
    var options = {
      debug:             true,
      statusLine:        true,
      configurationFile: './build/phpunit.xml'
    };

    watch(map, function() {
        gulp
            .src(map)
            .pipe(phplint(''))
            .pipe(phplint.reporter(function (file) {
                var report = file.phplintReport || {};
                if (report.error) {
                    /*notifier.notify({
                        'title': 'PhpLint',
                        'Message': 'Lint failed'
                    });*/
                }
            }))
            .pipe(phpunit('./vendor/bin/phpunit', options))

            .on('end', cb);
    })

});

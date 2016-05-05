var gulp    = require('gulp');
var phpunit = require('gulp-phpunit');
var watch = require('gulp-watch');

gulp.task('phpunit', function(cb) {
    var options = {
      debug:             true,
      statusLine:        true,
      configurationFile: './build/phpunit.xml'
    };
    watch('src/*.php', function() {

        gulp.src('src/*.php').pipe(phpunit('./vendor/bin/phpunit', options)).on('end', cb);
    })

});

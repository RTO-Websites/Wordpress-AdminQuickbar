

var gulp = require('gulp'),
  util = require("gulp-util"),//https://github.com/gulpjs/gulp-util
  sass = require('gulp-sass')(require('sass')),//https://www.npmjs.org/package/gulp-sass
  minifycss = require('gulp-clean-css'),//https://github.com/scniro/gulp-clean-css
  rename = require('gulp-rename'),//https://www.npmjs.org/package/gulp-rename
  concat = require('gulp-concat'),
  uglify = require('gulp-terser');


gulp.task('default', function(done) {
  // Admin
  var sassAdminFiles = ['Admin/scss/*.*'],
    jsFiles = ['Admin/js/src/partials/**/*.js', 'Admin/js/src/main.js'],
    jsDest = 'Admin/js';

  gulp.src(sassAdminFiles)
    .pipe(sass({style: 'expanded'}))
    .pipe(rename({suffix: '.min'}))
    .pipe(minifycss())
    .pipe(gulp.dest('Admin/css'))

  gulp.src(jsFiles)
    .pipe(concat('build.min.js'))
    .pipe(uglify().on('error', util.log))
    .pipe(gulp.dest(jsDest));

  done();
});

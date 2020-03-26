var gulp = require('gulp'),
  util = require("gulp-util"),//https://github.com/gulpjs/gulp-util
  sass = require("gulp-sass"),//https://www.npmjs.org/package/gulp-sass
  autoprefixer = require('gulp-autoprefixer'),//https://www.npmjs.org/package/gulp-autoprefixer
  minifycss = require('gulp-clean-css'),//https://github.com/scniro/gulp-clean-css
  rename = require('gulp-rename'),//https://www.npmjs.org/package/gulp-rename
  concat = require('gulp-concat'),
  uglify = require('gulp-terser');

gulp.task('default', function() {
  // Admin
  var sassAdminFiles = ['Admin/scss/*.*'],
    jsFiles = 'Admin/js/src/**/*.js',
    jsDest = 'Admin/js';

  gulp.src(sassAdminFiles)
    .pipe(sass({style: 'expanded'}))
    .pipe(autoprefixer("last 3 version", "safari 5", "ie 8", "ie 9"))
    .pipe(gulp.dest("Admin/css"))
    .pipe(rename({suffix: '.min'}))
    .pipe(minifycss())
    .pipe(gulp.dest('Admin/css'));

  gulp.src(jsFiles)
    .pipe(concat('build.js'))
    .pipe(gulp.dest(jsDest))
    .pipe(rename('build.min.js'))
    .pipe(uglify().on('error', util.log))
    .pipe(gulp.dest(jsDest));

});

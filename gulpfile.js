const gulp = require("gulp");
const sass = require("gulp-sass")(require("sass"));
const concat = require("gulp-concat");
const uglify = require("gulp-uglify");
const cleanCSS = require("gulp-clean-css");
const sourcemaps = require("gulp-sourcemaps");
const rename = require("gulp-rename");

// Paths
const paths = {
    scss: "src/scss/**/*.scss",
    js: "src/js/**/*.js",
    dist: "www/",
    bootstrapJS: "node_modules/bootstrap/dist/js/bootstrap.bundle.min.js",
    bootstrapIcons: "node_modules/bootstrap-icons/font/**/*"
};

// Compile SCSS → CSS
function styles() {
    return gulp.src("src/scss/style.scss")
        .pipe(sourcemaps.init())
        .pipe(sass().on("error", sass.logError))
        .pipe(cleanCSS())
        .pipe(rename({ suffix: ".min" }))
        .pipe(sourcemaps.write("."))
        .pipe(gulp.dest(paths.dist + "css"));
}

// Bundle JS (Bootstrap + Custom)
function scripts() {
    return gulp.src([paths.bootstrapJS, paths.js])
        .pipe(sourcemaps.init())
        .pipe(concat("bundle.min.js"))
        .pipe(uglify())
        .pipe(sourcemaps.write("."))
        .pipe(gulp.dest(paths.dist + "js"));
}

// Copy Bootstrap Icons
function icons() {
    return gulp.src(paths.bootstrapIcons)
        .pipe(gulp.dest(paths.dist + "icons"));
}

// Watch for changes
function watchFiles() {
    gulp.watch(paths.scss, styles);
    gulp.watch(paths.js, scripts);
}

// Define tasks
exports.styles = styles;
exports.scripts = scripts;
exports.icons = icons;
exports.watch = gulp.series(styles, scripts, icons, watchFiles);
exports.build = gulp.series(styles, scripts, icons);

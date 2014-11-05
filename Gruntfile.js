module.exports = function(grunt) {

    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        copy: {
            main: {
                files: [
                    {expand: true, src: ['assets/**'], dest: 'build/'},
                    {expand: true, src: ['views/**'], dest: 'build/'},
                    {expand: true, src: ['walkers/**'], dest: 'build/'},
                    {expand: false, src: ['readme.txt'], dest: 'build/readme.txt'},
                    {expand: false, src: ['screenshot-1.png'], dest: 'build/screenshot-1.png'},
                    {expand: false, src: ['screenshot-2.png'], dest: 'build/screenshot-2.png'},
                    {expand: false, src: ['screenshot-3.png'], dest: 'build/screenshot-3.png'},
                    {expand: false, src: ['screenshot-4.png'], dest: 'build/screenshot-4.png'},
                    {expand: false, src: ['submenu.php'], dest: 'build/submenu.php'},
                    {expand: false, src: ['SubmenuAdmin.php'], dest: 'build/SubmenuAdmin.php'},
                    {expand: false, src: ['SubmenuModel.php'], dest: 'build/SubmenuModel.php'}
                ]
            }
        },
        clean: ["build"],
        uglify: {
            my_target: {
                files: {
                    'build/assets/js/ajax.js': ['assets/js/ajax.js'],
                    'build/assets/js/main.js': ['assets/js/main.js']
                }
            }
        },
        cssmin: {
            my_target: {
                files: {
                    'build/assets/css/admin.css': ['assets/css/admin.css']
                }
            }
        }
    });

    // grunt modules
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');

    // Default task(s).
    grunt.registerTask('default', ['clean', 'copy', 'uglify', 'cssmin']);

};